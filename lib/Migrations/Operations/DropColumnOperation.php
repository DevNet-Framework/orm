<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migrations\Operations;

use DevNet\System\Exceptions\MethodException;

class DropColumnOperation extends ColumnOperation
{
    public function __construct(string $table, string $name)
    {
        $this->Table = $table;
        $this->Name  = $name;
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
        $visitor->visitDropColumn($this);
    }
}
