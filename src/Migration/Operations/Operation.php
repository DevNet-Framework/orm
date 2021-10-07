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

    public static function createTable(string $name, ?string $schema = null): CreateTable
    {
        return new CreateTable($name, $schema);
    }

    public static function alterTable(string $name, ?string $schema = null): AlterTable
    {
        return new AlterTable($name, $schema);
    }

    public static function renameTable(string $name, string $rename, ?string $schema = null): renameTable
    {
        return new renameTable($name, $rename, $schema);
    }

    public static function dropTable(string $name, ?string $schema = null): dropTable
    {
        return new dropTable($name, $schema);
    }
}
