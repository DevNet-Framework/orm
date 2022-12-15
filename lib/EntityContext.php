<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity;

use DevNet\Entity\Metadata\EntityModel;
use DevNet\Entity\Providers\MySql\MySqlDataProvider;
use DevNet\Entity\Providers\PostgreSql\PostgreSqlDataProvider;
use DevNet\Entity\Providers\Sqlite\SqliteDataProvider;
use DevNet\Entity\Storage\EntityDatabase;
use DevNet\System\Database\DbTransaction;
use DevNet\System\ObjectTrait;

class EntityContext
{
    use ObjectTrait;

    protected EntityOptions $options;
    protected EntityDatabase $database;
    private ?DbTransaction $transaction = null;
    private array $repositories = [];

    public function __construct(string $connectionString)
    {
        $this->options = new EntityOptions();
        $this->onConfigure($this->options);

        $roviderType = $this->options->ProviderType;
        if (class_exists($roviderType)) {
            $provider = new $roviderType($connectionString);
        } else {
            $driver = parse_url($connectionString, PHP_URL_SCHEME);
            switch ($driver) {
                case 'mysql':
                    $provider = new MySqlDataProvider($connectionString);
                    break;
                case 'pgsql':
                    $provider = new PostgreSqlDataProvider($connectionString);
                    break;
                case 'sqlite':
                    $provider = new SqliteDataProvider($connectionString);
                    break;
                default:
                    throw new \Exception("Could not find a compatible Data Provider! Try to implement a custom IEntityDataProvider");
                    break;
            }
        }

        $this->database = new EntityDatabase($provider);
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

    public function onConfigure(EntityOptions $options): void
    {
        # overide code...
    }

    public function onModelCreate(EntityModelBuilder $builder): void
    {
        # overide code...
    }
}
