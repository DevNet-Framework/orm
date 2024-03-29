<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM;

use DevNet\ORM\Query\EntityQuery;
use DevNet\ORM\Storage\EntityDatabase;

class EntitySet extends EntityQuery
{
    private EntityDatabase $database;

    public function __construct(string $entityName, EntityDatabase $database)
    {
        $this->database = $database;

        parent::__construct($database->Model->getEntityType($entityName), $database->QueryProvider);
    }

    public function find(string ...$keyValues): ?object
    {
        return $this->database->Finder->find($this->EntityType, $keyValues);
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
        $entityName = $this->EntityType->Name;
        $entity = new $entityName();
        $this->add($entity);
        return $entity;
    }
}
