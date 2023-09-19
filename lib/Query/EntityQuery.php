<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Query;

use DevNet\System\Collections\Enumerator;
use DevNet\System\Collections\IEnumerable;
use DevNet\System\Linq\Enumerables\TakeEnumerable;
use DevNet\System\Compiler\Expressions\Expression;
use DevNet\System\Linq\IQueryProvider;
use DevNet\System\Linq\IQueryable;
use DevNet\System\PropertyTrait;

/**
 * create expression from method and passe it to queryProvider.
 */
class EntityQuery implements IQueryable
{

    use PropertyTrait;

    private object $entityType;
    private IQueryProvider $provider;
    private Expression $expression;

    public function __construct(object $entityType, IQueryProvider $provider, Expression $expression = null)
    {
        $this->entityType = $entityType;
        $this->provider   = $provider;
        $this->expression = ($expression == null) ? Expression::constant($this) : $expression;
    }

    public function get_EntityType(): object
    {
        return $this->entityType;
    }

    public function get_Provider(): IQueryProvider
    {
        return $this->provider;
    }

    public function get_Expression(): Expression
    {
        return $this->expression;
    }

    public function getIterator(): Enumerator
    {
        $entities = $this->provider->execute($this->entityType, $this->expression);
        return new Enumerator($entities);
    }

    public function toArray(): array
    {
        return $this->getIterator()->toArray();
    }

    public function __toString(): string
    {
        return $this->provider->getQueryText($this->expression);
    }

    public function asEnumerable(): IEnumerable
    {
        return new TakeEnumerable($this);
    }
}
