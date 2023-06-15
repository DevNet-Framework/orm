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
class Column
{
    use PropertyTrait;

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function get_Name(): string
    {
        return $this->name;
    }
}
