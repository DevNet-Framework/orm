<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

class Column extends Operation
{
    public string $Table;
    public string $Name;
    public string $Type;
    public ?int $Max;
    public ?int $Scale;
    public bool $Nullable = false;
    public bool $Identity = false; // auto increment
    public $Default = null;

    public function __construct(string $table, string $name, string $type, ?int $max = null, ?int $scale = null)
    {
        $this->Table = $table;
        $this->Name  = $name;
        $this->Type  = strtolower($type);
        $this->Max   = $max;
        $this->Scale = $scale;
    }

    public function nullable(bool $nullable = true): Column
    {
        $this->Nullable = $nullable;
        return $this;
    }

    public function default($value): Column
    {
        $this->Default = $value;
        return $this;
    }

    public function identity(): Column
    {
        $this->Identity = true;
        $this->Nullable = false;
        return $this;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitColumn($this);
    }
}
