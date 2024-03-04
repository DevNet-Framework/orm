<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
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
