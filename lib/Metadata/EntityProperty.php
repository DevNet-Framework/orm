<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Metadata;

use DevNet\Entity\Annotations\Column;
use DevNet\System\PropertyTrait;
use ReflectionProperty;

class EntityProperty
{
    use PropertyTrait;

    private EntityType $entityType;
    private ReflectionProperty $propertyInfo;
    private string $columnName;

    public function __construct(EntityType $entityType, ReflectionProperty $propertyInfo)
    {
        $this->entityType   = $entityType;
        $this->propertyInfo = $propertyInfo;
        $this->columnName   = $propertyInfo->getName();

        foreach ($this->methodInfo->getAttributes() as $attribute) {
            if ($attribute->getName() == Column::class) {
                $column = $attribute->newInstance();
                $this->columnName = $column->getName();
                break;
            }
        }
    }

    public function get_EntityType(): EntityType
    {
        return $this->entityType;
    }

    public function get_PropertyInfo(): ReflectionProperty
    {
        return $this->propertyInfo;
    }

    public function hasColumn(string $name): void
    {
        $this->columnName = $name;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }
}
