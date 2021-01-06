<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Entity\Storage;

use Artister\System\Database\DbConnection;
use Artister\Entity\EntityOptions;
use Artister\Entity\Metadata\EntityModel;
use Artister\Entity\Storage\IEntityPersister;
use Artister\Entity\Tracking\EntityStateManager;
use Artister\Entity\Tracking\EntityState;
use Artister\Entity\Query\EntityQueryProvider;
use Artister\Entity\Internal\EntityFinder;
use Artister\Entity\IEntity;

class EntityDatabase
{
    protected EntityModel $Model;
    protected IEntityDataProvider $DataProvider;
    protected EntityStateManager $EntityStateManager;
    protected EntityQueryProvider $QueryProvider;
    protected EntityFinder $Finder;

    public function __construct(EntityOptions $options, EntityModel $model)
    {
        $this->Model                    = $model;
        $this->DataProvider             = $options->Provider;
        $this->EntityStateManager       = new EntityStateManager($model);
        $this->QueryProvider            = new EntityQueryProvider($this);
        $this->Finder                   = new EntityFinder($this);
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
    
    public function save()
    {
        $entries = $this->EntityStateManager->getEntries();
        $this->persiste($entries);
        $this->EntityStateManager->clearEntries();
    }

    public function persiste($entries)
    {
        $this->DataProvider->Connection->open();
        foreach ($entries as $entityType)
        {
            foreach ($entityType as $entry)
            {
                $entry->detectChanges();
                switch ($entry->State)
                {
                    case EntityState::Added:
                        $this->DataProvider->Persister->insert($entry);
                        break;
                    case EntityState::Modified:
                        $this->DataProvider->Persister->update($entry);
                        break;
                    case EntityState::Deleted:
                        $this->DataProvider->Persister->delete($entry);
                        break;
                }
            }
        }

        $this->DataProvider->Connection->close();
    }
}