<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Migrations\Operations;

class AlterColumnOperation extends ColumnOperation
{
    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitAlterColumn($this);
    }
}
