<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

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
