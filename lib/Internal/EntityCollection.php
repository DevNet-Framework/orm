<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Internal;

use DevNet\Entity\Metadata\EntityNavigation;
use DevNet\Entity\Storage\EntityDatabase;
use DevNet\System\Collections\Enumerator;
use DevNet\System\Collections\IList;

class EntityCollection implements IList
{
    private EntityNavigation $navigation;
    private EntityDatabase $database;
    private $keyValue;

    public function __construct(
        EntityNavigation $navigation,
        EntityDatabase $database,
        $keyValue
    ) {
        $this->navigation = $navigation;
        $this->database   = $database;
        $this->keyValue   = $keyValue;
    }

    public function add($entity): void
    {
        $this->database->add($entity);
    }

    public function remove($entity): void
    {
        $this->database->remove($entity);
    }

    public function contains($entity): bool
    {
        foreach ($this as $entity) {
            if ($entity == $entity) {
                return true;
            }
        }

        return false;
    }

    public function getIterator(): Enumerator
    {
        $entities = $this->database->Finder->query($this->navigation, $this->keyValue)->getIterator();

        foreach ($entities as $entity) {
            $this->database->Finder->load($entity);
        }

        return $entities;
    }

    public function first(): object
    {
        foreach ($this->getIterator() as $element) {
            return $element;
        }
    }

    public function last(): object
    {
        foreach ($this->getIterator() as $element) {
            $last = $element;
        }
        return $last;
    }

    public function toArray(): array
    {
        return $this->getIterator()->toArray();
    }
}
