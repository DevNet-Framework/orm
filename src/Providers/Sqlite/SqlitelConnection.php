<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity\Providers\Sqlite;

use Artister\System\Database\DbConnection;

class SqlitelConnection extends DbConnection
{
    public function __construct(string $connectionUrl)
    {
        $datasource = "mysql:".$connectionUrl;

        parent::__construct($datasource);
    }
}