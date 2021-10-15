<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

use DevNet\Entity\Migration\Operations\Operation;
use Closure;

class MigrationBuilder
{
    protected ?string $Schema;
    protected array $Operations = [];

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function __construct(?string $schema = null)
    {
        $this->Schema = $schema;
    }

    public function createTable(string $name, Closure $builder): void
    {
        $table = Operation::createTable($this->Schema, $name);
        $builder($table);
        $this->Operations[] = $table;
    }

    public function alterTable(string $name, Closure $builder): void
    {
        $table = Operation::alterTable($this->Schema, $name);
        $builder($table);
        $this->Operations[] = $table;
    }

    public function RenameTable(string $name, string $rename): void
    {
        $this->Operations[] = Operation::renameTable($this->Schema, $name, $rename);
    }

    public function dropTable(string $name): void
    {
        $this->Operations[] = Operation::dropTable($this->Schema, $name);
    }

    public function insertData(string $table, array $columns): void
    {
        $this->Operations[] = Operation::insertData($this->Schema, $table, $columns);
    }

    public function updateData(string $table, array $columns, array $keys): void
    {
        $this->Operations[] = Operation::updateData($this->Schema, $table, $columns, $keys);
    }

    public function deleteData(string $table, array $keys): void
    {
        $this->Operations[] = Operation::deleteData($this->Schema, $table, $keys);
    }
}
