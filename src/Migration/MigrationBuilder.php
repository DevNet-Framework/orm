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
    protected array $Operations = [];

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function createTable(string $tableName, Closure $builder): void
    {
        $table = Operation::createTable($tableName);
        $builder($table);
        $this->Operations[$tableName] = $table;
    }

    public function alterTable(string $tableName, Closure $builder): void
    {
        $table = Operation::alterTable($tableName);
        $builder($table);
        $this->Operations[$tableName] = $table;
    }

    public function RenameTable(string $tableName, string $rename): void
    {
        $this->Operations[$tableName] = Operation::renameTable($tableName, $rename);
    }

    public function dropTable(string $tableName): void
    {
        $this->Operations[$tableName] = Operation::dropTable($tableName);
    }
}
