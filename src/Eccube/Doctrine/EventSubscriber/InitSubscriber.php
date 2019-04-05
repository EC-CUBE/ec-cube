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

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;

class InitSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::postConnect];
    }

    /**
     * @param ConnectionEventArgs $args
     */
    public function postConnect(ConnectionEventArgs $args)
    {
        $db = $args->getConnection();
        $platform = $args->getDatabasePlatform()->getName();

        if ($platform === 'mysql') {
            $db->executeQuery("SET SESSION time_zone = '+00:00'");
        } elseif ($platform === 'postgresql') {
            $db->executeQuery("SET TIME ZONE 'UTC'");
        } elseif ($platform === 'sqlite') {
            // FIXME schema updateが通らないので一旦コメントアウト.
            // $db->executeQuery("PRAGMA foreign_keys = ON");
        }
    }
}
