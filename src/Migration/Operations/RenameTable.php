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

    public function __construct(string $name, string $rename)
    {
        $schema = explode(".", $name);
        $table  = array_pop($schema);
        $schema = implode(".", $schema);

        $this->Name   = $table;
        $this->Schema = $schema;
        $this->Rename = $rename;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitRenameTable($this);
    }
}
