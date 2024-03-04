<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Query;

use DevNet\Entity\Metadata\EntityNavigation;
use DevNet\Entity\Storage\EntityDatabase;
use DevNet\System\Collections\Enumerator;
use DevNet\System\Collections\ICollection;

class EntityCollection implements ICollection
{
    private EntityNavigation $navigation;
    private EntityDatabase $database;
    private string $keyValue;

    public function __construct(EntityNavigation $navigation, EntityDatabase $database, string $keyValue)
    {
        $this->navigation = $navigation;
        $this->database   = $database;
        $this->keyValue   = $keyValue;
    }

    public function add(object $entity): void
    {
        $this->database->add($entity);
    }

    public function remove(mixed $entity): void
    {
        $this->database->remove($entity);
    }

    public function contains(mixed $entity): bool
    {
        foreach ($this as $entity) {
            if ($entity == $entity) {
                return true;
            }
        }

        return false;
    }

    public function clear(): void
    {
        foreach ($this->getIterator() as $entity) {
            $this->database->remove($entity);
        }
    }

    public function getIterator(): Enumerator
    {
        $entities = $this->database->Finder->query($this->navigation, $this->keyValue)->getIterator();

        foreach ($entities as $entity) {
            $this->database->Finder->load($entity);
        }

        return $entities;
    }

    public function first(): ?object
    {
        foreach ($this->getIterator() as $entity) {
            return $entity;
        }

        return null;
    }

    public function last(): ?object
    {
        $entities = $this->toArray();
        return $entities[count($entities) - 1] ?? null;
    }

    public function toArray(): array
    {
        return $this->getIterator()->toArray();
    }
}
