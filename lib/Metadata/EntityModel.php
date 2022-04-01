<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Metadata;

use DevNet\Entity\EntityModelBuilder;
use DevNet\System\Exceptions\PropertyException;

class EntityModel
{
    private EntityModelBuilder $builder;
    private array $entityModel = [];
    private ?string $schema = null;

    public function __get(string $name)
    {
        if (in_array($name, ['Builder', 'EntityModel', 'Schema'])) {
            $property = lcfirst($name);
            return $this->$property;
        }

        if (property_exists($this, $name)) {
            throw new PropertyException("access to private property" . get_class($this) . "::" . $name);
        }

        throw new PropertyException("access to undefined property" . get_class($this) . "::" . $name);
    }

    public function __construct(EntityModelBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function setSchema(string $name)
    {
        $this->schema = $name;
    }

    public function addEntityType(EntityType $entityType)
    {
        $this->entityModel[$entityType->getName()] = $entityType;
    }

    public function getEntityType(string $entityName)
    {
        if (isset($this->entityModel[$entityName])) {
            return $this->entityModel[$entityName];
        }

        $entityType = new EntityType($entityName, $this);
        $this->entityModel[$entityName] = $entityType;

        return $entityType;
    }
}
