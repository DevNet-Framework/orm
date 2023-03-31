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
