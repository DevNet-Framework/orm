<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Tools;

use DevNet\Entity\EntityContext;
use DevNet\Entity\EntityOptions;
use DevNet\Entity\Migration\Migrator;
use DevNet\System\Command\CommandEventArgs;
use DevNet\System\Command\CommandLine;
use DevNet\System\Command\ICommandHandler;
use DevNet\System\Configuration\ConfigurationBuilder;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;

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
        $configBuilder = new ConfigurationBuilder();
        $settingsPath = $projectRoot . '/' . 'settings.json';
        if (file_exists($settingsPath)) {
            $configBuilder->addJsonFile($settingsPath);
        }

        $configuration = $configBuilder->build();
        $connectionString = $configuration->getValue('database:connection');
        $connection = $args->get('--connection');
        if ($connection) {
            if ($connection->Value) {
                $connectionString = ucwords($connection->Value, '\\');
            }
        }

        $providerType = $configuration->getValue('database:provider');
        $provider = $args->get('--provider');
        if ($provider) {
            if ($provider->Value) {
                $providerType = ucwords($provider->Value, '\\');
            }
        }

        $entityOptions = new EntityOptions($connectionString, $providerType);
        $entityContext = new EntityContext($entityOptions);
        if ($entityContext) {
            Console::writeLine("Build started...");
            $path = 'Migrations';
            $directory = $args->get('--directory');
            if ($directory) {
                if ($directory->Value) {
                    $path = ucwords($directory->Value, '/');
                }
            }
            $namespace = 'Application\\' . str_replace('/', '\\', $path);
            $migrator = new Migrator($entityContext->Database, $namespace, $projectRoot . '/' . $path);
            $target = $args->get('--target');
            if ($target) {
                $migrator->migrate($target->Value);
            } else {
                $migrator->migrate();
            }
        } else {
            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine("EntityContext not found.");
            Console::resetColor();
            return;
        }

        Console::$ForegroundColor = ConsoleColor::Green;
        Console::writeLine("Done.");
        Console::resetColor();
    }
}