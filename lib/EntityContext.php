<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity;

use DevNet\Entity\Metadata\EntityModel;
use DevNet\Entity\Storage\EntityDatabase;
use DevNet\System\Database\DbTransaction;

class EntityContext
{
    protected ?DbTransaction $Transaction;
    protected EntityDatabase $Database;
    protected EntityModel $Model;
    protected array $Repositories = [];

    public function __construct(EntityOptions $options)
    {
        $builder        = new EntityModelBuilder();
        $this->Database = new EntityDatabase($options, $builder->getModel());
        $this->Model    = $this->Database->Model;

        $this->onModelCreate($builder);
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function beginTransaction()
    {
        $this->Transaction = $this->Database->DataProvider->Connection->beginTransaction();
    }

    /** Registry pattern and singleton pattern. */
    public function set(string $entityType)
    {
        if (isset($this->Repositories[$entityType])) {
            return $this->Repositories[$entityType];
        }

        $entityRepository = new EntitySet($entityType, $this->Database);

        $this->Repositories[$entityType] = $entityRepository;
        return $this->Repositories[$entityType];
    }

    public function save(): int
    {
        return $this->Database->save();
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
