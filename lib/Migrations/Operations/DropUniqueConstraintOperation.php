<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Migrations\Operations;

class DropUniqueConstraintOperation extends UniqueConstraintOperation
{
    public function __construct(string $table, string $constraint)
    {
        $this->Table      = $table;
        $this->Constraint = $constraint;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitDropUniqueConstraint($this);
    }
}
