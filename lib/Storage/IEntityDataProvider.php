<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Storage;

use DevNet\Entity\Migration\Operations\OperationVisitor;
use DevNet\Entity\Storage\ISqlGenerationHelper;
use DevNet\System\Compiler\Expressions\ExpressionVisitor;
use DevNet\System\Database\DbConnection;
use DevNet\System\Exceptions\PropertyException;

interface IEntityDataProvider
{
    /**
     * This method must return the following properties.
     * @return DbConnection $Connection
     * @return ISqlGenerationHelper $SqlHelper
     * @return ExpressionVisitor $QueryGenerator
     * @return OperationVisitor $MigrationGenerator
     * and must throw an exception if the property doesn't exist
     * @throws PropertyException
     */
    public function __get(string $name);
}
