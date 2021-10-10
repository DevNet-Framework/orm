<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Entity\Providers;

use DevNet\Entity\EntityOptions;
use DevNet\Entity\Providers\MySql\MySqlConnection;
use DevNet\Entity\Providers\MySql\MySqlDataProvider;
use DevNet\Entity\Providers\PostgreSql\PostgreSqlConnection;
use DevNet\Entity\Providers\PostgreSql\PostgreSqlDataProvider;
use DevNet\Entity\Providers\Sqlite\SqliteDataProvider;
use DevNet\Entity\Providers\Sqlite\SqlitelConnection;

class EntityOptionsExtensions
{
    public static function useMySql(EntityOptions $options, string $connectionUri)
    {
        $options->useProvider(new MySqlDataProvider(new MySqlConnection($connectionUri)));
    }

    public static function usePostgreSql(EntityOptions $options, string $connectionUri)
    {
        $options->useProvider(new PostgreSqlDataProvider(new PostgreSqlConnection($connectionUri)));
    }

    public static function useSqlite(EntityOptions $options, string $connectionUri)
    {
        $options->useProvider(new SqliteDataProvider(new SqlitelConnection($connectionUri)));
    }
}
