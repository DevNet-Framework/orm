<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Tracking;

enum EntityState: int
{
    case Detached  = 0;
    case Attached  = 1;
    case Added     = 2;
    case Deleted   = 3;
    case Modified  = 4;
}
