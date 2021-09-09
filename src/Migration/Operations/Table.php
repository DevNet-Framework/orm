<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

abstract class Table extends Operation
{
    protected string $Name;
    protected string $Schema;
    protected array $Columns = [];
    protected array $Constraints = [];

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function __construct(string $name)
    {
        $schema = explode(".", $name);
        $table  = array_pop($schema);
        $schema = implode(".", $schema);

        $this->Name = $table;
        $this->Schema = $schema;
    }
}
