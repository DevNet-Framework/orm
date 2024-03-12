<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Tracking;

use DevNet\ORM\Metadata\EntityType;
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
            if ($property->PropertyInfo->isInitialized($this->entity)) {
                $value = $property->PropertyInfo->getValue($this->entity);
                if (is_array($value) || is_object($value)) {
                    if ($value instanceof DateTime) {
                        $value = $value->format('y-m-d h:m:s');
                    } else {
                        continue;
                    }
                }
                $this->values[$property->getColumnName()] = $value;
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
            if ($property->PropertyInfo->isInitialized($this->entity)) {
                $value = $property->PropertyInfo->getValue($this->entity);
                if (is_array($value) || is_object($value)) {
                    if ($value instanceof DateTime) {
                        $value = $value->format('y-m-d h:m:s');
                    } else {
                        continue;
                    }
                }
                if ($this->values[$property->getColumnName()] != $value) {
                    $values[$property->getColumnName()] = $value;
                }
            }
        }

        if ($values) {
            $this->values = $values;
            $this->state  = EntityState::Modified;
        }
    }
}
