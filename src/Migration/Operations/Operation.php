<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

abstract class Operation
{
    abstract public function accept(OperationVisitor $expressionVisitor): void;

    public static function createTable(string $name): CreateTable
    {
        return new CreateTable($name);
    }

    public static function alterTable(string $name): AlterTable
    {
        return new AlterTable($name);
    }

    public static function renameTable(string $name, string $rename): renameTable
    {
        return new renameTable($name, $rename);
    }

    public static function dropTable(string $name): dropTable
    {
        return new dropTable($name);
    }
}
