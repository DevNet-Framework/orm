<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class RenameColumn extends Column
{
    public string $Rename;

    public function __construct(string $table, string $name, string $rename)
    {
        $this->Table  = $table;
        $this->Name   = $name;
        $this->Rename = $rename;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitRenameColumn($this);
    }
}
