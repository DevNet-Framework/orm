<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Tracking;

enum EntityState: int
{
    case Detached  = 0;
    case Attached  = 1;
    case Added     = 2;
    case Deleted   = 3;
    case Modified  = 4;
}
