<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Providers\PostgreSql;

use DevNet\Entity\Storage\ISqlGenerationHelper;

class PostgreSqlHelper implements ISqlGenerationHelper
{
    public function delimitIdentifier(string $name, ?string $schema = null): string
    {
        if ($schema) {
            return "\"{$schema}\".\"{$name}\"";
        }

        return "\"{$name}\"";
    }
}