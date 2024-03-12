<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Migrations\Operations;

class CreateTableOperation extends TableOperation
{
    public function column(string $name, string $type, ?int $max = null, ?int $scale = null): ColumnOperation
    {
        $column = new ColumnOperation($this->Name, $name, $type, $max, $scale);
        $this->Columns[] = $column;
        return $column;
    }

    public function primaryKey(string ...$columns): PrimaryKeyOperation
    {
        $primaryKey = new PrimaryKeyOperation($this->Name, $columns);
        $this->Constraints[] = $primaryKey;
        return $primaryKey;
    }

    public function foreignKey(string $column): ForeignKeyOperation
    {
        $foreignKey = new ForeignKeyOperation($this->Name, $column);
        $this->Constraints[] = $foreignKey;
        return $foreignKey;
    }

    public function uniqueConstraint(string ...$columns): UniqueConstraintOperation
    {
        $unique = new UniqueConstraintOperation($this->Name, $columns);
        $this->Constraints[] = $unique;
        return $unique;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitCreateTable($this);
    }
}
