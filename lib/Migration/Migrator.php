<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

use DevNet\System\Linq;
use DevNet\Entity\Storage\EntityDatabase;
use DevNet\Entity\Providers\IEntityDataProvider;

class Migrator
{
    private ?string $Schema;
    private IEntityDataProvider $DataProvider;
    private MigrationHistory $History;
    private MigrationAssembly $Assembley;

    public function __construct(EntityDatabase $database, string $namespace, string $directory)
    {
        $this->Schema       = $database->Model->Schema;
        $this->DataProvider = $database->DataProvider;
        $this->History      = new MigrationHistory($database, $namespace, $directory);
        $this->Assembley    = new MigrationAssembly($namespace, $directory);
    }

    public function generateScript(array $operations): string
    {
        $migrationGenerator = new $this->DataProvider->MigrationGenerator;
        foreach ($operations as $operation) {
            $migrationGenerator->visit($operation);
        }

        return $migrationGenerator->__toString();
    }

    public function getCommands(?string $targetMigration = null): array
    {
        if ($targetMigration !== null) {
            $targetMigration = $this->Assembley->findMigrationId($targetMigration);
        }

        $lastMigration      = $this->History->max(fn ($m) => $m->Id);
        $migrationsToApply  = [];
        $migrationsToRevert = [];
        $commands           = [];

        if ($targetMigration === null) {
            $migrationsToApply = $this->Assembley->where(fn ($m) => $m->Id > $lastMigration)
                ->orderBy(fn ($m) => $m->Id);
        } else if ($targetMigration > $lastMigration) {
            if ($targetMigration) {
                $migrationsToApply = $this->Assembley->where(fn ($m) => $m->Id > $lastMigration && $m->Id <= $targetMigration)
                    ->orderBy(fn ($m) => $m->Id);
            } else {
                $migrationsToApply = $this->Assembley->where(fn ($m) => $m->Id > $lastMigration)
                    ->orderBy(fn ($m) => $m->Id);
            }
        } else {
            $migrationsToRevert = $this->History->where(fn ($m) => $m->Id <= $lastMigration && $m->Id > $targetMigration)
                ->orderByDescending(fn ($m) => $m->Id);
        }

        foreach ($migrationsToApply as $migrationToApply) {
            $migration  = $migrationToApply->Type;
            $migration  = new $migration($this->Schema);
            $upScript   = $this->generateScript($migration->UpOperations);
            $commands[] = $this->DataProvider->Connection->createCommand($upScript);

            $addScript  = $this->History->getInsertScript($migrationToApply->Id, $migrationToApply->Name);
            $commands[] = $this->DataProvider->Connection->createCommand($addScript);
        }

        foreach ($migrationsToRevert as $migrationToRevert) {
            $migration  = $migrationToRevert->Type;
            $migration  = new $migration($this->Schema);
            $downScript = $this->generateScript($migration->DownOperations);
            $commands[] = $this->DataProvider->Connection->createCommand($downScript);

            $delScript  = $this->History->getDeleteScript($migrationToRevert->Id);
            $commands[] = $this->DataProvider->Connection->createCommand($delScript);
        }

        return $commands;
    }

    public function migrate(?string $targetMigration = null): void
    {
        $commands   = $this->getCommands($targetMigration);
        $connection = $this->DataProvider->Connection;
        $connection->open();

        $transaction = $connection->beginTransaction();
        
        if (!$this->History->exists() && $commands) {
            $createScript = $this->History->getCreateScript();
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
