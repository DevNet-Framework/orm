<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity;

use Artister\Data\Entity\Providers\Mysql\MysqlConnection;
use Artister\Data\Entity\Providers\Mysql\MysqlDataProvider;
use Artister\Data\Entity\Storage\IEntityDataProvider;
use Artister\System\Exceptions\ClassException;

class EntityOptions
{
    private string $ContextType = EntityContext::class;
    private IEntityDataProvider $Provider;

    public function __get(string $name)
    {
        return $this->$name;
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

    public function useProvider(IEntityDataProvider $provider)
    {
        $this->Provider = $provider;
    }

    public function useMysql(string $connectionUri)
    {
        $this->useProvider(new MysqlDataProvider(new MysqlConnection($connectionUri)));
    }
}