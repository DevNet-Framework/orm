<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity\Providers\Mysql;

use Artister\System\Database\DbConnection;

class MysqlConnection extends DbConnection
{
    public function __construct(string $connectionUrl)
    {
        $segments   = parse_url($connectionUrl);
        $username   = $segments['user'] ?? "";
        $password   = $segments['pass'] ?? "";
        $host       = $segments['host'] ? "host=".$segments['host'] : "";
        $port       = $segments['port'] ? ":".$segments['port'] : "";
        $database   = $segments['path'] ? substr(strrchr($segments['path'], "/"), 1) : "";
        $options    = $segments['query'] ? str_replace("&", ";", $segments['query']).";" : "";

        $datasource = "mysql:".$host.$port.";"."dbname=".$database.";".$options;

        parent::__construct($datasource, $username, $password);
    }
}