<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migration;

use DevNet\System\ObjectTrait;

abstract class AbstractMigration
{
    use ObjectTrait;

    private ?string $schema;

    public function __construct(?string $schema = null)
    {
        $this->schema = $schema;
    }

    public function get_UpOperations(): array
    {
        return $this->build('up');
    }

    public function get_DownOperations(): array
    {
        return $this->build('down');
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
