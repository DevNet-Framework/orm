<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Metadata;

use DevNet\System\Exceptions\ClassException;
use DevNet\System\Exceptions\PropertyException;
use DevNet\System\PropertyTrait;
use Reflector;
use DateTime;

class EntityType
{
    use PropertyTrait;

    private EntityModel $model;
    private Reflector $entityInfo;
    private string $entityName;
    private string $tableName;
    private ?string $schema     = null;
    private string $propertyKey = 'Id';
    private array $foreignKeys  = [];
    private array $properties   = [];
    private array $navigations  = [];

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
                } else if (class_exists($propertyType)) {
                    $this->navigations[$propertyName] = new EntityNavigation($this, $propertyInfo);
                }
            }
        }
    }

    public function get_Model(): EntityModel
    {
        return $this->model;
    }

    public function get_EntityInfo(): Reflector
    {
        return $this->entityInfo;
    }

    public function get_PropertyKey(): string
    {
        return $this->propertyKey;
    }

    public function get_ForeignKeys(): array
    {
        return $this->foreignKeys;
    }

    public function get_Properties(): array
    {
        return $this->properties;
    }

    public function get_Navigations(): array
    {
        return $this->navigations;
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
        if ($this->schema) {
            return $this->schema;
        }

        return $this->model->Schema;
    }

    public function getPrimaryKey(): string
    {
        $property = $this->getProperty($this->propertyKey);
        if ($property) {
            return $property->Column['Name'];
        }

        return $this->propertyKey;
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
            throw new PropertyException("Could not find the public property {$this->entityName}::{$propertyName}", 0, 1);
        }

        return $propery;
    }

    public function getNavigation(string $navigationName): EntityNavigation
    {
        $navigation = $this->navigations[$navigationName] ?? null;
        if (!$navigation) {
            throw new PropertyException("Could not find the public property {$this->entityName}::{$navigationName}", 0, 1);
        }

        return $navigation;
    }

    public function setTableName(string $name, string $schema = null): void
    {
        $this->tableName = $name;
        $this->schema = $schema;
    }

    public function setPrimaryKey(string $propertyName): void
    {
        if (!property_exists($this->entityName, $propertyName)) {
            new PropertyException("Could not find the public property {$this->entityName}::{$propertyName}", 0, 1);
        }

        $property = $this->getProperty($propertyName);
        if ($property) {
            $this->propertyKey = $propertyName;
        }
    }

    public function addForeignKey(string $propertyName, string $entityReference): void
    {
        if (!property_exists($this->entityName, $propertyName)) {
            throw new PropertyException("Could not find the public property {$this->entityName}::{$propertyName}");
        }

        if (!class_exists($entityReference)) {
            throw new ClassException("Could not find the entity reference {$entityReference}", 0, 1);
        }

        $this->foreignKeys[$entityReference] = $propertyName;
    }
}
