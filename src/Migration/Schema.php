<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

use DevNet\Entity\Migration\Operations\AlterTable;
use DevNet\Entity\Migration\Operations\CreateTable;
use DevNet\Entity\Migration\Operations\DropTable;
use DevNet\Entity\Migration\Operations\Operation;
use DevNet\Entity\Migration\Operations\OperationVisitor;
use DevNet\Entity\Migration\Operations\RenameTable;

class Schema extends Operation
{
    protected array $Tables = [];

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitSchema($this);
    }

    public function createTable(string $tableName): CreateTable
    {
        $table = new createTable($tableName);
        $this->Tables[$tableName] = $table;
        return $table;
    }

    public function RenameTable(string $tableName, string $rename): void
    {
        $this->Tables[$tableName] = new RenameTable($tableName, $rename);
    }

    public function alterTable(string $tableName): AlterTable
    {
        $table = new AlterTable($tableName);
        $this->Tables[$tableName] = $table;
        return $table;
    }

    public function dropTable(string $tableName): void
    {
        $this->Tables[$tableName] = new DropTable($tableName);
    }
}
