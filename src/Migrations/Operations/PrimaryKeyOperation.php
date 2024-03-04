<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migrations\Operations;

class PrimaryKeyOperation extends Operation
{
    public string $Table;
    public array $Columns;
    public string $Constraint;

    public function __construct(string $table, array $columns)
    {
        $this->Table      = $table;
        $this->Columns    = $columns;
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
