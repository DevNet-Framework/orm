<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Migrations;

use DevNet\System\Linq;
use DevNet\ORM\Migrations\Operations\Operation;
use DevNet\ORM\Storage\EntityDatabase;
use DevNet\System\Collections\Enumerator;
use DevNet\System\Collections\IEnumerable;
use DevNet\System\MethodTrait;
use DevNet\System\PropertyTrait;

class MigrationHistory implements IEnumerable
{
    use MethodTrait;
    use PropertyTrait;

    private EntityDatabase $database;
    private string $table     = 'MigrationHistory';
    private array $migrations = [];
    private bool $existence   = false;

    public function __construct(EntityDatabase $database, MigrationAssembly $assembly)
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
                $id =  $dbReader->getValue("Id");
                $migration = $assembly->where(fn ($migration) => $migration->Id == $id)->first();
                if (!$migration) {
                    $connection->close();
                    throw new \Exception("Could not find migration: $id");
                }
                $this->migrations[] = $migration;
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
        $table->column('Version', 'varchar(15)')->nullable(false);
        $table->primaryKey('Id');

        $migrationGenerator = new $this->database->DataProvider->MigrationGenerator;
        $migrationGenerator->visit($table);

        return $migrationGenerator->__toString();
    }

    public function getInsertScript(string $id): string
    {
        $data = Operation::insertData($this->table, ['Id' => $id, 'Version' => '1.0.0']);
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
