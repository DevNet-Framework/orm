<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Providers\PostgreSql;

use DevNet\Entity\Migration\Operations\OperationVisitor;
use DevNet\Entity\Providers\IEntityDataProvider;
use DevNet\Entity\Storage\ISqlGenerationHelper;
use DevNet\System\Compiler\Expressions\ExpressionVisitor;
use DevNet\System\Database\DbConnection;
use DevNet\System\PropertyTrait;

class PostgreSqlDataProvider implements IEntityDataProvider
{
    use PropertyTrait;

    private DbConnection $connection;
    private ISqlGenerationHelper $sqlHelper;
    private ExpressionVisitor $queryGenerator;
    private OperationVisitor $migrationGenerator;

    public function __construct(string $connectionString)
    {
        $this->connection         = new PostgreSqlConnection($connectionString);
        $this->sqlHelper          = new PostgreSqlHelper();
        $this->queryGenerator     = new PostgreSqlQueryGenerator();
        $this->migrationGenerator = new PostgreSqlMigrationGenerator();
    }

    public function get_Connection(): DbConnection
    {
        return $this->connection;
    }

    public function get_SqlHelper(): ISqlGenerationHelper
    {
        return $this->sqlHelper;
    }

    public function get_QueryGenerator(): ExpressionVisitor
    {
        return $this->queryGenerator;
    }
    
    public function get_MigrationGenerator(): OperationVisitor
    {
        return $this->migrationGenerator;
    }
}
