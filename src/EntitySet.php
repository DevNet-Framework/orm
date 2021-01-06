<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Entity;

use Artister\Entity\IEntity;
use Artister\Entity\Query\EntityQuery;
use Artister\Entity\Storage\EntityDatabase;

class EntitySet extends EntityQuery
{
    private EntityDatabase $Database;

    public function __construct(string $entityName, EntityDatabase $database)
    {
        $this->Database = $database;

        parent::__construct($database->Model->getEntityType($entityName), $database->QueryProvider);
    }

    public function find(int $id) : ?IEntity
    {
        return $this->Database->Finder->find($this->EntityType, $id);
    }

    public function add(IEntity $entity) : void
    {
        $this->Database->add($entity);
    }

    public function remove(IEntity $entity) : void
    {
        $this->Database->remove($entity);
    }

    public function create()
    {
        $entityName = $this->EntityType->getName();
        $entity = new $entityName();
        $this->add($entity);
        return $entity;
    }
}