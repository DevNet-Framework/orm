<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Entity\Providers\Postgresql;

use Artister\Entity\Storage\IEntityDataProvider;
use Artister\Entity\Storage\IEntityPersister;
use Artister\System\Database\DbConnection;
use Artister\System\Compiler\ExpressionVisitor;
use Artister\System\Exceptions\PropertyException;

class PostgresqlDataProvider implements IEntityDataProvider
{   
    private string $Name = 'mysql';
    private DbConnection $Connection;
    private IEntityPersister $Persister;
    private ExpressionVisitor $Visitor;

    public function __construct(DbConnection $connection)
    {
        $this->Connection   = $connection;
        $this->Persister    = new PostgresqlEntityPersister($connection);
        $this->Visitor      = new PostgresqlQueryTranslator();
    }

    public function __get(string $name)
    {
        if (!in_array($name, ['Name', 'Connection', 'Persister', 'Visitor']))
        {
            throw PropertyException::undefinedPropery(self::class, $name);
        }

        return $this->$name;
    }
}