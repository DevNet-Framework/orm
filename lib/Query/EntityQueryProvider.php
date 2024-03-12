<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Query;

use DevNet\ORM\Storage\EntityDatabase;
use DevNet\System\Linq\IQueryable;
use DevNet\System\Linq\IQueryProvider;
use DevNet\System\Compiler\Expressions\Expression;
use DateTime;

class EntityQueryProvider implements IQueryProvider
{
    private EntityDatabase $database;

    public function __construct(EntityDatabase $database)
    {
        $this->database = $database;
    }

    public function createQuery(object $entityType, Expression $expression = null): IQueryable
    {
        return new EntityQuery($entityType, $this, $expression);
    }

    public function execute(object $entityType, Expression $expression): array
    {
        $this->database->DataProvider->Connection->open();

        $entities  = [];
        $slq       = $this->getQueryText($expression);
        $command   = $this->database->DataProvider->Connection->createCommand($slq);
        $variables = $this->database->DataProvider->QueryGenerator->OuterVariables;
        $dbReader  = $command->executeReader($variables);

        if ($dbReader) {
            while ($dbReader->read()) {
                $entity = $entityType->Name;
                $entity = new $entity();

                foreach ($entityType->Properties as $property) {
                    $value = null;
                    $propertyName = $property->PropertyInfo->getName();
                    if ($property->PropertyInfo->hasType()) {
                        $propertyType = $property->PropertyInfo->getType()->getName();
                        if ($propertyType == DateTime::class) {
                            $date = $dbReader->getValue($property->getColumnName());
                            if ($date) {
                                $value = new DateTime($date);
                            }
                        } else if ($propertyType == "bool") {
                            $value = (bool)$dbReader->getValue($property->getColumnName());
                        } else if ($propertyType == "int") {
                            $value = (int)$dbReader->getValue($property->getColumnName());
                        } else if ($propertyType == "float") {
                            $value = (float)$dbReader->getValue($property->getColumnName());
                        } else {
                            $value = $dbReader->getValue($property->getColumnName());
                        }
                    }

                    $entity->$propertyName = $value;
                }

                $entities[] = $entity;
            }
        }

        $this->database->DataProvider->Connection->close();
        return $entities;
    }

    public function getQueryText(Expression $expression): string
    {
        $queryGenerator = $this->database->DataProvider->QueryGenerator;
        $queryGenerator->Sql = [];
        $queryGenerator->OuterVariables = [];
        $queryGenerator->visit($expression);
        return $queryGenerator;
    }
}
