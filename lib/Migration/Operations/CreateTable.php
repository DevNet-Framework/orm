<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class CreateTable extends Table
{
    public function column(string $name): Column
    {
        $column = new Column($this->Name, $name);
        $this->Columns[] = $column;
        return $column;
    }

    public function primaryKey(string ...$columns): PrimaryKey
    {
        $primaryKey = new PrimaryKey($this->Name, $columns);
        $this->Constraints[] = $primaryKey;
        return $primaryKey;
    }

    public function foreignKey(string $column): ForeignKey
    {
        $foreignKey = new ForeignKey($this->Name, $column);
        $this->Constraints[] = $foreignKey;
        return $foreignKey;
    }

    public function uniqueConstraint(string ...$columns): UniqueConstraint
    {
        $unique = new UniqueConstraint($this->Name, $columns);
        $this->Constraints[] = $unique;
        return $unique;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitCreateTable($this);
    }
}
