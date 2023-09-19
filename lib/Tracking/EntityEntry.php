<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Tracking;

use DevNet\Entity\Metadata\EntityType;
use DevNet\System\PropertyTrait;
use DateTime;

class EntityEntry
{
    use PropertyTrait;

    private EntityType $metadata;
    private EntityState $state;
    private object $entity;
    private array $values = [];

    public function __construct(object $entity, EntityType $entityType)
    {
        $this->entity   = $entity;
        $this->metadata = $entityType;
        $this->state    = EntityState::Attached;

        foreach ($this->metadata->Properties as $property) {
            $propertyName = $property->PropertyInfo->getName();
            if ($property->PropertyInfo->isInitialized($this->entity)) {
                $value = $property->PropertyInfo->getValue($this->entity);
                if (is_array($value) || is_object($value)) {
                    if ($value instanceof DateTime) {
                        $value = $value->format('y-m-d h:m:s');
                    } else {
                        continue;
                    }
                }
                $this->values[$propertyName] = $value;
            } else {
                $this->values[$propertyName] = null;
            }
        }
    }

    public function get_Metadata(): EntityType
    {
        return $this->metadata;
    }

    public function get_State(): EntityState
    {
        return $this->state;
    }

    public function get_Entity(): Object
    {
        return $this->entity;
    }

    public function get_Values(): array
    {
        return $this->values;
    }

    public function set_State(EntityState $state): void
    {
        $this->state = $state;
    }

    public function detectChanges(): void
    {
        $values = [];
        foreach ($this->metadata->Properties as $property) {
            $propertyName = $property->PropertyInfo->getName();
            if ($property->PropertyInfo->isInitialized($this->entity)) {
                $value = $property->PropertyInfo->getValue($this->entity);
                if (is_array($value) || is_object($value)) {
                    if ($value instanceof DateTime) {
                        $value = $value->format('y-m-d h:m:s');
                    } else {
                        continue;
                    }
                }
                $values[$propertyName] = $value;
            } else {
                $values[$propertyName] = null;
            }
        }

        if ($this->values != $values) {
            $this->values = $values;
            $this->state  = EntityState::Modified;
        }
    }
}
