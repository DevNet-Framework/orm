<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class AlterTable extends Table
{
    public function addColumn(string $name, string $type, ?int $max = null, ?int $scale = null): AddColumn
    {
        $column = new AddColumn($this->Name, $name, $type, $max, $scale);
        $this->Columns[] = $column;
        return $column;
    }

    public function AlterColumn(string $name, string $type, ?int $max = null, ?int $scale = null): AlterColumn
    {
        $column = new AlterColumn($this->Name, $name, $type, $max, $scale);
        $this->Columns[] = $column;
        return $column;
    }

    public function DropColumn(string $name): void
    {
        $this->Columns[] = new DropColumn($this->Name, $name);
    }

    public function RenameColumn(string $name, string $rename): void
    {
        $this->Columns[] = new RenameColumn($this->Name, $name, $rename);
    }

    public function addPrimaryKey(string ...$columns): AddPrimaryKey
    {
        $primaryKey = new AddPrimaryKey($this->Name, $columns);
        $this->Constraints[] = $primaryKey;
        return $primaryKey;
    }

    public function dropPrimaryKey(string $constraint = null): void
    {
        if ($constraint) {
            $primaryKey = new DropPrimaryKey($this->Name, []);
            $primaryKey->constraint($constraint);
            $this->Constraints[] = $primaryKey;
        } else {
            $this->Constraints[] = new DropPrimaryKey($this->Name, []);
        }
    }

    public function addForeignKey(string $column, string $referencedTable, string $referencedColumn, string $constraint = null): void
    {
        $this->Constraints[] = new AddForeignKey($this->Name, $column, $referencedTable, $referencedColumn, $constraint);
    }

    public function dropForeignKey(string $constraint): void
    {
        $this->Constraints[] = new DropForeignKey($this->Name, $constraint);
    }

    public function addUniqueConstraint(string ...$columns): AddUniqueConstraint
    {
        $unique = new AddUniqueConstraint($this->Name, $columns);
        $this->Constraints[] = $unique;
        return $unique;
    }

    public function dropUniqueConstraint(string $constraint): void
    {
        $this->Constraints[] = new dropUniqueConstraint($this->Name, $constraint);
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitAlterTable($this);
    }
}
