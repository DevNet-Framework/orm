<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

use DevNet\Cli\Commands\CommandRegistry;
use DevNet\Cli\Templating\CodeGeneratorRegistry;
use DevNet\Entity\Tools\MigrateCommand;
use DevNet\Entity\Tools\MigrationGeneratorProvider;

/**
 * DevNet CLI package is not mandatory required by DevNet Entity package,
 * so we need to check first if the DevNet CLI is installed before registering the command.
 */
if (class_exists(CommandRegistry::class)) {
    CommandRegistry::register('migrate', MigrateCommand::class);
    CodeGeneratorRegistry::register('migration', MigrationGeneratorProvider::class);
}