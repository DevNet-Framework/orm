<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Entity\Query;

use Artister\Entity\Storage\EntityDatabase;
use Artister\System\Linq\IQueryable;
use Artister\System\Linq\IQueryProvider;
use Artister\System\Compiler\Expressions\Expression;
use Artister\System\Collections\Enumerator;

class EntityQueryProvider implements IQueryProvider
{
    private EntityDatabase $Database;

    public function __construct(EntityDatabase $database)
    {
        $this->Database = $database;
    }

    public function CreateQuery(object $entityType, Expression $expression = null) : IQueryable
    {
        return new EntityQuery($entityType, $this, $expression);
    }

    public function execute(object $entityType, Expression $expression)
    {
        $this->Database->DataProvider->Connection->open();
        $slq        = $this->getQueryText($expression);
        $command    = $this->Database->DataProvider->Connection->createCommand($slq);
        $variables  = $this->Database->DataProvider->Visitor->OuterVariables;

        if ($variables)
        {
            $command->addParameters($variables);
        }

        $entities = new Enumerator();
        $dbReader = $command->executeReader($entityType->getName());
        
        if ($dbReader)
        {
            $entities = $dbReader->getIterator();
        }
        
        $this->Database->DataProvider->Connection->close();
        
        return $entities;
    }

    public function getQueryText(Expression $expression) : string
    {
        $translator = $this->Database->DataProvider->Visitor;
        $translator->Sql = [];
        $translator->OuterVariables = [];
        $translator->visit($expression);
        return $translator->__toString();
    }
}
