<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Tracking;

class EntityState
{
    const Attached  = 1;
    const Added     = 2;
    const Modified  = 3;
    const Deleted   = 0;
    const Detached  = 4;
}
