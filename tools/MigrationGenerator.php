<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Tools;

use DevNet\Cli\Templating\CodeModel;
use DevNet\Cli\Templating\ICodeGenerator;
use DevNet\System\Text\StringBuilder;

class MigrationGenerator implements ICodeGenerator
{
    private StringBuilder $content;

    public function __construct()
    {
        $this->content = new StringBuilder();
    }

    public function generate(array $parameters): array
    {
        $output    = $parameters['--output'] ?? 'Migrations';
        $namespace = str_replace('/', '\\', $output);
        $namespace = 'Application\\' . $namespace;       
        $namespace = trim($namespace, '\\');
        $namespace = ucwords($namespace, '\\');
        $name      = $parameters['--name'] ?? 'MyMigration';
        $name      = ucfirst($name);

        $this->content = new StringBuilder();
        $this->content->appendLine('<?php');
        $this->content->appendLine();
        $this->content->appendLine("namespace {$namespace};");
        $this->content->appendLine();
        $this->content->appendLine('use DevNet\Entity\Migrations\Migration;');
        $this->content->appendLine('use DevNet\Entity\Migrations\MigrationBuilder;');
        $this->content->appendLine();
        $this->content->appendLine("class {$name} extends Migration");
        $this->content->appendLine('{');
        $this->content->appendLine('    public function up(MigrationBuilder $builder): void');
        $this->content->appendLine('    {');
        $this->content->appendLine('    }');
        $this->content->appendLine();
        $this->content->appendLine('    public function down(MigrationBuilder $builder): void');
        $this->content->appendLine('    {');
        $this->content->appendLine('    }');
        $this->content->appendLine('}');

        return [new CodeModel(date('Ymdhis') . '_' . $name . '.php', $this->content, $output)];
    }
}
