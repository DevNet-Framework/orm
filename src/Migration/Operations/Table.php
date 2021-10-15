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
    protected ?string $Schema;
    protected string $Name;
    protected array $Columns = [];
    protected array $Constraints = [];

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function __construct(?string $schema, string $name)
    {        
        $this->Schema = $schema;
        $this->Name   = $name;
    }
}
