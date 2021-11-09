<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class PrimaryKey extends Operation
{
    protected string $Table;
    protected array $Columns;
    protected string $Constraint;

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function __construct(string $table, array $columns)
    {
        $this->Table  = $table;
        $this->Columns = $columns;
        $this->Constraint = "PK_" . $table;
    }

    public function constraint(string $name): void
    {
        $this->Constraint = $name;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitPrimaryKey($this);
    }
}
