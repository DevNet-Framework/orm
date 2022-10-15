<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Tools;

use DevNet\Cli\Templating\CodeGeneratorProvider;

class MigrationGeneratorProvider extends CodeGeneratorProvider
{
    public function __construct()
    {
        parent::__construct('migration', 'Generate a database migration class file.', new MigrationGenerator());
    }
}
