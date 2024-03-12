<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Migrations\Operations;

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
