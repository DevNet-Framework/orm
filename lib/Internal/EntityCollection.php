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
    private EntityNavigation $Navigation;
    private EntityDatabase $Database;
    private $KeyValue;

    public function __construct(
        EntityNavigation $navigation,
        EntityDatabase $database,
        $keyValue
    ) {
        $this->Navigation = $navigation;
        $this->Database   = $database;
        $this->KeyValue   = $keyValue;
    }

    public function add($entity): void
    {
        $this->Database->add($entity);
    }

    public function remove($entity): void
    {
        $this->Database->remove($entity);
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
        $entities = $this->Database->Finder->query($this->Navigation, $this->KeyValue)->getIterator();

        foreach ($entities as $entity) {
            $this->Database->Finder->load($entity);
        }

        return $entities;
    }

    public function first()
    {
        foreach ($this->getIterator() as $element) {
            return $element;
        }
    }

    public function last()
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
