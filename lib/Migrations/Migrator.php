<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migrations;

use DevNet\System\Linq;
use DevNet\Entity\Storage\EntityDatabase;
use DevNet\Entity\Storage\IEntityDataProvider;

class Migrator
{
    private IEntityDataProvider $dataProvider;
    private MigrationHistory $history;
    private MigrationAssembly $assembly;

    public function __construct(EntityDatabase $database, string $namespace, string $directory)
    {
        $this->dataProvider = $database->DataProvider;
        $this->history      = new MigrationHistory($database, $namespace, $directory);
        $this->assembly    = new MigrationAssembly($namespace, $directory);
    }

    public function generateScript(array $operations): string
    {
        $migrationGenerator = new $this->dataProvider->MigrationGenerator;
        foreach ($operations as $operation) {
            $migrationGenerator->visit($operation);
        }

        return $migrationGenerator->__toString();
    }

    public function getCommands(?string $targetMigration = null): array
    {
        if ($targetMigration !== null) {
            $targetMigration = $this->assembly->findMigrationId($targetMigration);
        }

        $lastMigration      = $this->history->max(fn ($migration) => $migration->Id);
        $migrationsToApply  = [];
        $migrationsToRevert = [];
        $commands           = [];

        if ($targetMigration === null) {
            $migrationsToApply = $this->assembly->where(fn ($migration) => $migration->Id > $lastMigration)
                ->orderBy(fn ($migration) => $migration->Id);
        } else if ($targetMigration > $lastMigration) {
            if ($targetMigration) {
                $migrationsToApply = $this->assembly->where(fn ($migration) => $migration->Id > $lastMigration && $migration->Id <= $targetMigration)
                    ->orderBy(fn ($migration) => $migration->Id);
            } else {
                $migrationsToApply = $this->assembly->where(fn ($migration) => $migration->Id > $lastMigration)
                    ->orderBy(fn ($migration) => $migration->Id);
            }
        } else {
            $migrationsToRevert = $this->history->where(fn ($migration) => $migration->Id <= $lastMigration && $migration->Id > $targetMigration)
                ->orderByDescending(fn ($migration) => $migration->Id);
        }

        foreach ($migrationsToApply as $migrationToApply) {
            $migration  = $migrationToApply->Type;
            $migration  = new $migration();
            $upScript   = $this->generateScript($migration->UpOperations);
            $commands[] = $this->dataProvider->Connection->createCommand($upScript);

            $addScript  = $this->history->getInsertScript($migrationToApply->Id, $migrationToApply->Name);
            $commands[] = $this->dataProvider->Connection->createCommand($addScript);
        }

        foreach ($migrationsToRevert as $migrationToRevert) {
            $migration  = $migrationToRevert->Type;
            $migration  = new $migration();
            $downScript = $this->generateScript($migration->DownOperations);
            $commands[] = $this->dataProvider->Connection->createCommand($downScript);

            $delScript  = $this->history->getDeleteScript($migrationToRevert->Id);
            $commands[] = $this->dataProvider->Connection->createCommand($delScript);
        }

        return $commands;
    }

    public function migrate(?string $targetMigration = null): void
    {
        $commands   = $this->getCommands($targetMigration);
        $connection = $this->dataProvider->Connection;
        $connection->open();

        $transaction = $connection->beginTransaction();

        if (!$this->history->exists() && $commands) {
            $createScript = $this->history->getCreateScript();
            $command = $connection->createCommand($createScript);
            $command->execute();
        }

        try {
            foreach ($commands as $command) {
                $command->execute();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        $connection->close();
    }
}
