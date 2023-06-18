<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Metadata;

use DevNet\Entity\EntityModelBuilder;
use DevNet\System\PropertyTrait;

class EntityModel
{
    use PropertyTrait;

    private EntityModelBuilder $builder;
    private array $entityModel = [];
    private ?string $schema = null;

    public function __construct()
    {
        $this->builder = new EntityModelBuilder($this);
    }

    public function get_Builder(): EntityModelBuilder
    {
        return $this->builder;
    }

    public function get_EntityModel(): array
    {
        return $this->entityModel;
    }

    public function get_Schema(): ?string
    {
        return $this->schema;
    }

    public function setSchema(string $name): void
    {
        $this->schema = $name;
    }

    public function addEntityType(EntityType $entityType): void
    {
        $this->entityModel[$entityType->Name] = $entityType;
    }

    public function getEntityType(string $entityName): EntityType
    {
        if (isset($this->entityModel[$entityName])) {
            return $this->entityModel[$entityName];
        }

        $entityType = new EntityType($entityName, $this);
        $this->entityModel[$entityName] = $entityType;

        return $entityType;
    }
}
