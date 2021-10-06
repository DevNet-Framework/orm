<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class DropUniqueConstraint extends UniqueConstraint
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