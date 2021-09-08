<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

use DevNet\Entity\Migration\Schema;

abstract class Operation
{
    abstract public function accept(OperationVisitor $expressionVisitor): void;

    public static function createSchema(): Schema
    {
        return new Schema();
    }
}
