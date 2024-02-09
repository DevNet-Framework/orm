<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\MySql;

use DevNet\Entity\Storage\ISqlGenerationHelper;

class MySqlHelper implements ISqlGenerationHelper
{
    public function delimitIdentifier(string $name, ?string $schema = null): string
    {
        if ($schema) {
            return "`{$schema}`.`{$name}`";
        }

        return "`{$name}`";
    }
}
