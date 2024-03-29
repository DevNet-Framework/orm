<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM;

use DevNet\ORM\Metadata\EntityModel;
use DevNet\ORM\Metadata\EntityTypeBuilder;
use DevNet\ORM\Metadata\IEntityTypeConfiguration;

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
