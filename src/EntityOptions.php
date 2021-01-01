<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity;

use Artister\System\Database\DbConnectionStringBuilder;
use Artister\System\Exceptions\ClassException;

class EntityOptions
{
    private ?string $Connection = null;
    private string $ContextType = EntityContext::class;

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function useConnection(string $connectionUri)
    {
        $builder = new DbConnectionStringBuilder($connectionUri);
        $this->Connection = $builder->build();
    }

    public function useContext(string $contextType)
    {
        if (!class_exists($contextType))
        {
            throw ClassException::classNotFound($contextType);
        }

        $parents = class_parents($contextType);
        if (!in_array(EntityContext::class, $parents)) {
            throw new \Exception("Custom EntityContext must inherent from ".EntityContext::class);
        }

        $this->ContextType = $contextType;
    }
}