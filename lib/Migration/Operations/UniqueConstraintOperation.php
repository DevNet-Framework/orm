<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class UniqueConstraintOperation extends Operation
{
    public string $Table;
    public array $Columns;
    public string $Constraint;

    public function __construct(string $table, array $columns)
    {
        $this->Table = $table;
        $this->Columns = $columns;
        $columns = implode(',', $columns);
        $this->Constraint = "UQ_" . $table . "_" . "$columns";
    }

    public function constraint(string $name)
    {
        $this->Constraint = $name;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitUniqueConstraint($this);
    }
}
