<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Providers\PostgreSql;

use DevNet\System\Database\DbConnection;

class PostgreSqlConnection extends DbConnection
{
    public function __construct(string $connectionUrl)
    {
        $username = parse_url($connectionUrl, PHP_URL_USER);
        $password = parse_url($connectionUrl, PHP_URL_PASS);
        $host     = parse_url($connectionUrl, PHP_URL_HOST);
        $port     = parse_url($connectionUrl, PHP_URL_PORT);
        $path     = parse_url($connectionUrl, PHP_URL_PATH);
        $query    = parse_url($connectionUrl, PHP_URL_QUERY);

        $username = $username ? $username : "";
        $password = $password ? $password : "";
        $host     = $host ? "host=" . $host : "";
        $port     = $port ? "," . $port : "";
        $database = $path ? substr(strrchr($path, "/"), 1) : "";
        $options  = $query ? str_replace("&", ";", $query) . ";" : "";

        $datasource = "pgsql:" . $host . $port . ";" . "dbname=" . $database . ";" . $options;

        parent::__construct($datasource, $username, $password);
    }
}
