<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Migrations\Operations;

class DeleteDataOperation extends Operation
{
    public string $Table;
    public array $Keys;
    public ?string $Schema = null;

    public function __construct(string $table, array $keys, ?string $schema = null)
    {
        $this->Table  = $table;
        $this->Keys   = $keys;
        $this->Schema = $schema;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitDeleteData($this);
    }
}
