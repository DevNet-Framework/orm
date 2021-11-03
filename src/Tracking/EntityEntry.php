<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Tracking;

use DevNet\Entity\IEntity;
use DevNet\Entity\Metadata\EntityType;
use DevNet\System\Exceptions\PropertyException;
use DateTime;

class EntityEntry
{
    private IEntity $Entity;
    private EntityType $Metadata;
    private int $State;
    private array $Values = [];

    public function __construct(IEntity $entity, EntityType $entityType)
    {
        $this->Entity   = $entity;
        $this->Metadata = $entityType;
        $this->State    = EntityState::Attached;
    }

    public function __get(string $name)
    {
        if ($name == "Values") {
            foreach ($this->Metadata->Properties as $property) {
                $propertyName = $property->PropertyInfo->getName();
                $property->PropertyInfo->setAccessible(true);
                if ($property->PropertyInfo->isInitialized($this->Entity)) {
                    $value = $property->PropertyInfo->getValue($this->Entity);
                    if ($value instanceof DateTime) {
                        $value = $value->format('y-m-d h:m:s');
                    }
                    $this->Values[$propertyName] = $value;
                } else {
                    $this->Values[$propertyName] = null;
                }
            }
        }

        return $this->$name;
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

    public function detectChanges()
    {
        $values = [];
        foreach ($this->Metadata->Properties as $property) {
            $propertyName = $property->PropertyInfo->getName();
            if (isset($this->Entity->$propertyName)) {
                $values[$propertyName] = $this->Entity->$propertyName;
            } else {
                $this->Values[$propertyName] = null;
            }
        }

        if ($this->Values != $values && $this->State == EntityState::Attached) {
            $this->State = EntityState::Modified;
            $this->Values = $values;
        }
    }
}
