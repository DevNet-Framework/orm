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
use DevNet\System\Exceptions\PropertyException;

class MySqlDataProvider implements IEntityDataProvider
{
    private DbConnection $Connection;
    private ISqlGenerationHelper $SqlHelper;
    private ExpressionVisitor $QueryGenerator;
    private OperationVisitor $MigrationGenerator;

    public function __construct(DbConnection $connection)
    {
        $this->Connection         = $connection;
        $this->SqlHelper          = new MySqlHelper();
        $this->QueryGenerator     = new MySqlQueryGenerator();
        $this->MigrationGenerator = new MySqlMigrationGenerator();
    }

    public function __get(string $name)
    {
        if (!in_array($name, ['Connection', 'SqlHelper', 'QueryGenerator', 'MigrationGenerator'])) {
            throw PropertyException::undefinedPropery(self::class, $name);
        }

        return $this->$name;
    }
}
