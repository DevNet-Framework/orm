<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Providers\MySql;

use DevNet\Entity\Migration\Operations\OperationVisitor;
use DevNet\Entity\Storage\IEntityDataProvider;
use DevNet\Entity\Storage\IEntityPersister;
use DevNet\System\Database\DbConnection;
use DevNet\System\Exceptions\PropertyException;
use DevNet\System\Compiler\Expressions\ExpressionVisitor;

class MySqlDataProvider implements IEntityDataProvider
{
    private string $Name = 'MySql';
    private DbConnection $Connection;
    private IEntityPersister $Persister;
    private ExpressionVisitor $Visitor;
    private OperationVisitor $SchemaGenerator;

    public function __construct(DbConnection $connection)
    {
        $this->Connection      = $connection;
        $this->Persister       = new MySqlEntityPersister($connection);
        $this->Visitor         = new MySqlQueryTranslator();
        $this->SchemaGenerator = new MySqlSchemaGenerator();
    }

    public function __get(string $name)
    {
        if (!in_array($name, ['Name', 'Connection', 'Persister', 'Visitor', 'SchemaGenerator'])) {
            throw PropertyException::undefinedPropery(self::class, $name);
        }

        return $this->$name;
    }
}
