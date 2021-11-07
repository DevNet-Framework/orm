<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class ForeignKey extends Operation
{
    protected string $Table;
    protected string $Column;
    protected string $ReferencedTable;
    protected string $ReferencedColumn;
    protected string $Constraint;
    protected ?string $OnUpdate = null;
    protected ?string $OnDelete = null;

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function __construct(string $table, string $column)
    {
        $this->Table  = $table;
        $this->Column = $column;
    }

    public function references(string $referencedTable, string $referencedColumn): ForeignKey
    {
        $this->ReferencedTable  = $referencedTable;
        $this->ReferencedColumn = $referencedColumn;
        $this->Constraint = "FK_" . $this->Table . "_" . "$referencedTable";
        return $this;
    }

    public function constraint(string $name): ForeignKey
    {
        $this->Constraint = $name;
        return $this;
    }

    public function onUpdate(string $option): ForeignKey
    {
        $this->OnUpdate = $option;
        return $this;
    }

    public function onDelete(string $option): ForeignKey
    {
        $this->OnUpdate = $option;
        return $this;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitForeignKey($this);
    }
}
