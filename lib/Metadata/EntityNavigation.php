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

class EntityNavigation
{
    use ObjectTrait;

    public const NavigationReference  = 1;
    public const NavigationCollection = 2;

    private ReflectionProperty $propertyInfo;
    private EntityType $metadata;
    private EntityType $metadataReference;
    private int $navigationType = 0;

    public function __construct(EntityType $entityType, ReflectionProperty $propertyInfo)
    {
        $this->metadata = $entityType;
        $this->propertyInfo = $propertyInfo;
    }

    public function get_PropertyInfo(): ReflectionProperty
    {
        return $this->propertyInfo;
    }
    public function get_Metadata(): EntityType
    {
        return $this->metadata;
    }
    public function get_MetadataReference(): EntityType
    {
        return $this->metadataReference;
    }
    public function get_NavigationType(): int
    {
        return $this->navigationType;
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
