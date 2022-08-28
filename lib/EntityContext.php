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
use DevNet\System\ObjectTrait;

class EntityContext
{
    use ObjectTrait;

    protected EntityDatabase $database;
    protected EntityModel $model;
    private ?DbTransaction $transaction = null;
    private array $repositories = [];

    public function __construct(EntityOptions $options)
    {
        $builder        = new EntityModelBuilder();
        $this->Database = new EntityDatabase($options, $builder->getModel());
        $this->Model    = $this->Database->Model;

        $this->onModelCreate($builder);
    }

    public function get_Database(): EntityDatabase
    {
        return $this->database;
    }

    public function get_Model(): EntityModel
    {
        return $this->model;
    }

    public function beginTransaction()
    {
        $this->transaction = $this->Database->DataProvider->Connection->beginTransaction();
    }

    public function set(string $entityType)
    {
        // Registry pattern with singleton pattern.
        if (isset($this->repositories[$entityType])) {
            return $this->repositories[$entityType];
        }

        $entityRepository = new EntitySet($entityType, $this->Database);
        $this->repositories[$entityType] = $entityRepository;

        return $entityRepository;
    }

    public function save(): int
    {
        return $this->Database->save();
    }

    public function commit()
    {
        $this->transaction->commit();
    }

    public function rollBack()
    {
        $this->transaction->rollBack();
    }

    public function onModelCreate(EntityModelBuilder $builder)
    {
        # overide code...
    }
}
