<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Storage;

use DevNet\System\Database\DbConnection;
use DevNet\Entity\EntityOptions;
use DevNet\Entity\Metadata\EntityModel;
use DevNet\Entity\Storage\IEntityPersister;
use DevNet\Entity\Tracking\EntityStateManager;
use DevNet\Entity\Tracking\EntityState;
use DevNet\Entity\Query\EntityQueryProvider;
use DevNet\Entity\Internal\EntityFinder;
use DevNet\Entity\IEntity;

class EntityDatabase
{
    protected EntityModel $Model;
    protected IEntityDataProvider $DataProvider;
    protected EntityStateManager $EntityStateManager;
    protected EntityQueryProvider $QueryProvider;
    protected EntityFinder $Finder;

    public function __construct(EntityOptions $options, EntityModel $model)
    {
        $this->Model              = $model;
        $this->DataProvider       = $options->Provider;
        $this->EntityStateManager = new EntityStateManager($model);
        $this->QueryProvider      = new EntityQueryProvider($this);
        $this->Finder             = new EntityFinder($this);
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function finder(string $entityName)
    {
        $entityType = $this->Model->getEntityType($entityName);
        return $this->EntityFinderFactory->create($entityType);
    }

    public function entry(IEntity $entity)
    {
        return $this->EntityStateManager->getOrCreateEntry($entity);
    }

    public function attach(IEntity $entity)
    {
        $this->entry($entity);
    }

    public function add(IEntity $entity)
    {
        $this->entry($entity)->State = EntityState::Added;
    }

    public function remove(IEntity $entity)
    {
        $this->entry($entity)->State = EntityState::Deleted;
    }

    public function save(): int
    {
        $entries = $this->EntityStateManager->getEntries();
        $count   = $this->persiste($entries);
        $this->EntityStateManager->clearEntries();
        return $count;
    }

    public function persiste($entries): int
    {
        $count = 0;
        $this->DataProvider->Connection->open();
        foreach ($entries as $entityType) {
            foreach ($entityType as $entry) {
                $entry->detectChanges();
                switch ($entry->State) {
                    case EntityState::Added:
                        $count += $this->DataProvider->Persister->insert($entry);
                        break;
                    case EntityState::Modified:
                        $count += $this->DataProvider->Persister->update($entry);
                        break;
                    case EntityState::Deleted:
                        $count += $this->DataProvider->Persister->delete($entry);
                        break;
                }
            }
        }

        $this->DataProvider->Connection->close();
        return $count;
    }
}
