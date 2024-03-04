<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Storage;

interface ISqlGenerationHelper
{
    /**
     * Generates the delimited SQL representation of an identifier (column name, table name, etc.).
     */
    public function delimitIdentifier(string $name, ?string $schema = null): string;
}
