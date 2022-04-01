<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Providers\Sqlite;

use DevNet\Entity\Providers\IEntityDataProvider;
use DevNet\Entity\Storage\ISqlGenerationHelper;
use DevNet\Entity\Migration\Operations\OperationVisitor;
use DevNet\System\Compiler\Expressions\ExpressionVisitor;
use DevNet\System\Database\DbConnection;
use DevNet\System\Exceptions\PropertyException;

class SqliteDataProvider implements IEntityDataProvider
{
    private DbConnection $connection;
    private ISqlGenerationHelper $sqlHelper;
    private ExpressionVisitor $queryGenerator;
    private OperationVisitor $migrationGenerator;

    public function __get(string $name)
    {
        if (in_array($name, ['Connection', 'SqlHelper', 'QueryGenerator', 'MigrationGenerator'])) {
            $property = lcfirst($name);
            return $this->$property;
        }

        if (property_exists($this, $name)) {
            throw new PropertyException("access to private property" . get_class($this) . "::" . $name);
        }

        throw new PropertyException("access to undefined property" . get_class($this) . "::" . $name);
    }

    public function __construct(DbConnection $connection)
    {
        $this->connection         = $connection;
        $this->sqlHelper          = new SqliteHelper();
        $this->queryGenerator     = new SqliteQueryGenerator();
        $this->migrationGenerator = new SqliteMigrationGenerator();
    }
}
