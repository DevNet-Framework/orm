<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Entity\Providers;

use Artister\Entity\EntityOptions;
use Artister\Entity\Providers\MySql\MySqlConnection;
use Artister\Entity\Providers\MySql\MySqlDataProvider;
use Artister\Entity\Providers\PostgreSql\PostgreSqlConnection;
use Artister\Entity\Providers\PostgreSql\PostgreSqlDataProvider;
use Artister\Entity\Providers\Sqlite\SqliteDataProvider;
use Artister\Entity\Providers\Sqlite\SqlitelConnection;

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
