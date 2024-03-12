<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Storage;

use DevNet\ORM\Tracking\EntityEntry;
use DevNet\System\Database\DbConnection;

class EntityDataPersister
{
    private DbConnection $connection;
    private ISqlGenerationHelper $sqlHelper;

    public function __construct(DbConnection $connection, ISqlGenerationHelper $sqlHelper)
    {
        $this->connection = $connection;
        $this->sqlHelper  = $sqlHelper;
    }

    public function insert(EntityEntry $entry): int
    {
        $entityType   = $entry->Metadata;
        $placeHolders = [];
        $columns      = [];
        $values       = [];
        foreach ($entry->Values as $name => $value) {
            $placeHolders[] = '?';
            $columns[]      = $this->sqlHelper->delimitIdentifier($name);
            $values[]       = $value;
        }

        $placeHolders = implode(', ', $placeHolders);
        $columns      = implode(', ', $columns);
        $table        = $this->sqlHelper->delimitIdentifier($entityType->getTableName(), $entityType->getSchemaName());
        $dbCommand    = $this->connection->createCommand("INSERT INTO {$table} ($columns) VALUES ({$placeHolders})");

        return $dbCommand->execute($values);
    }

    public function update(EntityEntry $entry): int
    {
        $entityType = $entry->Metadata;
        $count      = count($entityType->Keys);

        if ($count < 1 && $count > 2) {
            return 0;
        }

        $placeHolders = [];
        foreach ($entry->Values as $name => $value) {
            $placeHolders[] = $this->sqlHelper->delimitIdentifier($name) . " = ?";
            $values[]       = $value;
        }

        $placeHolders = implode(', ', $placeHolders);
        $table        = $this->sqlHelper->delimitIdentifier($entityType->getTableName(), $entityType->getSchemaName());
        $propertyKey  = $entityType->Keys[0];
        $values[]     = $entry->Entity->$propertyKey;
        $key          = $entityType->getProperty($propertyKey)->getColumnName();
        $key          = $this->sqlHelper->delimitIdentifier($key);
        $dbCommand    = $this->connection->createCommand("UPDATE {$table} SET {$placeHolders} WHERE {$key} = ?");

        if ($count == 2) {
            $propertyKey2 = $entityType->Keys[1];
            $values[]     = $entry->Entity->$propertyKey2;
            $key2         = $entityType->getProperty($propertyKey2)->getColumnName();
            $key2         = $this->sqlHelper->delimitIdentifier($key2);
            $dbCommand    = $this->connection->createCommand("UPDATE {$table} SET {$placeHolders} WHERE {$key} = ? AND {$key2} = ?");
        }

        return $dbCommand->execute($values);
    }

    public function delete(EntityEntry $entry): int
    {
        $entityType = $entry->Metadata;
        $count      = count($entityType->Keys);

        if ($count < 1 && $count > 2) {
            return 0;
        }

        $table       = $this->sqlHelper->delimitIdentifier($entityType->getTableName(), $entityType->getSchemaName());
        $propertyKey = $entityType->Keys[0];
        $values[]    = $entry->Entity->$propertyKey;
        $key         = $entityType->getProperty($propertyKey)->getColumnName();
        $key         = $this->sqlHelper->delimitIdentifier($key);
        $dbCommand   = $this->connection->createCommand("DELETE FROM {$table} WHERE {$key} = ?");

        if ($count == 2) {
            $propertyKey2 = $entityType->Keys[1];
            $values[]     = $entry->Entity->$propertyKey2;
            $key2         = $entityType->getProperty($propertyKey2)->getColumnName();
            $key2         = $this->sqlHelper->delimitIdentifier($key2);
            $dbCommand    = $this->connection->createCommand("DELETE FROM {$table} WHERE {$key} = ? AND {$key2} = ?");
        }

        return $dbCommand->execute($values);
    }
}
