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
use DevNet\System\Exceptions\PropertyException;
use DevNet\System\Linq\IQueryProvider;
use DevNet\System\Linq\IQueryable;
use DevNet\System\ObjectTrait;

/**
 * create expression from method and passe it to queryProvider.
 */
class EntityQuery implements IQueryable
{
    use ObjectTrait;

    private object $entityType;
    private IQueryProvider $provider;
    private Expression $expression;

    public function __get(string $name)
    {
        if (in_array($name, ['EntityType', 'Provider', 'Expression'])) {
            $property = lcfirst($name);
            return $this->$property;
        }

        if (property_exists($this, $name)) {
            throw new PropertyException("access to private property " . get_class($this) . "::" . $name);
        }

        throw new PropertyException("access to undefined property " . get_class($this) . "::" . $name);
    }

    public function __construct(object $entityType, IQueryProvider $provider, Expression $expression = null)
    {
        $this->entityType = $entityType;
        $this->provider   = $provider;
        $this->expression = ($expression == null) ? Expression::constant($this) : $expression;
    }

    public function getIterator(): Enumerator
    {
        return $this->provider->execute($this->entityType, $this->expression);
    }

    public function toArray(): array
    {
        return $this->getIterator()->toArray();
    }

    public function __toString()
    {
        return $this->provider->getQueryText($this->expression);
    }

    public function asEnumerable()
    {
        return new TakeEnumerable($this);
    }
}
