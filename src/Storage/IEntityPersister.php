<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Storage;

use DevNet\Entity\Tracking\EntityEntry;
use DevNet\System\Database\DbConnection;

interface IEntityPersister
{   
    public function __construct(DbConnection $connection);

    public function insert(EntityEntry $entry) : int;

    public function update(EntityEntry $entry) : int;

    public function delete(EntityEntry $entry) : int;
}
