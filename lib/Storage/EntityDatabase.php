<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Storage;

use DevNet\ORM\Metadata\EntityModel;
use DevNet\ORM\Query\EntityFinder;
use DevNet\ORM\Query\EntityQueryProvider;
use DevNet\ORM\Storage\EntityDataPersister;
use DevNet\ORM\Tracking\EntityEntry;
use DevNet\ORM\Tracking\EntityStateManager;
use DevNet\ORM\Tracking\EntityState;
use DevNet\System\PropertyTrait;

class EntityDatabase
{
    use PropertyTrait;

    private EntityModel $model;
    private EntityFinder $finder;
    private IEntityDataProvider $dataProvider;
    private EntityQueryProvider $queryProvider;
    private EntityDataPersister $dataPersister;
    private EntityStateManager $entityStateManager;

    public function __construct(IEntityDataProvider $provider)
    {
        $this->dataProvider       = $provider;
        $this->model              = new EntityModel();
        $this->dataPersister      = new EntityDataPersister($provider->Connection, $provider->SqlHelper);
        $this->entityStateManager = new EntityStateManager($this->model);
        $this->queryProvider      = new EntityQueryProvider($this);
        $this->finder             = new EntityFinder($this);
    }

    public function get_Model(): EntityModel
    {
        return $this->model;
    }

    public function get_Finder(): EntityFinder
    {
        return $this->finder;
    }

    public function get_DataProvider(): IEntityDataProvider
    {
        return $this->dataProvider;
    }

    public function get_QueryProvider(): EntityQueryProvider
    {
        return $this->queryProvider;
    }

    public function get_DataPersister(): EntityDataPersister
    {
        return $this->dataPersister;
    }

    public function get_EntityStateManager(): EntityStateManager
    {
        return $this->entityStateManager;
    }

    public function entry(object $entity): EntityEntry
    {
        return $this->entityStateManager->getOrCreateEntry($entity);
    }

    public function attach(object $entity): void
    {
        $this->entry($entity);
    }

    public function add(object $entity): void
    {
        $this->entry($entity)->State = EntityState::Added;
    }

    public function remove(object $entity): void
    {
        $this->entry($entity)->State = EntityState::Deleted;
    }

    public function save(): int
    {
        $entries = $this->entityStateManager->getEntries();
        $count = $this->persist($entries);
        $this->entityStateManager->clearEntries();
        return $count;
    }

    public function persist($entries): int
    {
        $count = 0;
        $this->dataProvider->Connection->open();
        foreach ($entries as $entityType) {
            foreach ($entityType as $entry) {
                if ($entry->State == EntityState::Attached) {
                    $entry->detectChanges();
                }
                switch ($entry->State) {
                    case EntityState::Added:
                        $count += $this->dataPersister->insert($entry);
                        break;
                    case EntityState::Modified:
                        $count += $this->dataPersister->update($entry);
                        break;
                    case EntityState::Deleted:
                        $count += $this->dataPersister->delete($entry);
                        break;
                }
            }
        }

        $this->dataProvider->Connection->close();
        return $count;
    }
}
