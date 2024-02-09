<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
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
