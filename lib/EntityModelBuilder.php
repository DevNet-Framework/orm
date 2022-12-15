<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity;

use DevNet\Entity\Metadata\EntityModel;
use DevNet\Entity\Metadata\EntityTypeBuilder;
use DevNet\Entity\Metadata\IEntityTypeConfiguration;

class EntityModelBuilder
{
    private EntityModel $model;

    public function __construct(EntityModel $model)
    {
        $this->model = $model;
    }

    public function hasSchema(string $name): void
    {
        $this->model->setSchema($name);
    }

    public function entity(string $entityName): EntityTypeBuilder
    {
        $entityType = $this->model->getEntityType($entityName);

        return new EntityTypeBuilder($entityType);
    }

    public function ApplyConfiguration(IEntityTypeConfiguration $configuration): void
    {
        $configuration->configure($this->entity($configuration->getEntityName()));
    }

    public function getModel(): EntityModel
    {
        return $this->model;
    }
}
