<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Migrations;

use DevNet\System\Exceptions\MethodException;
use DevNet\System\PropertyTrait;

abstract class Migration
{
    use PropertyTrait;

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
            throw new MethodException("Method {$action} not supported");
        }

        $builder = new MigrationBuilder();
        $this->$action($builder);

        return $builder->Operations;
    }

    abstract public function up(MigrationBuilder $builder): void;

    abstract public function down(MigrationBuilder $builder): void;
}
