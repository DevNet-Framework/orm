<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

use DevNet\Entity\Migration\Operations\Operation;
use DevNet\Entity\Storage\EntityDatabase;
use DevNet\System\Collections\Enumerator;
use DevNet\System\Collections\IEnumerable;
use DevNet\System\MethodTrait;
use stdClass;

class MigrationHistory implements IEnumerable
{
    use MethodTrait;

    private EntityDatabase $database;
    private string $table     = 'MigrationHistory';
    private array $migrations = [];
    private bool $existence   = false;

    public function __construct(EntityDatabase $database, string $namespace, string $directory)
    {
        $this->database = $database;

        $connection = $database->DataProvider->Connection;
        $connection->open();

        $script  = $this->getSelectScript();
        $command = $connection->createCommand($script);

        try {
            $dbReader = $command->executeReader();
        } catch (\Exception $e) {
            $dbReader = null;
        }

        if ($dbReader) {
            $this->existence = true;
            while ($dbReader->read()) {
                $file = $directory . "/" . $dbReader->getValue("Id") . "_" . $dbReader->getValue("Name") . ".php";
                if (file_exists($file)) {
                    require_once($file);
                    $migration = new stdClass();
                    $migration->Id = (int)$dbReader->getValue("Id");
                    $migration->Name = $dbReader->getValue("Name");
                    $migration->Type = $namespace . "\\" . $dbReader->getValue("Name");
                    $this->migrations[] = $migration;
                } else {
                    throw new \Exception("Not found File: {$file}");
                }
            }
        }

        $connection->close();
    }

    public function exists(): bool
    {
        return $this->existence;
    }

    public function getSelectScript(): string
    {
        $sqlHelper = $this->database->DataProvider->SqlHelper;
        $table = $sqlHelper->delimitIdentifier($this->table);

        return "SELECT * FROM {$table}";
    }

    public function getCreateScript(): string
    {
        $table = Operation::createTable('MigrationHistory');
        $table->column('Id', 'bigint')->nullable(false);
        $table->column('Name', 'varchar(45)')->nullable(false);
        $table->primaryKey('Id');

        $migrationGenerator = new $this->database->DataProvider->MigrationGenerator;
        $migrationGenerator->visit($table);

        return $migrationGenerator->__toString();
    }

    public function getInsertScript(string $id, string $name): string
    {
        $data = Operation::insertData($this->table, ['Id' => $id, 'Name' => $name]);
        $migrationGenerator = new $this->database->DataProvider->MigrationGenerator;
        $migrationGenerator->visit($data);

        return $migrationGenerator->__toString();
    }

    public function getDeleteScript(string $id): string
    {
        $data = Operation::deleteData($this->table, ['Id' => $id]);
        $migrationGenerator = new $this->database->DataProvider->MigrationGenerator;
        $migrationGenerator->visit($data);

        return $migrationGenerator->__toString();
    }

    public function getIterator(): Enumerator
    {
        return new Enumerator($this->migrations);
    }
}
