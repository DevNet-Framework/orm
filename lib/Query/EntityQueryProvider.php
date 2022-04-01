<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Query;

use DevNet\Entity\Storage\EntityDatabase;
use DevNet\System\Linq\IQueryable;
use DevNet\System\Linq\IQueryProvider;
use DevNet\System\Collections\Enumerator;
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

    public function execute(object $entityType, Expression $expression)
    {
        $this->database->DataProvider->Connection->open();
        $slq       = $this->getQueryText($expression);
        $command   = $this->database->DataProvider->Connection->createCommand($slq);
        $variables = $this->database->DataProvider->QueryGenerator->OuterVariables;

        if ($variables) {
            $command->addParameters($variables);
        }

        $entities = [];
        $dbReader = $command->executeReader($entityType->getName());

        if ($dbReader) {
            while ($dbReader->read()) {
                $entity = $entityType->getName();
                $entity = new $entity();

                foreach ($entityType->Properties as $property) {
                    $value = null;
                    $propertyName = $property->PropertyInfo->getName();
                    if ($property->PropertyInfo->hasType()) {
                        $propertyType = $property->PropertyInfo->getType()->getName();
                        if ($propertyType == DateTime::class) {
                            $date = $dbReader->getValue($property->Column['Name']);
                            if ($date) {
                                $value = new DateTime($date);
                            }
                        } else if ($propertyType == "bool") {
                            $value = (bool)$dbReader->getValue($property->Column['Name']);
                        } else if ($propertyType == "int") {
                            $value = (int)$dbReader->getValue($property->Column['Name']);
                        } else if ($propertyType == "float") {
                            $value = (float)$dbReader->getValue($property->Column['Name']);
                        } else {
                            $value = $dbReader->getValue($property->Column['Name']);
                        }
                    }

                    $entity->$propertyName = $value;
                }

                $entities[] = $entity;
            }
        }

        $this->database->DataProvider->Connection->close();
        return new Enumerator($entities);
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
