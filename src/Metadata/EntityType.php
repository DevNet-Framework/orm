<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Metadata;

use DevNet\Entity\IEntity;
use DevNet\System\Collections\IList;
use DevNet\System\Exceptions\ClassException;
use DevNet\System\Exceptions\PropertyException;
use Reflector;
use DateTime;

class EntityType
{
    private EntityModel $Model;
    private Reflector $EntityInfo;
    private string $EntityName;
    private string $TableName;
    private string $PropertyKey = 'Id';
    private array $ForeignKeys  = [];
    private array $Properties   = [];
    private array $Navigations  = [];

    public function __construct(string $entityName, EntityModel $model)
    {
        $this->Model      = $model;
        $this->EntityName = $entityName;
        $this->EntityInfo = new \ReflectionClass($entityName);
        $this->TableName  = $this->EntityInfo->getShortName();

        $scalarTypes = ['bool', 'int', 'float', 'string'];
        foreach ($this->EntityInfo->getProperties() as $PropertyInfo) {
            if ($PropertyInfo->hasType()) {
                $propertyName = $PropertyInfo->getName();
                $propertyType = $PropertyInfo->getType()->getName();
                if (in_array(strtolower($propertyType), $scalarTypes) || $propertyType === DateTime::class) {
                    $this->Properties[$propertyName] = new EntityProperty($this, $PropertyInfo);
                    if (strtolower($propertyName) === 'id') {
                        $this->PropertyKey = $propertyName;
                    }
                } else {
                    if ($propertyType === IList::class) {
                        // later add conventional code here
                        $this->Navigations[$propertyName] = new EntityNavigation($this, $PropertyInfo); //new EntityNavigation($this);
                    } else {
                        if (class_exists($propertyType)) {
                            $interfaces = class_implements($propertyType);
                            if (in_array(IEntity::class, $interfaces)) {
                                // later add conventional code here
                                $this->Navigations[$propertyName] = new EntityNavigation($this, $PropertyInfo); //new EntityNavigation($this);
                            }
                        }
                    }
                }
            }
        }
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function getName()
    {
        return $this->EntityName;
    }

    public function getTableName(): string
    {
        return $this->TableName;
    }

    public function getPrimaryKey(): string
    {
        $property = $this->getProperty($this->PropertyKey);
        if ($property) {
            return $property->Column['Name'];
        }
    }

    public function getForeignKey(string $entityReference): ?string
    {
        $propertyName = $this->ForeignKeys[$entityReference] ?? null;
        if (!$propertyName) {
            return null;
        }

        $property = $this->getProperty($propertyName);
        return $property->Column['Name'] ?? null;
    }

    public function getProperty(string $propertyName)
    {
        $propery = $this->Properties[$propertyName] ?? null;
        if (!$propery) {
            throw new PropertyException("Undefined property {$this->EntityName}::{$propertyName}");
        }

        return $propery;
    }

    public function getNavigation(string $navigationName)
    {
        $navigation = $this->Navigations[$navigationName] ?? null;
        if (!$navigation) {
            throw new PropertyException("Undefined property {$this->EntityName}::{$navigationName}");
        }

        return $navigation;
    }

    public function setTableName(string $name)
    {
        $this->TableName = $name;
    }

    public function setPrimaryKey(string $propertyName)
    {
        if (!property_exists($this->EntityName, $propertyName)) {
            throw new PropertyException("Undefined property {$this->EntityName}::{$propertyName}");
        }

        $property = $this->getProperty($propertyName);
        if ($property) {
            $this->PropertyKey = $propertyName;
        }
    }

    public function addForeignKey(string $propertyName, string $entityReference)
    {
        if (!property_exists($this->EntityName, $propertyName)) {
            throw new PropertyException("Undefined property {$this->EntityName}::{$propertyName}");
        }

        if (!class_exists($entityReference)) {
            throw new ClassException("Class {$entityReference} not found");
        }

        $this->ForeignKeys[$entityReference] = $propertyName;
    }
}
