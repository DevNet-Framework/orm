<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Sqlite;

use DevNet\Entity\Storage\IEntityDataProvider;
use DevNet\System\Database\Sqlite\SqliteConnection;
use DevNet\System\PropertyTrait;

class SqliteDataProvider implements IEntityDataProvider
{
    use PropertyTrait;

    private SqliteConnection $connection;
    private SqliteHelper $sqlHelper;
    private SqliteQueryGenerator $queryGenerator;
    private SqliteMigrationGenerator $migrationGenerator;

    public function __construct(string $connectionString)
    {
        $this->connection         = new SqliteConnection($connectionString);
        $this->sqlHelper          = new SqliteHelper();
        $this->queryGenerator     = new SqliteQueryGenerator();
        $this->migrationGenerator = new SqliteMigrationGenerator();
    }

    public function get_Connection(): SqliteConnection
    {
        return $this->connection;
    }

    public function get_SqlHelper(): SqliteHelper
    {
        return $this->sqlHelper;
    }

    public function get_QueryGenerator(): SqliteQueryGenerator
    {
        return $this->queryGenerator;
    }

    public function get_MigrationGenerator(): SqliteMigrationGenerator
    {
        return $this->migrationGenerator;
    }
}
