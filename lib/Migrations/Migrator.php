<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Migrations;

use DevNet\System\Linq;
use DevNet\ORM\Storage\EntityDatabase;
use DevNet\ORM\Storage\IEntityDataProvider;
use DevNet\System\IO\Console;

class Migrator
{
    private IEntityDataProvider $dataProvider;
    private MigrationAssembly $assembly;
    private MigrationHistory $history;

    public function __construct(EntityDatabase $database, string $directory)
    {
        $this->dataProvider = $database->DataProvider;
        $this->assembly     = new MigrationAssembly($directory);
        $this->history      = new MigrationHistory($database, $this->assembly);
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

            if (!empty($upScript)) {
                $commands[0][$migrationToApply->Id][] = $this->dataProvider->Connection->createCommand($upScript);
            }

            $addScript = $this->history->getInsertScript($migrationToApply->Id);
            $commands[0][$migrationToApply->Id][] = $this->dataProvider->Connection->createCommand($addScript);
        }

        foreach ($migrationsToRevert as $migrationToRevert) {
            $migration  = $migrationToRevert->Type;
            $migration  = new $migration();
            $downScript = $this->generateScript($migration->DownOperations);

            if (!empty($downScript)) {
                $commands[1][$migrationToRevert->Id][] = $this->dataProvider->Connection->createCommand($downScript);
            }

            $delScript = $this->history->getDeleteScript($migrationToRevert->Id);
            $commands[1][$migrationToRevert->Id][] = $this->dataProvider->Connection->createCommand($delScript);
        }

        return $commands;
    }

    public function migrate(?string $targetMigration = null): int
    {
        $commands   = $this->getCommands($targetMigration);
        $connection = $this->dataProvider->Connection;
        $connection->open();

        $transaction = $connection->beginTransaction();

        if ($commands && !$this->history->exists()) {
            $createScript = $this->history->getCreateScript();
            $command = $connection->createCommand($createScript);
            $command->execute();
        }

        try {
            if (isset($commands[0])) {
                foreach ($commands[0] as $id => $applies) {
                    Console::writeLine("Applying migration {$id}");
                    foreach ($applies as $command) {
                        $count = $command->execute();
                    }
                }
            } else if (isset($commands[1])) {
                foreach ($commands[1] as $id => $reverts) {
                    Console::writeLine("Reverting migration {$id}");
                    foreach ($reverts as $command) {
                        $count = $command->execute();
                    }
                }
            } else {
                $count = 0;
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw $e;
        }

        $connection->close();
        return $count;
    }
}
