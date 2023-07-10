<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class AddColumnOperation extends ColumnOperation
{
    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitAddColumn($this);
    }
}
