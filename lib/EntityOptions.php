<?php

/**
 * @author      Mohammed Moussaoui
 * @license     MIT license. For more license information, see the LICENSE file in the root directory.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\ORM;

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
