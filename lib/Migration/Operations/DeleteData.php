<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class DeleteData extends Operation
{
    public ?string $Schema;
    public string $Table;
    public array $Keys;

    public function __construct(?string $schema, string $table, array $keys)
    {        
        $this->Schema = $schema;
        $this->Table  = $table;
        $this->Keys   = $keys;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitDeleteData($this);
    }
}
