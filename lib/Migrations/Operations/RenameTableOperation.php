<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Migrations\Operations;

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
