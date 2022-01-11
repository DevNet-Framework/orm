<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

use DevNet\Entity\Migration\Operations\CreateTable;
use DevNet\Entity\Migration\Operations\InsertData;
use DevNet\Entity\Migration\Operations\DeleteData;
use DevNet\Entity\Storage\EntityDatabase;
use DevNet\System\Collections\Enumerator;
use DevNet\System\Collections\IEnumerable;
use stdClass;

class MigrationHistory implements IEnumerable
{
    use \DevNet\System\Extension\ExtenderTrait;

    private EntityDatabase $Database;
    
    private ?string $Schema   = null;
    private string $Table     = 'MigrationHistory';
    private array $Migrations = [];
    private bool $Existence   = false;

    public function __construct(EntityDatabase $database, string $namespace, string $directory)
    {
        $this->Schema   = $database->Model->Schema;
        $this->Database = $database;

        $connection = $database->DataProvider->Connection;
        $connection->open();

        $script   = $this->getSelectScript();
        $command  = $connection->createCommand($script);

        try {
            $dbReader = $command->executeReader();
        } catch (\Exception $e) {
            $dbReader = null;
        }

        if ($dbReader) {
            $this->Existence = true;
            while ($dbReader->read()) {
                $file = $directory . "/" . $dbReader->getValue("Id") . "_" . $dbReader->getValue("Name") . ".php";
                if (file_exists($file)) {
                    require_once($file);
                    $migration = new stdClass();
                    $migration->Id = (int)$dbReader->getValue("Id");
                    $migration->Name = $dbReader->getValue("Name");
                    $migration->Type = $namespace . "\\" . $dbReader->getValue("Name");
                    $this->Migrations[] = $migration;
                } else {
                    throw new \Exception("Not found File: {$file}");
                }
            }
        }

        $connection->close();
    }

    public function exists(): bool
    {
        return $this->Existence;
    }

    public function getSelectScript(): string
    {
        $sqlHelper = $this->Database->DataProvider->SqlHelper;
        $table     = $sqlHelper->delimitIdentifier($this->Table, $this->Schema);

        return "SELECT * FROM {$table}";
    }

    public function getCreateScript(): string
    {
        $table = new CreateTable($this->Schema, 'MigrationHistory');
        $table->column('Id')->type('bigint')->nullable(false);
        $table->column('Name')->type('varchar(45)')->nullable(false);
        $table->primaryKey('Id');

        $migrationGenerator = new $this->Database->DataProvider->MigrationGenerator;
        $migrationGenerator->visit($table);

        return $migrationGenerator->__toString();
    }

    public function getInsertScript(string $id, string $name): string
    {
        $data = new InsertData($this->Schema, $this->Table, ['Id' => $id, 'Name' => $name]);
        $migrationGenerator = new $this->Database->DataProvider->MigrationGenerator;
        $migrationGenerator->visit($data);

        return $migrationGenerator->__toString();
    }

    public function getDeleteScript(string $id): string
    {
        $data = new DeleteData($this->Schema, $this->Table, ['Id' => $id]);
        $migrationGenerator = new $this->Database->DataProvider->MigrationGenerator;
        $migrationGenerator->visit($data);

        return $migrationGenerator->__toString();
    }

    public function getIterator(): Enumerator
    {
        return new Enumerator($this->Migrations);
    }
}
