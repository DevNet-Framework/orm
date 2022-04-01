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

    public function find(EntityType $entityType, $id): ?object
    {
        $entry = $this->entityStateManager->getEntry($entityType->getName(), $id);
        if ($entry) {
            return $entry->Entity;
        }

        $query  = new EntityQuery($entityType, $this->database->QueryProvider);
        $key    = $entityType->getPrimaryKey();
        $entity = $query->where(fn ($entity) => $entity->$key == $id)->first();

        if ($entity) {
            $this->load($entity);
        }

        return $entity;
    }

    public function load(object $entity)
    {
        $this->database->attach($entity);

        $entityType = $this->database->Model->getEntityType(get_class($entity));
        foreach ($entityType->Navigations as $navigation) {
            $navigation->PropertyInfo->setAccessible(true);
            if ($navigation->NavigationType == 2) {
                $key = $entityType->getPrimaryKey();
                $navigation->PropertyInfo->setValue($entity, new EntityCollection($navigation, $this->database, $entity->$key));
            } else if ($navigation->NavigationType == 1) {
                $foreignKey = $navigation->Metadata->getForeignKey($navigation->MetadataReference->getName());
                if ($foreignKey) {
                    $ParentEntity = $this->find($navigation->MetadataReference, $entity->$foreignKey);
                    $navigation->PropertyInfo->setValue($entity, $ParentEntity);
                } else {
                    $key = $entityType->getPrimaryKey();
                    $childEntity = $this->query($navigation, $entity->$key)->first();
                    if ($childEntity) {
                        $navigation->PropertyInfo->setValue($entity, $childEntity);
                    }
                }
            }
        }
    }

    public function query(EntityNavigation $navigation, $keyValue): IQueryable
    {
        $query = new EntityQuery($navigation->MetadataReference, $this->database->QueryProvider);
        $foreignKey = $navigation->getForeignKey();

        return $query->where(fn ($entity) => $entity->$foreignKey == $keyValue);
    }
}
