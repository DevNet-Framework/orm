<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migrations;

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
                preg_match('/(?i)(\d+)_(\w+).php/', $file->getFilename(), $matches);
                $id = $matches[1] ?? null;
                $name = $matches[2] ?? null;
                if (!$id || !$name) {
                    continue;
                }

                $content = file_get_contents($file->getRealPath());
                preg_match('/(?i)namespace\s+([a-z0-9_\\\]+);/', $content, $matches);
                $namespace = $matches[1] ?? null;
                if (!$namespace) {
                    continue;
                }

                @include_once $file->getRealPath();

                $class = $namespace . "\\" . $name;
                if (class_exists($class)) {
                    $parents = class_parents($class);
                    if (in_array(Migration::class, $parents)) {
                        $migration = new stdClass();
                        $migration->Id = $id;
                        $migration->Type = $class;
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
