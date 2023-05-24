<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity;

class EntityOptions
{
    public string $ProviderType     = '';
    public string $ConnectionString = '';
    public ?string $DefaultSchema   = null;

    public function __construct(string $connectionString = '', string $providerType = '')
    {
        $this->ProviderType = $providerType;
        $this->ConnectionString = $connectionString;
    }
}
