<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity;

use DevNet\Entity\Query\EntityQuery;
use DevNet\Entity\Storage\EntityDatabase;

class EntitySet extends EntityQuery
{
    private EntityDatabase $database;

    public function __construct(string $entityName, EntityDatabase $database)
    {
        $this->database = $database;

        parent::__construct($database->Model->getEntityType($entityName), $database->QueryProvider);
    }

    public function find(int $id): ?object
    {
        return $this->database->Finder->find($this->EntityType, $id);
    }

    public function add(object $entity): void
    {
        $this->database->add($entity);
    }

    public function remove(object $entity): void
    {
        $this->database->remove($entity);
    }

    public function update(object $entity): void
    {
        $this->database->attach($entity);
    }

    public function create(): object
    {
        $entityName = $this->EntityType->getName();
        $entity = new $entityName();
        $this->add($entity);
        return $entity;
    }
}
