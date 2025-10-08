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

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;

#[AsDoctrineListener(event: Events::postConnect)]
class InitSubscriber
{
    /**
     * @param ConnectionEventArgs $args
     *
     * @return void
     */
    public function __invoke(ConnectionEventArgs $args): void
    {
        $db = $args->getConnection();
        $platform = $args->getConnection()->getDatabasePlatform();

        if ($platform instanceof MySQLPlatform) {
            $db->executeQuery("SET SESSION time_zone = '+00:00'");
        } elseif ($platform instanceof PostgreSQLPlatform) {
            $db->executeQuery("SET TIME ZONE 'UTC'");
        } elseif ($platform instanceof SqlitePlatform) {
            // FIXME schema updateが通らないので一旦コメントアウト.
            // $db->executeQuery("PRAGMA foreign_keys = ON");
        }
    }
}
