<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Tracking;

use DevNet\Entity\Metadata\EntityType;
use DevNet\System\Exceptions\PropertyException;
use DateTime;

class EntityEntry
{
    private EntityType $metadata;
    private object $entity;
    private int $state;
    private array $values = [];

    public function __construct(object $entity, EntityType $entityType)
    {
        $this->entity   = $entity;
        $this->metadata = $entityType;
        $this->state    = EntityState::Attached;
    }

    public function __get(string $name)
    {
        if (in_array($name, ['Metadata', 'Entity', 'State'])) {
            $property = lcfirst($name);
            return $this->$property;
        }
        
        if ($name == "Values") {
            foreach ($this->metadata->Properties as $property) {
                $propertyName = $property->PropertyInfo->getName();
                if ($property->PropertyInfo->isInitialized($this->entity)) {
                    $value = $property->PropertyInfo->getValue($this->entity);
                    if ($value instanceof DateTime) {
                        $value = $value->format('y-m-d h:m:s');
                    }
                    $this->values[$propertyName] = $value;
                } else {
                    $this->values[$propertyName] = null;
                }
            }
        }

        if (property_exists($this, $name)) {
            throw new PropertyException("access to private property " . get_class($this) . "::" . $name);
        }

        throw new PropertyException("access to undefined property " . get_class($this) . "::" . $name);
    }

    public function __set(string $name, $value)
    {
        switch ($name) {
            case 'entity':
            case 'metadata':
            case 'values':
            case 'navigations':
            case 'references':
                throw PropertyException::privateProperty(self::class, $name);
                break;
        }

        $this->$name = $value;
    }

    public function detectChanges(): void
    {
        $values = [];
        foreach ($this->metadata->Properties as $property) {
            $propertyName = $property->PropertyInfo->getName();
            if (isset($this->entity->$propertyName)) {
                $values[$propertyName] = $this->entity->$propertyName;
            } else {
                $this->values[$propertyName] = null;
            }
        }

        if ($this->values != $values && $this->state == EntityState::Attached) {
            $this->state = EntityState::Modified;
            $this->values = $values;
        }
    }
}
