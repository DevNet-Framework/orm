<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Tracking;

use DevNet\Entity\Metadata\EntityModel;

class EntityStateManager
{
    private EntityModel $model;
    public array $identityMap = [];

    public function __construct(EntityModel $model)
    {
        $this->model = $model;
    }

    public function getOrCreateEntry(object $entity): EntityEntry
    {
        $entityName = get_class($entity);
        $entityType = $this->model->getEntityType($entityName);
        $entry      = $this->getEntry($entity);
        if ($entry) {
            return $entry;
        }

        $entry = new EntityEntry($entity, $entityType);
        $this->addEntry($entry);
        return $entry;
    }

    public function addEntry(EntityEntry $entry): void
    {
        $entity = $entry->Entity;
        $entityHash = spl_object_hash($entity);
        $entityName = $entry->Metadata->Name;
        $this->identityMap[$entityName][$entityHash] = $entry;
    }

    public function getEntry(object|string $entity, ?string $keyValue = null): ?EntityEntry
    {
        if (is_string($entity)) {
            if (isset($this->identityMap[$entity])) {
                foreach ($this->identityMap[$entity] as $entry) {
                    $key = $entry->Metadata->PropertyKey;
                    if ($entry->Entity->$key == $keyValue) {
                        return $entry;
                    }
                }
            }
        }

        if (is_object($entity)) {
            $entityName = get_class($entity);
            $entityHash = spl_object_hash($entity);
            if (isset($this->identityMap[$entityName][$entityHash])) {
                return $this->identityMap[$entityName][$entityHash];
            }
        }

        return null;
    }

    public function getEntries(): array
    {
        return $this->identityMap;
    }

    public function clearEntries(): void
    {
        $this->identityMap = [];
    }
}
