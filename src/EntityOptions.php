<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Data\Entity;

use Artister\Data\Entity\Providers\Mysql\MysqlDataProvider;
use Artister\System\Database\DbConnection;
use Artister\System\Database\DbConnectionStringBuilder;
use Artister\System\Exceptions\ClassException;

class EntityOptions
{
    private DbConnection $Connection;
    private string $ContextType = EntityContext::class;
    private MysqlDataProvider $Provider;

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function useMysql(string $connectionUri)
    {
        $builder = new DbConnectionStringBuilder($connectionUri);
        $this->Connection = new DbConnection($builder->build());

        $this->Provider = new MysqlDataProvider($this->Connection);
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