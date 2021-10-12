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
    private EntityDatabase $Database;

    public function __construct(EntityDatabase $database)
    {
        $this->Database = $database;
    }

    public function createQuery(object $entityType, Expression $expression = null): IQueryable
    {
        return new EntityQuery($entityType, $this, $expression);
    }

    public function execute(object $entityType, Expression $expression)
    {
        $this->Database->DataProvider->Connection->open();
        $slq       = $this->getQueryText($expression);
        $command   = $this->Database->DataProvider->Connection->createCommand($slq);
        $variables = $this->Database->DataProvider->Visitor->OuterVariables;

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

        $this->Database->DataProvider->Connection->close();
        return new Enumerator($entities);
    }

    public function getQueryText(Expression $expression): string
    {
        $translator = $this->Database->DataProvider->Visitor;
        $translator->Sql = [];
        $translator->OuterVariables = [];
        $translator->visit($expression);
        return $translator->__toString();
    }
}
