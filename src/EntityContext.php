<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity;


use Artister\Data\Entity\Metadata\EntityModel;
use Artister\Data\Entity\Storage\EntityMapper;
use Artister\System\Database\DbConnection;
use Artister\System\Database\DbTransaction;

class EntityContext
{
    protected ?DbTransaction $Transaction;
    protected EntityMapper $Mapper;
    protected EntityModel $Model;
    protected array $Repositories = [];

    public function __construct(DbConnection $connection)
    {
        $builder        = new EntityModelBuilder();
        $this->Mapper   = new EntityMapper($connection, $builder->getModel());
        $this->Model    = $this->Mapper->Model;
        
        $this->onModelCreate($builder);
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function beginTransaction()
    {
        $this->Transaction = $this->Mapper->Connection->beginTransaction();
    }

    /** Registry pattern and singleton pattern. */
    public function set(string $entityType)
    {
        if (isset($this->Repositories[$entityType]))
        {
            return $this->Repositories[$entityType];
        }

        $entityRepository = new EntitySet($entityType, $this->Mapper);

        $this->Repositories[$entityType] = $entityRepository;
        return $this->Repositories[$entityType];
    }

    public function save()
    {
        return $this->Mapper->save();
    }

    public function commit()
    {
        $this->Transaction->commit();
    }

    public function rollBack()
    {
        $this->Transaction->rollBack();
    }

    public function onModelCreate(EntityModelBuilder $builder)
    {
        # overide code...
    }
}