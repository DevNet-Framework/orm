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

class EntityNavigation
{
    public const NavigationReference  = 1;
    public const NavigationCollection = 2;

    private ReflectionProperty $propertyInfo;
    private EntityType $metadata;
    private EntityType $metadataReference;
    private int $navigationType = 0;

    public function __get(string $name)
    {
        if (in_array($name, ['PropertyInfo', 'Metadata', 'MetadataReference', 'NavigationType'])) {
            $property = lcfirst($name);
            return $this->$property;
        }

        if (property_exists($this, $name)) {
            throw new PropertyException("access to private property " . get_class($this) . "::" . $name);
        }

        throw new PropertyException("access to undefined property " . get_class($this) . "::" . $name);
    }

    public function __construct(EntityType $entityType, ReflectionProperty $propertyInfo)
    {
        $this->metadata = $entityType;
        $this->propertyInfo = $propertyInfo;
    }

    public function hasMany(string $entityReference)
    {
        $this->metadataReference = $this->metadata->Model->getEntityType($entityReference);
        $this->navigationType = 2;
    }

    public function hasOne(string $EntityReference)
    {
        $this->metadataReference = $this->metadata->Model->getEntityType($EntityReference);
        $this->navigationType = 1;
    }

    public function getForeignKey(): ?string
    {
        return $this->metadataReference->getForeignKey($this->metadata->getName());
    }
}
