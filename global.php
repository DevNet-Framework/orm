<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

use DevNet\CLI\Plugin\CodeGeneratorRegistry;
use DevNet\CLI\Plugin\CommandRegistry;
use DevNet\ORM\Tools\MigrateCommand;
use DevNet\ORM\Tools\MigrationGeneratorProvider;

/**
 * DevNet CLI package is not mandatory required by DevNet ORM package,
 * so we need to check first if the DevNet CLI is installed before registering the command.
 */
if (class_exists(CommandRegistry::class)) {
    CommandRegistry::register('migrate',MigrateCommand::class);
    CodeGeneratorRegistry::register('migration', MigrationGeneratorProvider::class);
}