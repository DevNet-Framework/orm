<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Storage;

use DevNet\Entity\Migrations\Operations\OperationVisitor;
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
