<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Metadata;

use DevNet\System\Exceptions\PropertyException;
use ReflectionProperty;

class EntityProperty
{
    private EntityType $metadata;
    private ReflectionProperty $propertyInfo;
    private string $tableReference;
    private array $column = [];
    private ?EntityNavigation $navigation = null;

    public function __get(string $name)
    {
        if (in_array($name, ['Metadata', 'PropertyInfo', 'TableReference', 'Column', 'Navigation'])) {
            $property = lcfirst($name);
            return $this->$property;
        }

        if (property_exists($this, $name)) {
            throw new PropertyException("access to private property" . get_class($this) . "::" . $name);
        }

        throw new PropertyException("access to undefined property" . get_class($this) . "::" . $name);
    }

    public function __construct(EntityType $entityType, ReflectionProperty $propertyInfo)
    {
        $this->metadata       = $entityType;
        $this->propertyInfo   = $propertyInfo;
        $this->column['Name'] = $propertyInfo->getName();
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
