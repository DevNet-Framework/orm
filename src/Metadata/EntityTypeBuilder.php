<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Metadata;

class EntityTypeBuilder
{
    private EntityType $Metadata;

    public function __construct(EntityType $entityType)
    {
        $this->Metadata = $entityType;
    }

    public function toTable(string $tableName)
    {
        $this->Metadata->setTableName($tableName);
        return $this;
    }

    public function property(string $propertyName)
    {
        return $this->Metadata->getProperty($propertyName);
    }

    public function hasKey(string $propertyName)
    {
        $this->Metadata->setPrimaryKey($propertyName);
        return $this;
    }

    public function hasForeignKey(string $propertyName, string $entityReference)
    {
        $this->Metadata->addForeignKey($propertyName, $entityReference);
        return $this;
    }

    public function hasMany(string $navigationName, string $EntityReference)
    {
        $navigation = $this->Metadata->getNavigation($navigationName);

        $navigation->hasMany($EntityReference);
        return $this;
    }

    public function hasOne(string $navigationName, string $entityReference)
    {
        $navigation = $this->Metadata->getNavigation($navigationName);

        $navigation->hasOne($entityReference);
        return $this;
    }
}
