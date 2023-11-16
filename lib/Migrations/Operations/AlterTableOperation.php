<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migrations\Operations;

class AlterTableOperation extends TableOperation
{
    public function addColumn(string $name, string $type, ?int $max = null, ?int $scale = null): AddColumnOperation
    {
        $column = new AddColumnOperation($this->Name, $name, $type, $max, $scale);
        $this->Columns[] = $column;
        return $column;
    }

    public function AlterColumn(string $name, string $type, ?int $max = null, ?int $scale = null): AlterColumnOperation
    {
        $column = new AlterColumnOperation($this->Name, $name, $type, $max, $scale);
        $this->Columns[] = $column;
        return $column;
    }

    public function DropColumn(string $name): void
    {
        $this->Columns[] = new DropColumnOperation($this->Name, $name);
    }

    public function RenameColumn(string $name, string $rename): void
    {
        $this->Columns[] = new RenameColumnOperation($this->Name, $name, $rename);
    }

    public function addPrimaryKey(string ...$columns): AddPrimaryKeyOperation
    {
        $primaryKey = new AddPrimaryKeyOperation($this->Name, $columns);
        $this->Constraints[] = $primaryKey;
        return $primaryKey;
    }

    public function dropPrimaryKey(string $constraint = null): void
    {
        if ($constraint) {
            $primaryKey = new DropPrimaryKeyOperation($this->Name, []);
            $primaryKey->constraint($constraint);
            $this->Constraints[] = $primaryKey;
        } else {
            $this->Constraints[] = new DropPrimaryKeyOperation($this->Name, []);
        }
    }

    public function addForeignKey(string $column, string $referencedTable, string $referencedColumn, string $constraint = null): void
    {
        $this->Constraints[] = new AddForeignKeyOperation($this->Name, $column, $referencedTable, $referencedColumn, $constraint);
    }

    public function dropForeignKey(string $constraint): void
    {
        $this->Constraints[] = new DropForeignKeyOperation($this->Name, $constraint);
    }

    public function addUniqueConstraint(string ...$columns): AddUniqueConstraintOperation
    {
        $unique = new AddUniqueConstraintOperation($this->Name, $columns);
        $this->Constraints[] = $unique;
        return $unique;
    }

    public function dropUniqueConstraint(string $constraint): void
    {
        $this->Constraints[] = new dropUniqueConstraintOperation($this->Name, $constraint);
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitAlterTable($this);
    }
}