<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Providers\PostgreSql;

use DevNet\Entity\Storage\IEntityPersister;
use DevNet\Entity\Tracking\EntityEntry;
use DevNet\System\Database\DbConnection;

class PostgreSqlEntityPersister implements IEntityPersister
{
    private DbConnection $Connection;

    public function __construct(DbConnection $connection)
    {
        $this->Connection = $connection;
    }

    public function insert(EntityEntry $entry): int
    {
        $entityType   = $entry->Metadata;
        $placeHolders = [];
        $culomns      = [];
        $values       = [];
        foreach ($entry->Values as $name => $value) {
            $placeHolders[] = '?';
            $culomns[]      = '"' . $name . '"';
            $values[]       = $value;
        }

        $placeHolders = implode(', ', $placeHolders);
        $culomns      = implode(', ', $culomns);
        $dbCommand    = $this->Connection->createCommand("INSERT INTO \"{$entityType->getTableName()}\" ($culomns) VALUES ({$placeHolders})");
        $dbCommand->addParameters($values);
        return $dbCommand->execute();
    }

    public function update(EntityEntry $entry): int
    {
        $entityType   = $entry->Metadata;
        $key          = $entityType->getPrimaryKey();
        $placeHolders = [];
        $values       = [];

        foreach ($entry->Values as $name => $value) {
            $placeHolders[] = "\"{$name}\" = ?";
            $values[]       = $value;
        }

        $placeHolders = implode(', ', $placeHolders);
        $values[]     = $entry->Entity->$key;
        $dbCommand    = $this->Connection->createCommand("UPDATE \"{$entityType->getTableName()}\" SET {$placeHolders} WHERE {$key} = ?");

        $dbCommand->addParameters($values);
        return $dbCommand->execute();
    }

    public function delete(EntityEntry $entry): int
    {
        $entityType = $entry->Metadata;
        $key        = $entityType->getPrimaryKey();
        $values[]   = $entry->Entity->$key;
        $dbCommand  = $this->Connection->createCommand("DELETE FROM \"{$entityType->getTableName()}\" WHERE {$key} = ?");

        $dbCommand->addParameters($values);
        return $dbCommand->execute();
    }
}
