<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity\Storage;

use Artister\System\Database\DbConnection;
use Artister\Data\Entity\Metadata\EntityModel;
use Artister\Data\Entity\Tracking\EntityStateManager;
use Artister\Data\Entity\Tracking\EntityState;
use Artister\Data\Entity\Internal\EntityFinder;
use Artister\Data\Entity\Query\EntityQueryProvider;
use Artister\Data\Entity\Storage\IEntityPersister;
use Artister\Data\Entity\Providers\Mysql\MysqlDataProvider;
use Artister\Data\Entity\Providers\Mysql\MysqlEntityPersister;
use Artister\Data\Entity\Providers\Mysql\MysqlQueryTranslator;
use Artister\Data\Entity\IEntity;

class EntityDatabase
{
    protected DbConnection $Connection;
    protected EntityModel $Model;
    protected EntityStateManager $EntityStateManager;
    protected EntityFinder $Finder;
    protected EntityQueryProvider $QueryProvider;
    protected IEntityDataProvider $DataProvider;

    public function __construct(DbConnection $connection, EntityModel $model)
    {
        $this->Connection               = $connection;
        $this->Model                    = $model;
        $this->EntityStateManager       = new EntityStateManager($model);
        $this->Finder                   = new EntityFinder($this);
        $this->QueryProvider            = new EntityQueryProvider($this);
        $this->DataProvider             = new MysqlDataProvider(new MysqlEntityPersister($connection), new MysqlQueryTranslator);
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
        $this->Connection->open();
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

        $this->Connection->close();
    }
}