<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity;

use Artister\Data\Entity\Query\EntityQuery;
use Artister\Data\Entity\Storage\EntityMapper;
use Artister\Data\Entity\Metadata\EntityType;
use Artister\Data\Entity\IEntity;
use Artister\System\Linq\Expressions\Expression;
use Artister\System\Linq\IQueryProvider;

class EntitySet extends EntityQuery
{
    //public object $EntityType;
    private EntityMapper $Mapper;

    public function __construct(string $entityName, EntityMapper $mapper)
    {
        $this->Mapper = $mapper;
        //$this->EntityType = $mapper->Model->getEntityType($entityName);

        parent::__construct($mapper->Model->getEntityType($entityName), $mapper->Provider);
    }

    public function find(int $id) : ?IEntity
    {
        return $this->Mapper->Finder->find($this->EntityType, $id);
    }

    public function add(IEntity $entity) : void
    {
        $this->Mapper->add($entity);
    }

    public function remove(IEntity $entity) : void
    {
        $this->Mapper->remove($entity);
    }

    public function create()
    {
        $entityName = $this->EntityType->getName();
        $entity = new $entityName();
        $this->add($entity);
        return $entity;
    }
}