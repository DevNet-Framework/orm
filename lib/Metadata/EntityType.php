<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Metadata;

use DevNet\System\Collections\IList;
use DevNet\System\Exceptions\ClassException;
use DevNet\System\Exceptions\PropertyException;
use Reflector;
use DateTime;

class EntityType
{
    private EntityModel $model;
    private Reflector $entityInfo;
    private string $entityName;
    private string $tableName;
    private string $propertyKey = 'Id';
    private array $foreignKeys  = [];
    private array $properties   = [];
    private array $navigations  = [];

    public function __get(string $name)
    {
        if (in_array($name, ['Model', 'EntityInfo', 'EntityName', 'TableName', 'PropertyKey', 'foreignKeys', 'properties', 'navigations'])) {
            $property = lcfirst($name);
            return $this->$property;
        }

        if (property_exists($this, $name)) {
            throw new PropertyException("access to private property " . get_class($this) . "::" . $name);
        }

        throw new PropertyException("access to undefined property " . get_class($this) . "::" . $name);
    }

    public function __construct(string $entityName, EntityModel $model)
    {
        $this->model      = $model;
        $this->entityName = $entityName;
        $this->entityInfo = new \ReflectionClass($entityName);
        $this->tableName  = $this->entityInfo->getShortName();

        $scalarTypes = ['bool', 'int', 'float', 'string'];
        foreach ($this->entityInfo->getProperties() as $propertyInfo) {
            // map only public typed properties
            if ($propertyInfo->isPublic() && $propertyInfo->hasType()) {
                $propertyName = $propertyInfo->getName();
                $propertyType = $propertyInfo->getType()->getName();
                if (in_array(strtolower($propertyType), $scalarTypes) || $propertyType === DateTime::class) {
                    $this->properties[$propertyName] = new EntityProperty($this, $propertyInfo);
                    if (strtolower($propertyName) === 'id') {
                        $this->propertyKey = $propertyName;
                    }
                } else {
                    if ($propertyType === IList::class) {
                        // conventional collection navigation property feature will be added in the future release
                        $this->navigations[$propertyName] = new EntityNavigation($this, $propertyInfo);
                    } else {
                        if (class_exists($propertyType)) {
                            // conventional reference navigation property feature will be added in the future release
                            $this->navigations[$propertyName] = new EntityNavigation($this, $propertyInfo);
                        }
                    }
                }
            }
        }
    }

    public function getName(): string
    {
        return $this->entityName;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getSchemaName(): ?string
    {
        return $this->model->Schema;
    }

    public function getPrimaryKey(): string
    {
        $property = $this->getProperty($this->propertyKey);
        if ($property) {
            return $property->Column['Name'];
        }
    }

    public function getForeignKey(string $entityReference): ?string
    {
        $propertyName = $this->foreignKeys[$entityReference] ?? null;
        if (!$propertyName) {
            return null;
        }

        $property = $this->getProperty($propertyName);
        return $property->Column['Name'] ?? null;
    }

    public function getProperty(string $propertyName): EntityProperty
    {
        $propery = $this->properties[$propertyName] ?? null;
        if (!$propery) {
            throw new PropertyException("Undefined property {$this->entityName}::{$propertyName}");
        }

        return $propery;
    }

    public function getNavigation(string $navigationName): EntityNavigation
    {
        $navigation = $this->navigations[$navigationName] ?? null;
        if (!$navigation) {
            throw new PropertyException("Undefined property {$this->entityName}::{$navigationName}");
        }

        return $navigation;
    }

    public function setTableName(string $name): void
    {
        $this->tableName = $name;
    }

    public function setPrimaryKey(string $propertyName): void
    {
        if (!property_exists($this->entityName, $propertyName)) {
            throw new PropertyException("Undefined property {$this->entityName}::{$propertyName}");
        }

        $property = $this->getProperty($propertyName);
        if ($property) {
            $this->propertyKey = $propertyName;
        }
    }

    public function addForeignKey(string $propertyName, string $entityReference): void
    {
        if (!property_exists($this->entityName, $propertyName)) {
            throw new PropertyException("Undefined property {$this->entityName}::{$propertyName}");
        }

        if (!class_exists($entityReference)) {
            throw new ClassException("Class {$entityReference} not found");
        }

        $this->foreignKeys[$entityReference] = $propertyName;
    }
}
