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
    protected ?string $Schema = null;
    protected string $Name;
    protected array $Columns = [];
    protected array $Constraints = [];

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function __construct(string $name, ?string $schema = null)
    {        
        $this->Name   = $name;
        $this->Schema = $schema;
    }
}
