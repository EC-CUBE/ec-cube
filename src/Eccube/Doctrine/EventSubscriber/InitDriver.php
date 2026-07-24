<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Doctrine\EventSubscriber;

use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;

class InitDriver extends AbstractDriverMiddleware
{
    #[\Override]
    public function connect(#[\SensitiveParameter] array $params): DriverConnection
    {
        $connection = parent::connect($params);

        $driver = (string) ($params['driver'] ?? '');
        if (str_contains($driver, 'mysql')) {
            $connection->exec("SET SESSION time_zone = '+00:00'");
        } elseif (str_contains($driver, 'pgsql') || str_contains($driver, 'postgres')) {
            $connection->exec("SET TIME ZONE 'UTC'");
        }
        // sqlite は schema update が通らないため PRAGMA foreign_keys は設定しない (従来踏襲)

        return $connection;
    }
}
