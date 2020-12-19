<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity;

use Artister\System\Exceptions\PropertyException;

interface IEntity
{
    /**
     * @return mixed return the field value
     * @throws PropertyException undefined Propery | private Property | protected Property
     * throwing PropertyException is optionnel.
     */
    public function __get(string $name);

    /**
     * set the field value
     * @throws PropertyException Invalid Value Type | private Property | protected Property
     * throwing PropertyException is optionnel.
     */
    public function __set(string $name, $value);
}