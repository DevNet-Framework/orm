<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migrations\Operations;

use DevNet\System\Exceptions\MethodException;

class RenameColumnOperation extends ColumnOperation
{
    public string $Rename;

    public function __construct(string $table, string $name, string $rename)
    {
        $this->Table  = $table;
        $this->Name   = $name;
        $this->Rename = $rename;
    }

    public function nullable(bool $nullable = true): ColumnOperation
    {
        throw new MethodException("The Method nullable() Not Applicable");
    }

    public function default($value): ColumnOperation
    {
        throw new MethodException("The Method default() Not Applicable");
    }

    public function identity(): ColumnOperation
    {
        throw new MethodException("The Method identity() Not Applicable");
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitRenameColumn($this);
    }
}
