<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration\Operations;

abstract class TableOperation extends Operation
{
    public string $Name;
    public ?string $Schema = null;
    public array $Columns = [];
    public array $Constraints = [];

    public function __construct(string $name, ?string $schema = null)
    {
        $this->Name = $name;
        $this->Schema = $schema;
    }
}
