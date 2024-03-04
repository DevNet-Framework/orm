<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
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
