<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
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