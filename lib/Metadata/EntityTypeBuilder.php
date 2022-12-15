<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Metadata;

class EntityTypeBuilder
{
    private EntityType $metadata;

    public function __construct(EntityType $entityType)
    {
        $this->metadata = $entityType;
    }

    public function toTable(string $tableName): static
    {
        $this->metadata->setTableName($tableName);
        return $this;
    }

    public function property(string $propertyName): EntityProperty
    {
        return $this->metadata->getProperty($propertyName);
    }

    public function hasKey(string $propertyName): static
    {
        $this->metadata->setPrimaryKey($propertyName);
        return $this;
    }

    public function hasForeignKey(string $propertyName, string $entityReference): static
    {
        $this->metadata->addForeignKey($propertyName, $entityReference);
        return $this;
    }

    public function hasMany(string $navigationName, string $EntityReference): static
    {
        $navigation = $this->metadata->getNavigation($navigationName);
        $navigation->hasMany($EntityReference);
        return $this;
    }

    public function hasOne(string $navigationName, string $entityReference): static
    {
        $navigation = $this->metadata->getNavigation($navigationName);
        $navigation->hasOne($entityReference);
        return $this;
    }
}
