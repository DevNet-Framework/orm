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
    protected string $Table;
    protected string $Name;
    protected string $Type;
    protected bool $Nullable = true;
    protected bool $Identity = false; // auto increment
    protected $Default = null;

    public function __construct(string $table, string $name)
    {
        $this->Table = $table;
        $this->Name  = $name;
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function type(string $type): Column
    {
        $this->Type = strtolower($type);
        return $this;
    }

    public function notNull(): Column
    {
        $this->Nullable = false;
        return $this;
    }

    public function identity(): Column
    {
        $this->Identity = true;
        return $this;
    }

    public function default($value): Column
    {
        $this->Default = $value;
        return $this;
    }

    public function accept(OperationVisitor $visitor): void
    {
        $visitor->visitColumn($this);
    }
}
