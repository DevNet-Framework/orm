<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

use DevNet\Entity\Migration\Operations\Operation;
use DevNet\System\PropertyTrait;
use Closure;

class MigrationBuilder
{
    use PropertyTrait;

    private array $operations = [];

    public function get_Operations(): array
    {
        return $this->operations;
    }

    public function createTable(string $name, Closure $builder, ?string $schema = null): void
    {
        $table = Operation::createTable($name, $schema);
        $builder($table);
        $this->operations[] = $table;
    }

    public function alterTable(string $name, Closure $builder, ?string $schema = null): void
    {
        $table = Operation::alterTable($name, $schema);
        $builder($table);
        $this->operations[] = $table;
    }

    public function RenameTable(string $name, string $rename, ?string $schema = null): void
    {
        $this->operations[] = Operation::renameTable($name, $rename, $schema);
    }

    public function dropTable(string $name, ?string $schema = null): void
    {
        $this->operations[] = Operation::dropTable($name, $schema);
    }

    public function insertData(string $table, array $columns, ?string $schema = null): void
    {
        $this->operations[] = Operation::insertData($table, $columns, $schema);
    }

    public function updateData(string $table, array $columns, array $keys, ?string $schema = null): void
    {
        $this->operations[] = Operation::updateData($table, $columns, $keys, $schema);
    }

    public function deleteData(string $table, array $keys = [], ?string $schema = null): void
    {
        $this->operations[] = Operation::deleteData($table, $keys, $schema);
    }
}
