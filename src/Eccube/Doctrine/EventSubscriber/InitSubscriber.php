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

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Driver\Middleware as MiddlewareInterface;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;

/**
 * DBAL 4 middleware that sets the timezone to UTC on connect.
 *
 * Replaces the former postConnect event listener (ConnectionEventArgs was removed in DBAL 4).
 */
class InitSubscriber implements MiddlewareInterface
{
    public function wrap(Driver $driver): Driver
    {
        return new class($driver) extends AbstractDriverMiddleware {
            public function connect(
                #[\SensitiveParameter]
                array $params,
            ): DriverConnection {
                $connection = parent::connect($params);

                // Determine the database type from the driver class hierarchy
                $driverClass = get_class($this);
                // Walk up the decoration chain - check the actual driver name from params or class
                $innerDriverClass = $this->detectDriverType();

                if ($innerDriverClass === 'mysql') {
                    $connection->exec("SET SESSION time_zone = '+00:00'");
                } elseif ($innerDriverClass === 'pgsql') {
                    $connection->exec("SET TIME ZONE 'UTC'");
                }

                return $connection;
            }

            private function detectDriverType(): string
            {
                // Use reflection to get the wrapped driver to determine DB type
                $ref = new \ReflectionClass(AbstractDriverMiddleware::class);
                $prop = $ref->getProperty('wrappedDriver');
                $driver = $prop->getValue($this);

                // Unwrap nested middleware layers
                while ($driver instanceof AbstractDriverMiddleware) {
                    $prop = $ref->getProperty('wrappedDriver');
                    $driver = $prop->getValue($driver);
                }

                $className = get_class($driver);

                if (str_contains($className, 'MySQL') || str_contains($className, 'mysqli')) {
                    return 'mysql';
                }
                if (str_contains($className, 'PgSQL') || str_contains($className, 'PostgreSQL') || str_contains($className, 'pgsql')) {
                    return 'pgsql';
                }

                return 'other';
            }
        };
    }
}
