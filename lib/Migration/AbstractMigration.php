<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

use DevNet\System\Exceptions\PropertyException;

abstract class AbstractMigration
{
    private ?string $schema;
    private array $upOperations;
    private array $downOperations;

    public function __get(string $name)
    {
        if ($name == 'UpOperations') {
            return $this->build('up');
        }

        if ($name == 'DownOperations') {
            return $this->build('down');
        }

        if (property_exists($this, $name)) {
            throw new PropertyException("access to private property " . get_class($this) . "::" . $name);
        }

        throw new PropertyException("access to undefined property " . get_class($this) . "::" . $name);
    }

    public function __construct(?string $schema = null)
    {
        $this->schema = $schema;
    }

    public function build(string $action): array
    {
        if ($action != 'up' && $action != 'down') {
            throw new \Exception("Method {$action} not supported");
        }

        $builder = new MigrationBuilder($this->schema);
        $this->$action($builder);

        return $builder->Operations;
    }

    abstract public function up(MigrationBuilder $builder): void;

    abstract public function down(MigrationBuilder $builder): void;
}
