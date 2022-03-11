<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity;

use DevNet\System\Exceptions\PropertyException;

interface IEntity
{
    /**
     * Only the public typed properties will be maped as fields.
     * protected, private or untyped properties, will be ignored, but can be used in your private code.
     * Single navigation property must have the type of the referenced entity
     * Collection navigation property must be of type: IList
     */
}
