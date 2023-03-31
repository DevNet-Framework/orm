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

    public static function renameTable(string $name, string $rename, ?string $schema = null): RenameTable
    {
        return new RenameTable($name, $rename, $schema);
    }

    public static function dropTable(string $name, ?string $schema = null): DropTable
    {
        return new DropTable($name, $schema);
    }

    public static function insertData(string $name, array $columns, ?string $schema = null): InsertData
    {
        return new InsertData($name, $columns, $schema);
    }

    public static function updateData(string $name, array $columns, array $keys, ?string $schema = null): UpdateData
    {
        return new UpdateData($name, $columns, $keys, $schema);
    }

    public static function deleteData(string $name, array $keys, ?string $schema = null): DeleteData
    {
        return new DeleteData($name, $keys, $schema);
    }
}
