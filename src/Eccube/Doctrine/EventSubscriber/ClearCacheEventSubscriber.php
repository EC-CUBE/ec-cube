<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Eccube\Application;

class ClearCacheEventSubscriber implements EventSubscriber
{
    /**
     * @var array 対象のエンティティクラス名(FQDN)
     */
    private $classes;

    /**
     * @var Application
     */
    private $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->classes = array(
            'Eccube\Entity\BaseInfo',
            'Eccube\Entity\Category',
            'Eccube\Entity\PageLayout',
            'Eccube\Entity\Block',
            'Eccube\Entity\BlockPosition',
        );
    }

    public function getSubscribedEvents()
    {
        $options = $this->app['config']['doctrine_cache'];
        $clearCache = $options['result_cache']['clear_cache'];

        if ($clearCache) {
            return array(
                Events::postPersist,
                Events::postRemove,
                Events::postUpdate,
            );
        }
        // キャッシュの削除オプションが無効の場合は、イベントを定義せず空の配列を返す.
        return array();
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->app['monolog']->debug('clear result cache: postPersist');
        $this->clearCache($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->app['monolog']->debug('clear result cache: postRemove');
        $this->clearCache($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->app['monolog']->debug('clear result cache: postUpdate');
        $this->clearCache($args);
    }

    protected function clearCache(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $classes = $this->classes;
        foreach ($classes as $class) {
            if ($entity instanceof $class) {
                $this->app['monolog']->debug('clear result cache: '.$class);
                $cache = $args->getObjectManager()->getConfiguration()->getResultCacheImpl();
                $cache->deleteAll();
                break;
            }
        }
    }
}
