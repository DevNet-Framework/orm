<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Providers;

use DevNet\Entity\Storage\ISqlGenerationHelper;
use DevNet\Entity\Migration\Operations\OperationVisitor;
use DevNet\System\Compiler\ExpressionVisitor;
use DevNet\System\Database\DbConnection;
use DevNet\System\Exceptions\PropertyException;

interface IEntityDataProvider
{   
    /**
     * This method must retun the following properties.
     * @return DbConnection $Connection
     * @return ISqlGenerationHelper $SqlHelper
     * @return ExpressionVisitor $QueryGenerator
     * @return OperationVisitor $SchemaGenerator
     * and must throw an exception if the property doesn't exist
     * @throws PropertyException
     */
    public function __get(string $name);
}
