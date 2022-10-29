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
    public string $ContextType = EntityContext::class;
    public string $ProviderType = '';
    public string $ConnectionString = '';

    public function __construct(string $providerType = '', string $connectionString = '')
    {
        $this->ProviderType = $providerType;
        $this->ConnectionString = $connectionString;
    }
}
