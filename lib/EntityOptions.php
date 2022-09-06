<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity;

use DevNet\Entity\Providers\IEntityDataProvider;
use DevNet\System\ObjectTrait;

class EntityOptions
{
    use ObjectTrait;

    private IEntityDataProvider $provider;

    public function get_ContextType(): string
    {
        return $this->contextType;
    }

    public function get_Provider(): IEntityDataProvider
    {
        return $this->provider;
    }

    public function useProvider(IEntityDataProvider $provider): void
    {
        $this->provider = $provider;
    }
}
