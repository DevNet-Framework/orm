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

    public static function createTable(string $name, ?string $schema = null): CreateTableOperation
    {
        return new CreateTableOperation($name, $schema);
    }

    public static function alterTable(string $name, ?string $schema = null): AlterTableOperation
    {
        return new AlterTableOperation($name, $schema);
    }

    public static function renameTable(string $name, string $rename, ?string $schema = null): RenameTableOperation
    {
        return new RenameTableOperation($name, $rename, $schema);
    }

    public static function dropTable(string $name, ?string $schema = null): DropTableOperation
    {
        return new DropTableOperation($name, $schema);
    }

    public static function insertData(string $name, array $columns, ?string $schema = null): InsertDataOperation
    {
        return new InsertDataOperation($name, $columns, $schema);
    }

    public static function updateData(string $name, array $columns, array $keys, ?string $schema = null): UpdateDataOperation
    {
        return new UpdateDataOperation($name, $columns, $keys, $schema);
    }

    public static function deleteData(string $name, array $keys, ?string $schema = null): DeleteDataOperation
    {
        return new DeleteDataOperation($name, $keys, $schema);
    }
}
