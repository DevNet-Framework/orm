<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

use DevNet\System\Collections\Enumerator;
use DevNet\System\Collections\IEnumerable;
use stdClass;

class MigrationAssembly implements IEnumerable
{
    use \DevNet\System\Extension\ExtensionTrait;

    private array $Migrations = [];

    public function __construct(string $namespace, string $directory)
    {
        $files = array_diff(scandir($directory), array('.', '..'));
        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            preg_match('%(\d+)_(\w+)%', $filename, $matches);
            if ($matches) {
                $file = $directory . "/" . $filename . ".php";
                if (file_exists($file)) {
                    require_once($file);
                }

                $class = $namespace . "\\" . $matches[2];
                if (class_exists($class)) {
                    $parents = class_parents($class);
                    if (in_array(AbstractMigration::class, $parents)) {
                        $migration = new stdClass();
                        $migration->Id = (int)$matches[1];
                        $migration->Name = $matches[2];
                        $migration->Type = $class;
                        $this->Migrations[] = $migration;
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

        foreach ($this->Migrations as $migration) {
            if (str_starts_with($target, $migration->Id)) {
                return $migration->Id;
            }
        }
        return null;
    }

    public function getIterator(): Enumerator
    {
        return new Enumerator($this->Migrations);
    }
}