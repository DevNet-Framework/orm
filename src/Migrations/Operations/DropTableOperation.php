<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migrations\Operations;

class DropTableOperation extends TableOperation
{
    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitDropTable($this);
    }
}
