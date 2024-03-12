<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Metadata;

use DevNet\ORM\Annotations\PrimaryKey;
use DevNet\ORM\Annotations\Table;
use DevNet\System\Collections\ICollection;
use DevNet\System\Exceptions\ClassException;
use DevNet\System\Exceptions\PropertyException;
use DevNet\System\PropertyTrait;
use DateTime;
use Reflector;

class EntityType
{
    use PropertyTrait;

    private EntityModel $model;
    private Reflector $entityInfo;
    private string $name;
    private string $tableName;
    private ?string $schema    = null;
    private array $keys        = [];
    private array $foreignKeys = [];
    private array $properties  = [];
    private array $navigations = [];

    public function __construct(string $entityName, EntityModel $model)
    {
        $this->name       = $entityName;
        $this->model      = $model;
        $this->entityInfo = new \ReflectionClass($entityName);
        $this->tableName  = $this->entityInfo->getShortName();

        if (property_exists($entityName, 'Id')) {
            $this->keys[] = 'Id';
        }

        foreach ($this->entityInfo->getAttributes() as $attribute) {
            if ($attribute->getName() == Table::class) {
                $table = $attribute->newInstance();
                $this->tableName = $table->getName();
                $this->schema = $table->getSchema();
            } else if ($attribute->getName() == PrimaryKey::class) {
                $primary = $attribute->newInstance();
                $this->keys = $primary->getKeys();
            }
        }

        $scalarTypes = ['bool', 'int', 'float', 'string'];
        foreach ($this->entityInfo->getProperties() as $propertyInfo) {
            // map only public typed properties
            if ($propertyInfo->isPublic() && $propertyInfo->hasType()) {
                $propertyName = $propertyInfo->getName();
                $propertyType = $propertyInfo->getType()->getName();
                if (in_array(strtolower($propertyType), $scalarTypes) || $propertyType === DateTime::class) {
                    $this->properties[$propertyName] = new EntityProperty($this, $propertyInfo);
                } else if ($propertyType == ICollection::class) {
                    $this->navigations[$propertyName] = new EntityNavigation($this, $propertyInfo, 2);
                } else if (class_exists($propertyType)) {
                    $this->navigations[$propertyName] = new EntityNavigation($this, $propertyInfo, 1);
                }
            }
        }
    }

    public function get_Name(): string
    {
        return $this->name;
    }

    public function get_Model(): EntityModel
    {
        return $this->model;
    }

    public function get_EntityInfo(): Reflector
    {
        return $this->entityInfo;
    }

    public function get_Keys(): array
    {
        return $this->keys;
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

    public function getForeignKey(string $relatedEntity): ?string
    {
        return $this->foreignKeys[$relatedEntity] ?? null;
    }

    public function getProperty(string $propertyName): EntityProperty
    {
        $property = $this->properties[$propertyName] ?? null;
        if (!$property) {
            throw new PropertyException("Could not find the public property {$this->name}::{$propertyName}", 0, 1);
        }

        return $property;
    }

    public function getNavigation(string $navigationName): EntityNavigation
    {
        $navigation = $this->navigations[$navigationName] ?? null;
        if (!$navigation) {
            throw new PropertyException("Could not find the public property {$this->name}::{$navigationName}", 0, 1);
        }

        return $navigation;
    }

    public function setTableName(string $name, string $schema = null): void
    {
        $this->tableName = $name;
        $this->schema = $schema;
    }

    public function setPrimaryKey(array $propertyNames): void
    {
        foreach ($propertyNames as $propertyName) {
            if (!property_exists($this->name, $propertyName)) {
                new PropertyException("Could not find the public property {$this->name}::{$propertyName}", 0, 1);
            }
        }

        if ($propertyNames) {
            $this->keys = $propertyNames;
        }
    }

    public function addForeignKey(string $propertyName, string $relatedEntity): void
    {
        if (!property_exists($this->name, $propertyName)) {
            throw new PropertyException("Could not find the public property {$this->name}::{$propertyName}");
        }

        if (!class_exists($relatedEntity)) {
            throw new ClassException("Could not find the entity reference {$relatedEntity}", 0, 1);
        }

        $this->foreignKeys[$relatedEntity] = $propertyName;
    }
}
