<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Query;

use DevNet\System\Collections\Enumerator;
use DevNet\System\Linq\Enumerables\TakeEnumerable;
use DevNet\System\Compiler\Expressions\Expression;
use DevNet\System\Linq\IQueryProvider;
use DevNet\System\Linq\IQueryable;

/**
 * create expression from method and passe it to queryProvider.
 */
class EntityQuery implements IQueryable
{
    use \DevNet\System\Extension\ExtenderTrait;

    private object $EntityType;
    private IQueryProvider $Provider;
    private Expression $Expression;

    public function __construct(object $entityType, IQueryProvider $provider, Expression $expression = null)
    {
        $this->EntityType = $entityType;
        $this->Provider   = $provider;
        $this->Expression = ($expression == null) ? Expression::constant($this) : $expression;
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function getIterator(): Enumerator
    {
        return $this->Provider->execute($this->EntityType, $this->Expression);
    }

    public function toArray(): array
    {
        return $this->getIterator()->toArray();
    }

    public function __toString()
    {
        return $this->Provider->getQueryText($this->Expression);
    }

    public function asEnumerable()
    {
        return new TakeEnumerable($this);
    }
}
