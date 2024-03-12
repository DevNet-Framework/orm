<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Tools;

use DevNet\ORM\EntityContext;
use DevNet\ORM\EntityOptions;
use DevNet\ORM\Migrations\Migrator;
use DevNet\System\Command\CommandEventArgs;
use DevNet\System\Command\CommandLine;
use DevNet\System\Command\ICommandHandler;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;
use DirectoryIterator;

class MigrateCommand extends CommandLine implements ICommandHandler
{
    public function __construct()
    {
        parent::__construct('migrate', 'Update the database to a specified migration.');

        $this->addOption('--target', 'The migration target, by default update to the last migration.');
        $this->addOption('--directory', 'The relative path to the migration target.');
        $this->addOption('--connection', 'The connection string to the database. Defaults the one specified in settings.json.');
        $this->addOption('--provider', 'The custom IEntityDataProvider to use. Defaults the one specified in settings.json.');
    }

    public function onExecute(object $sender, CommandEventArgs $args): void
    {
        $projectRoot = getcwd();
        $settingsPath = $projectRoot . '/settings.json';
        if (is_file($settingsPath)) {
            $contents = file_get_contents($settingsPath);
            $settings = json_decode($contents, true);
            $connectionString = $settings['Database']['ConnectionString'] ?? null;
            $providerType = $settings['Database']['ProviderType'] ?? null;
        }

        $connection = $args->get('--connection');
        if ($connection) {
            if ($connection->Value) {
                $connectionString = ucwords($connection->Value, '\\');
            }
        }

        $provider = $args->get('--provider');
        if ($provider) {
            if ($provider->Value) {
                $providerType = ucwords($provider->Value, '\\');
            }
        }

        if (!$providerType) {
            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine("The data provider type is missing!");
            Console::resetColor();
            exit;
        }

        if (!$connectionString) {
            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine("The connection string is missing!");
            Console::resetColor();
            exit;
        }

        $entityOptions = new EntityOptions($connectionString, $providerType);
        $entityContext = new EntityContext($entityOptions);

        $directoryName = 'Migrations';
        $directory = $args->get('--directory');
        if ($directory) {
            if ($directory->Value) {
                $directoryName = ucwords($directory->Value, '/');
            }
        }

        $path = $this->findMigrationsPath($projectRoot, $directoryName);
        $migrator = new Migrator($entityContext->Database, $path);
        $target = $args->get('--target');
        if ($target) {
            $count = $migrator->migrate($target->Value);
        } else {
            $count = $migrator->migrate();
        }

        if ($count > 0) {
            Console::$ForegroundColor = ConsoleColor::Green;
            Console::writeLine("Done.");
            Console::resetColor();
            exit;
        }

        Console::$ForegroundColor = ConsoleColor::Yellow;
        Console::writeLine("No migration has been applied!");
        Console::resetColor();
    }

    public function findMigrationsPath(string $projectRoot, string $directoryName): ?string
    {
        $directories = [];
        foreach (new DirectoryIterator($projectRoot) as $path) {
            if ($path->isDir() && !$path->isDot()) {
                if ($path->getFilename() == $directoryName) {
                    return $projectRoot . '/' . $directoryName;
                }
                $directories[] = $path->getFilename();
            }
        }

        foreach ($directories as $dir) {
            $path = $this->findMigrationsPath($projectRoot . '/' . $dir, $directoryName);
            if ($path) {
                return $path;
            }
        }

        return null;
    }
}
