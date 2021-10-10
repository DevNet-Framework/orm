<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Storage;

use DevNet\Entity\Tracking\EntityEntry;
use DevNet\System\Database\DbConnection;

class EntityDataPersister
{
    private DbConnection $Connection;
    private ISqlGenerationHelper $SqlHelper;

    public function __construct(DbConnection $connection, ISqlGenerationHelper $sqlHelper)
    {
        $this->Connection = $connection;
        $this->SqlHelper  = $sqlHelper;
    }

    public function insert(EntityEntry $entry): int
    {
        $entityType   = $entry->Metadata;
        $placeHolders = [];
        $culomns      = [];
        $values       = [];
        foreach ($entry->Values as $name => $value) {
            $placeHolders[] = '?';
            $culomns[]      = $this->SqlHelper->delimitIdentifier($name);
            $values[]       = $value;
        }

        $table        = $this->SqlHelper->delimitIdentifier($entityType->getTableName(), $entityType->getSchemaName());
        $placeHolders = implode(', ', $placeHolders);
        $culomns      = implode(', ', $culomns);
        $dbCommand    = $this->Connection->createCommand("INSERT INTO {$table} ($culomns) VALUES ({$placeHolders})");
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
            $placeHolders[] = $this->SqlHelper->delimitIdentifier($name) . " = ?";
            $values[]       = $value;
        }

        $table        = $this->SqlHelper->delimitIdentifier($entityType->getTableName(), $entityType->getSchemaName());
        $placeHolders = implode(', ', $placeHolders);
        $values[]     = $entry->Entity->$key;
        $key          = $this->SqlHelper->delimitIdentifier($key);
        $dbCommand    = $this->Connection->createCommand("UPDATE {$table} SET {$placeHolders} WHERE {$key} = ?");

        $dbCommand->addParameters($values);
        return $dbCommand->execute();
    }

    public function delete(EntityEntry $entry): int
    {
        $entityType = $entry->Metadata;
        $key        = $this->SqlHelper->delimitIdentifier($entityType->getPrimaryKey());
        $values[]   = $entry->Entity->$key;
        $table      = $this->SqlHelper->delimitIdentifier($entityType->getTableName(), $entityType->getSchemaName());
        $dbCommand  = $this->Connection->createCommand("DELETE FROM {$table} WHERE {$key} = ?");

        $dbCommand->addParameters($values);
        return $dbCommand->execute();
    }
}
