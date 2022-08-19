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
use DevNet\System\Exceptions\PropertyException;

class EntityOptions
{
    use \DevNet\System\Extension\ExtensionTrait;

    private string $contextType = EntityContext::class;
    private IEntityDataProvider $provider;

    public function __get(string $name)
    {
        if ($name == 'ContextType') {
            return $this->contextType;
        }
        
        if ($name == 'Provider') {
            return $this->provider;
        }

        if (property_exists($this, $name)) {
            throw new PropertyException("access to private property " . get_class($this) . "::" . $name);
        }

        throw new PropertyException("access to undefined property " . get_class($this) . "::" . $name);
    }

    public function useContext(string $contextType)
    {
        if (!class_exists($contextType)) {
            throw ClassException::classNotFound($contextType);
        }

        $parents = class_parents($contextType);
        if (!in_array(EntityContext::class, $parents)) {
            throw new \Exception("Custom EntityContext must inherent from " . EntityContext::class);
        }

        $this->contextType = $contextType;
    }

    public function useProvider(IEntityDataProvider $provider)
    {
        $this->provider = $provider;
    }
}
