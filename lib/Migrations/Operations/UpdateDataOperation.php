<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migrations\Operations;

class UpdateDataOperation extends Operation
{
    public string $Table;
    public array $Columns;
    public array $Keys;
    public ?string $Schema = null;

    public function __construct(string $table, array $columns, array $keys, ?string $schema = null)
    {
        $this->Table   = $table;
        $this->Columns = $columns;
        $this->Keys    = $keys;
        $this->Schema  = $schema;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitUpdateData($this);
    }
}
