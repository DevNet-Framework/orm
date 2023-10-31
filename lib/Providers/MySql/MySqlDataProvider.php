<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\MySql;

use DevNet\Entity\Storage\IEntityDataProvider;
use DevNet\System\Database\MySql\MySqlConnection;
use DevNet\System\PropertyTrait;

class MySqlDataProvider implements IEntityDataProvider
{
    use PropertyTrait;

    private MySqlConnection $connection;
    private MySqlHelper $sqlHelper;
    private MySqlQueryGenerator $queryGenerator;
    private MySqlMigrationGenerator $migrationGenerator;

    public function __construct(string $connectionUrl)
    {
        $this->connection         = new MySqlConnection($connectionUrl);
        $this->sqlHelper          = new MySqlHelper();
        $this->queryGenerator     = new MySqlQueryGenerator();
        $this->migrationGenerator = new MySqlMigrationGenerator();
    }

    public function get_Connection(): MySqlConnection
    {
        return $this->connection;
    }

    public function get_SqlHelper(): MySqlHelper
    {
        return $this->sqlHelper;
    }

    public function get_QueryGenerator(): MySqlQueryGenerator
    {
        return $this->queryGenerator;
    }

    public function get_MigrationGenerator(): MySqlMigrationGenerator
    {
        return $this->migrationGenerator;
    }
}
