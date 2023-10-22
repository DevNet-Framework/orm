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

    public function hasKey(string ...$properties): static
    {
        $this->metadata->setPrimaryKey($properties);
        return $this;
    }

    public function property(string $propertyName): EntityProperty
    {
        return $this->metadata->getProperty($propertyName);
    }

    public function navigation(string $navigationName): EntityNavigation
    {
        return $this->metadata->getNavigation($navigationName);
    }
}
