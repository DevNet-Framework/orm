<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity;

use DevNet\Entity\Providers\IEntityDataProvider;
use DevNet\System\Exceptions\ClassException;
use DevNet\System\ObjectTrait;

class EntityOptions
{
    use ObjectTrait;

    private string $contextType = EntityContext::class;
    private IEntityDataProvider $provider;

    public function get_ContextType(): string
    {
        return $this->contextType;
    }

    public function get_Provider(): IEntityDataProvider
    {
        return $this->provider;
    }

    public function useContext(string $contextType): void
    {
        if (!class_exists($contextType)) {
            throw new ClassException("Could not find class {$contextType}", 0, 1);
        }

        $parents = class_parents($contextType);
        if (!in_array(EntityContext::class, $parents)) {
            throw new ClassException("Custom EntityContext must inherent from " . EntityContext::class, 0, 1);
        }

        $this->contextType = $contextType;
    }

    public function useProvider(IEntityDataProvider $provider): void
    {
        $this->provider = $provider;
    }
}
