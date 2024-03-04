<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migrations\Operations;

class AddPrimaryKeyOperation extends PrimaryKeyOperation
{
    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitAddPrimaryKey($this);
    }
}
