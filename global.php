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

CodeGeneratorRegistry::register('migration', MigrationGeneratorProvider::class);
CommandRegistry::register('migrate', MigrateCommand::class);