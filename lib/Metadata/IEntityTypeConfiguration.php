<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM\Metadata;

use DevNet\ORM\Metadata\EntityTypeBuilder;

interface IEntityTypeConfiguration
{
    public function getEntityName(): string;

    public function configure(EntityTypeBuilder $builder): void;
}
