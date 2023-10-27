<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migrations\Operations;

class ForeignKeyOperation extends Operation
{
    public string $Table;
    public string $Column;
    public string $ReferencedTable;
    public string $ReferencedColumn;
    public string $Constraint;
    public ?string $OnUpdate = null;
    public ?string $OnDelete = null;

    public function __construct(string $table, string $column)
    {
        $this->Table  = $table;
        $this->Column = $column;
    }

    public function references(string $referencedTable, string $referencedColumn): ForeignKeyOperation
    {
        $this->ReferencedTable  = $referencedTable;
        $this->ReferencedColumn = $referencedColumn;
        $this->Constraint = "FK_" . $this->Table . "_" . "$referencedTable";
        return $this;
    }

    public function constraint(string $name): ForeignKeyOperation
    {
        $this->Constraint = $name;
        return $this;
    }

    public function onUpdate(string $option): ForeignKeyOperation
    {
        $this->OnUpdate = $option;
        return $this;
    }

    public function onDelete(string $option): ForeignKeyOperation
    {
        $this->OnUpdate = $option;
        return $this;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitForeignKey($this);
    }
}
