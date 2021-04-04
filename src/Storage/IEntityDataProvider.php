<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Storage;

use DevNet\Entity\Storage\IEntityPersister;
use DevNet\System\Database\DbConnection;
use DevNet\System\Compiler\ExpressionVisitor;
use DevNet\System\Exceptions\PropertyException;

interface IEntityDataProvider
{   
    /**
     * This method must retun the following properties.
     * @return string $Name (the database provider name ex: mysql, pgsql, sqlite, ...)
     * @return DbConnection $Connection
     * @return IEntityPersister $Persister
     * @return ExpressionVisitor $Visitor
     * and must throw an exception if the property doesn't exist
     * @throws PropertyException
     */
    public function __get(string $name);
}
