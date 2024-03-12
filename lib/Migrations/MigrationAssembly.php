<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Migrations;

use DevNet\System\Collections\Enumerator;
use DevNet\System\Collections\IEnumerable;
use DevNet\System\MethodTrait;
use DevNet\System\PropertyTrait;
use DirectoryIterator;
use stdClass;

class MigrationAssembly implements IEnumerable
{
    use MethodTrait;
    use PropertyTrait;

    private array $migrations = [];

    public function __construct(string $directory)
    {
        foreach (new DirectoryIterator($directory) as $file) {
            if ($file->isFile()) {
                preg_match('%(\d+).*\.php%i', $file->getFilename(), $matches);
                $id = $matches[1] ?? null;
                if (!$id) {
                    continue;
                }

                $content = file_get_contents($file->getRealPath());
                preg_match_all('%namespace\s+([a-z][a-z0-9_\\\]*)\s*;|class\s+([a-z][a-z0-9_]*)|/\*(.|\n)*?\*/|//.*%i', $content, $matches);

                $namespace = null;
                $namespaces = $matches[1] ?? [];
                foreach ($namespaces as $namespace) {
                    if ($namespace) {
                        break;
                    }
                }

                $class = null;
                $classes = $matches[2] ?? [];
                foreach ($classes as $class) {
                    if ($class) {
                        break;
                    }
                }

                if (!$namespace || !$class) {
                    continue;
                }

                @include_once $file->getRealPath();

                $className = $namespace . '\\' . $class;
                if (class_exists($className)) {
                    $parents = class_parents($className);
                    if (in_array(Migration::class, $parents)) {
                        $migration = new stdClass();
                        $migration->Id = $id;
                        $migration->Type = $className;
                        $this->migrations[] = $migration;
                    }
                }
            }
        }
    }

    public function findMigrationId(string $target): ?int
    {
        if ($target == '0') {
            return 0;
        }

        foreach ($this->migrations as $migration) {
            if (str_starts_with($target, $migration->Id)) {
                return $migration->Id;
            }
        }
        return null;
    }

    public function getIterator(): Enumerator
    {
        return new Enumerator($this->migrations);
    }
}
