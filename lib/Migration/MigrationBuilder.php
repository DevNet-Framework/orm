<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

use DevNet\Entity\Migration\Operations\Operation;
use DevNet\System\ObjectTrait;
use Closure;

class MigrationBuilder
{
    use ObjectTrait;

    private ?string $schema;
    private array $operations = [];

    public function __construct(?string $schema = null)
    {
        $this->schema = $schema;
    }

    public function get_Operations(): array
    {
        return $this->operations;
    }

    public function createTable(string $name, Closure $builder): void
    {
        $table = Operation::createTable($this->schema, $name);
        $builder($table);
        $this->operations[] = $table;
    }

    public function alterTable(string $name, Closure $builder): void
    {
        $table = Operation::alterTable($this->schema, $name);
        $builder($table);
        $this->operations[] = $table;
    }

    public function RenameTable(string $name, string $rename): void
    {
        $this->operations[] = Operation::renameTable($this->schema, $name, $rename);
    }

    public function dropTable(string $name): void
    {
        $this->operations[] = Operation::dropTable($this->schema, $name);
    }

    public function insertData(string $table, array $columns): void
    {
        $this->operations[] = Operation::insertData($this->schema, $table, $columns);
    }

    public function updateData(string $table, array $columns, array $keys): void
    {
        $this->operations[] = Operation::updateData($this->schema, $table, $columns, $keys);
    }

    public function deleteData(string $table, array $keys = []): void
    {
        $this->operations[] = Operation::deleteData($this->schema, $table, $keys);
    }
}
