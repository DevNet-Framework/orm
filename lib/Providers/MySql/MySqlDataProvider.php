<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Providers\MySql;

use DevNet\Entity\Providers\IEntityDataProvider;
use DevNet\Entity\Storage\ISqlGenerationHelper;
use DevNet\Entity\Migration\Operations\OperationVisitor;
use DevNet\System\Compiler\Expressions\ExpressionVisitor;
use DevNet\System\Database\DbConnection;
use DevNet\System\ObjectTrait;

class MySqlDataProvider implements IEntityDataProvider
{
    use ObjectTrait;

    private DbConnection $connection;
    private ISqlGenerationHelper $sqlHelper;
    private ExpressionVisitor $queryGenerator;
    private OperationVisitor $migrationGenerator;

    public function __construct(string $connectionString)
    {
        $this->connection         = new MySqlConnection($connectionString);
        $this->sqlHelper          = new MySqlHelper();
        $this->queryGenerator     = new MySqlQueryGenerator();
        $this->migrationGenerator = new MySqlMigrationGenerator();
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
