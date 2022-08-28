<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Metadata;

use DevNet\System\ObjectTrait;
use ReflectionProperty;

class EntityProperty
{
    use ObjectTrait;

    private EntityType $metadata;
    private ReflectionProperty $propertyInfo;
    private string $tableReference;
    private array $column = [];
    private ?EntityNavigation $navigation = null;

    public function __construct(EntityType $entityType, ReflectionProperty $propertyInfo)
    {
        $this->metadata       = $entityType;
        $this->propertyInfo   = $propertyInfo;
        $this->column['Name'] = $propertyInfo->getName();
    }

    public function get_Metadata(): EntityType
    {
        return $this->metadata;
    }

    public function get_PropertyInfo(): ReflectionProperty
    {
        return $this->propertyInfo;
    }

    public function get_TableReference(): string
    {
        return $this->tableReference;
    }

    public function get_Column(): array
    {
        return $this->column;
    }

    public function get_Navigation(): ?EntityNavigation
    {
        return $this->navigation;
    }

    public function hasColumn(string $name, string $type = null, int $lenth = null)
    {
        $this->column['Name']  = $name;
        $this->column['Type']  = $type;
        $this->column['Lenth'] = $lenth;
    }

    public function getColumnName(): string
    {
        return $this->column['Name'];
    }
}
