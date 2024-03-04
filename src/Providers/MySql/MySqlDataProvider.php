<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
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
