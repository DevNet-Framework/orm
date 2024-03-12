<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Tracking;

use DevNet\ORM\Metadata\EntityModel;

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

    public function getEntry(object|string $entity, array $keyValues = []): ?EntityEntry
    {
        if (is_string($entity)) {
            if (isset($this->identityMap[$entity])) {
                foreach ($this->identityMap[$entity] as $entry) {
                    if (count($keyValues) > 0 && count($keyValues) == count($entry->Metadata->Keys)) {
                        switch (count($keyValues)) {
                            case 1:
                                $key = $entry->Metadata->Keys[0];
                                if ($entry->Entity->$key == $keyValues[0]) {
                                    return $entry;
                                }
                                break;
                            case 2:
                                $key1 = $entry->Metadata->Keys[0];
                                $key2 = $entry->Metadata->Keys[1];
                                if ($entry->Entity->$key1 == $keyValues[0] && $entry->Entity->$key2 == $keyValues[1]) {
                                    return $entry;
                                }
                                break;
                        }
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
