<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class RenameTable extends Table
{
    protected string $Rename;

    public function __construct(?string $schema, string $name, string $rename)
    {
        $this->Schema = $schema;
        $this->Name   = $name;
        $this->Rename = $rename;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitRenameTable($this);
    }
}
