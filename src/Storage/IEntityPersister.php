<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Entity\Storage;

use Artister\Entity\Tracking\EntityEntry;
use Artister\System\Database\DbConnection;

interface IEntityPersister
{   
    public function __construct(DbConnection $connection);

    public function insert(EntityEntry $entry);

    public function update(EntityEntry $entry);

    public function delete(EntityEntry $entry);
}