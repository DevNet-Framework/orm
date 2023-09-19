<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Providers\PgSql;

use DevNet\Entity\Storage\IEntityDataProvider;
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
