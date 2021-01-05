<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity\Providers\Mysql;

use Artister\Data\Entity\Storage\IEntityDataProvider;
use Artister\Data\Entity\Storage\IEntityPersister;
use Artister\System\Database\DbConnection;
use Artister\System\Compiler\ExpressionVisitor;
use Artister\System\Exceptions\PropertyException;

class MysqlDataProvider implements IEntityDataProvider
{   
    private string $Name = 'mysql';
    private IEntityPersister $Persister;
    private ExpressionVisitor $Visitor;

    public function __construct(DbConnection $connection)
    {
        $this->Persister    = new MysqlEntityPersister($connection);
        $this->Visitor      = new MysqlQueryTranslator();
    }

    public function __get(string $name)
    {
        if (!in_array($name, ['Name', 'Persister', 'Visitor']))
        {
            throw PropertyException::undefinedPropery(self::class, $name);
        }

        return $this->$name;
    }
}