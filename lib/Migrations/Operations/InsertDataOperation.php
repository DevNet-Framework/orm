<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Migrations\Operations;

class InsertDataOperation extends Operation
{
    public string $Table;
    public array $Columns;
    public ?string $Schema;

    public function __construct(string $table, array $columns, ?string $schema = null)
    {
        $this->Table   = $table;
        $this->Columns = $columns;
        $this->Schema  = $schema;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitInsertData($this);
    }
}
