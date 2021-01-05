<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity\Storage;

use Artister\Data\Entity\Storage\IEntityPersister;
use Artister\System\Compiler\ExpressionVisitor;
use Artister\System\Exceptions\PropertyException;

interface IEntityDataProvider
{   
        /**
     * This method must retun the following properties.
     * @return string $Name (the database provider name ex: mysql, pgsql, sqlite, ...)
     * @return IEntityPersister $Persister
     * @return ExpressionVisitor $Visitor
     * and must throw an exception if the property doesn't exist
     * @throws PropertyException
     */
    public function __get(string $name);
}