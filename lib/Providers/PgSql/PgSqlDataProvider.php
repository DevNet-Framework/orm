<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\PgSql;

use DevNet\ORM\Storage\IEntityDataProvider;
use DevNet\System\Database\PgSql\PgSqlConnection;
use DevNet\System\PropertyTrait;

class PgSqlDataProvider implements IEntityDataProvider
{
    use PropertyTrait;

    private PgSqlConnection $connection;
    private PgSqlHelper $sqlHelper;
    private PgSqlQueryGenerator $queryGenerator;
    private PgSqlMigrationGenerator $migrationGenerator;

    public function __construct(string $connectionString)
    {
        $this->connection         = new PgSqlConnection($connectionString);
        $this->sqlHelper          = new PgSqlHelper();
        $this->queryGenerator     = new PgSqlQueryGenerator();
        $this->migrationGenerator = new PgSqlMigrationGenerator();
    }

    public function get_Connection(): PgSqlConnection
    {
        return $this->connection;
    }

    public function get_SqlHelper(): PgSqlHelper
    {
        return $this->sqlHelper;
    }

    public function get_QueryGenerator(): PgSqlQueryGenerator
    {
        return $this->queryGenerator;
    }

    public function get_MigrationGenerator(): PgSqlMigrationGenerator
    {
        return $this->migrationGenerator;
    }
}
