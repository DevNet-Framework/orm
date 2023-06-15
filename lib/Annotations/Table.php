<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Annotations;

use DevNet\System\PropertyTrait;
use Attribute;

#[Attribute]
class Table
{
    use PropertyTrait;

    private string $name;
    private ?string $schema;

    public function __construct(string $name, ?string $schema = null)
    {
        $this->name = $name;
        $this->schema = $schema;
    }

    public function get_Name(): string
    {
        return $this->name;
    }

    public function get_Schema(): ?string
    {
        return $this->schema;
    }
}
