<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

use DevNet\Entity\Migration\Operations\Operation;

abstract class AbstractMigration
{
    protected Operation $UpOperation;
    protected Operation $DownOperation;

    public function __get(string $name)
    {
        if ($name == 'UpOperation') {
            return $this->build('up');
        }

        if ($name == 'DownOperation') {
            return $this->build('down');
        }

        return $this->$name;
    }

    public function build(string $action): Schema
    {
        if ($action != 'up' && $action != 'down') {
            throw new \Exception("Method {$action} not supoerted");
        }

        $schema = Operation::createSchema();
        $this->$action($schema);

        return $schema;
    }

    abstract public function up(Schema $schema): void;

    abstract public function down(Schema $schema): void;
}
