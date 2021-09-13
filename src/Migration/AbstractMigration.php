<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

abstract class AbstractMigration
{
    protected array $UpOperations;
    protected array $DownOperations;

    public function __get(string $name)
    {
        if ($name == 'UpOperations') {
            return $this->build('up');
        }

        if ($name == 'DownOperations') {
            return $this->build('down');
        }

        return $this->$name;
    }

    public function build(string $action): array
    {
        if ($action != 'up' && $action != 'down') {
            throw new \Exception("Method {$action} not supoerted");
        }

        $builder = new MigrationBuilder();
        $this->$action($builder);

        return $builder->Operations;
    }

    abstract public function up(MigrationBuilder $builder): void;

    abstract public function down(MigrationBuilder $builder): void;
}
