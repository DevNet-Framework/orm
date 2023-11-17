<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Internal;

use DevNet\System\Linq;
use DevNet\System\Linq\IQueryable;
use DevNet\Entity\Query\EntityQuery;
use DevNet\Entity\Metadata\EntityType;
use DevNet\Entity\Metadata\EntityNavigation;
use DevNet\Entity\Storage\EntityDatabase;
use DevNet\Entity\Tracking\EntityStateManager;

class EntityFinder
{
    private EntityDatabase $database;
    private EntityStateManager $entityStateManager;

    public function __construct(EntityDatabase $database)
    {
        $this->database = $database;
        $this->entityStateManager = $database->EntityStateManager;
    }

    public function find(EntityType $entityType, array $keyValues): ?object
    {
        $entry = $this->entityStateManager->getEntry($entityType->Name, $keyValues);
        if ($entry) {
            return $entry->Entity;
        }

        $entity = null;
        $query  = new EntityQuery($entityType, $this->database->QueryProvider);
        if (count($keyValues) > 0 && count($keyValues) == count($entityType->Keys)) {
            switch (count($keyValues)) {
                case 1:
                    $key = $entityType->Keys[0];
                    $keyValue = $keyValues[0];
                    $entity = $query->where(fn ($entity) => $entity->$key == $keyValue)->first();
                    break;
                case 2:
                    $key1 = $entityType->Keys[0];
                    $key2 = $entityType->Keys[1];
                    $keyValue1 = $keyValues[0];
                    $keyValue2 = $keyValues[1];
                    $entity = $query->where(fn ($entity) => $entity->$key1 == $keyValue1 && $entity->$key2 == $keyValue2)->first();
                    break;
            }
        }

        if ($entity) {
            $this->load($entity);
        }

        return $entity;
    }

    public function load(object $entity): void
    {
        $this->database->attach($entity);

        $entityType = $this->database->Model->getEntityType($entity::class);
        foreach ($entityType->Navigations as $navigation) {
            $navigation->PropertyInfo->setAccessible(true);
            if ($navigation->Cardinality == 2 && count($entityType->Keys) == 1) {
                $key = $entityType->Keys[0];
                $navigation->PropertyInfo->setValue($entity, new EntityCollection($navigation, $this->database, $entity->$key));
            } else if ($navigation->Cardinality == 1) {
                $foreignKey = $navigation->EntityType->getForeignKey($navigation->TargetEntity);
                if ($foreignKey) {
                    $ParentEntityType = $this->database->Model->getEntityType($navigation->TargetEntity);
                    $ParentEntity = $this->find($ParentEntityType, [$entity->$foreignKey]);
                    $navigation->PropertyInfo->setValue($entity, $ParentEntity);
                } else {
                    if (count($entityType->Keys) == 1) {
                        $key = $entityType->Keys[0];
                        $childEntity = $this->query($navigation, $entity->$key)->first();
                        if ($childEntity) {
                            $this->load($childEntity);
                            $navigation->PropertyInfo->setValue($entity, $childEntity);
                        }
                    }
                }
            }
        }
    }

    public function query(EntityNavigation $navigation, string $keyValue): IQueryable
    {
        $entityTypeTarget = $this->database->Model->getEntityType($navigation->TargetEntity);
        $foreignKey = $entityTypeTarget->getForeignKey($navigation->EntityType->Name);
        $query = new EntityQuery($entityTypeTarget, $this->database->QueryProvider);

        return $query->where(fn ($entity) => $entity->$foreignKey == $keyValue);
    }
}
