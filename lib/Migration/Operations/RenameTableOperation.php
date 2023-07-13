<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class RenameTableOperation extends TableOperation
{
    public string $Rename;

    public function __construct(string $name, string $rename, ?string $schema = null)
    {
        $this->Name   = $name;
        $this->Rename = $rename;
        $this->Schema = $schema;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitRenameTable($this);
    }
}
