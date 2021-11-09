<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
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
