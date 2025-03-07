<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Tools;

use DevNet\CLI\Plugin\CodeModel;
use DevNet\CLI\Plugin\ICodeGenerator;
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
        $name      = $parameters['--name'] ?? 'MyMigration';
        $name      = ucfirst($name);
        $output    = $parameters['--output'] ?? 'Migrations';
        $namespace = $parameters['--prefix'] ?? 'Application';
        $namespace = $namespace .'\\' . str_replace('/', '\\', $output);
        $namespace = trim($namespace, '\\');
        $namespace = ucwords($namespace, '\\');

        $this->content = new StringBuilder();
        $this->content->appendLine('<?php');
        $this->content->appendLine();
        $this->content->appendLine("namespace {$namespace};");
        $this->content->appendLine();
        $this->content->appendLine('use DevNet\ORM\Migrations\Migration;');
        $this->content->appendLine('use DevNet\ORM\Migrations\MigrationBuilder;');
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

        return [new CodeModel(date('YmdHis') . '_' . $name . '.php', $this->content, $output)];
    }
}
