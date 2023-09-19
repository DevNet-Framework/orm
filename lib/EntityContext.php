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
use DevNet\Entity\Storage\IEntityDataProvider;
use DevNet\System\Database\DbTransaction;
use DevNet\System\Exceptions\ClassException;
use DevNet\System\PropertyTrait;

class EntityContext
{
    use PropertyTrait;

    protected EntityOptions $options;
    protected EntityDatabase $database;
    private ?DbTransaction $transaction = null;
    private array $repositories = [];

    public function __construct(EntityOptions $options)
    {
        $this->options = $options;

        $providerType = $options->ProviderType;
        if (!class_exists($providerType)) {
            throw new ClassException("Could not find the data provider class {$providerType}", 0, 1);
        }

        $interfaces = class_implements($providerType);
        if (!in_array(IEntityDataProvider::class, $interfaces)) {
            throw new ClassException("{$providerType} must implements IEntityDataProvider inteface", 0, 1);
        }

        $provider = new $providerType($options->ConnectionString);
        $this->database = new EntityDatabase($provider);
        if ($options->DefaultSchema) {
            $this->database->Model->SetSchema($options->DefaultSchema);
        }

        $this->onModelCreate($this->database->Model->Builder);
    }

    public function get_Options(): EntityOptions
    {
        return $this->options;
    }

    public function get_Database(): EntityDatabase
    {
        return $this->database;
    }

    public function get_Model(): EntityModel
    {
        return $this->model;
    }

    public function beginTransaction(): void
    {
        $this->transaction = $this->Database->DataProvider->Connection->beginTransaction();
    }

    public function set(string $entityType): EntitySet
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

    public function commit(): void
    {
        $this->transaction->commit();
    }

    public function rollBack(): void
    {
        $this->transaction->rollBack();
    }

    public function onModelCreate(EntityModelBuilder $builder): void
    {
        # overide code...
    }
}
