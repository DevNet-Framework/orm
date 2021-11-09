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

    public static function createTable(?string $schema, string $name): CreateTable
    {
        return new CreateTable($schema, $name);
    }

    public static function alterTable(?string $schema, string $name): AlterTable
    {
        return new AlterTable($schema, $name);
    }

    public static function renameTable(?string $schema, string $name, string $rename): RenameTable
    {
        return new RenameTable($schema, $name, $rename);
    }

    public static function dropTable(?string $schema, string $name): DropTable
    {
        return new DropTable($schema, $name);
    }

    public static function insertData(?string $schema, string $name, array $columns): InsertData
    {
        return new InsertData($schema, $name, $columns);
    }

    public static function updateData(?string $schema, string $name, array $columns, array $keys): UpdateData
    {
        return new UpdateData($schema, $name, $columns, $keys);
    }

    public static function deleteData(?string $schema, string $name, array $keys): DeleteData
    {
        return new DeleteData($schema, $name, $keys);
    }
}
