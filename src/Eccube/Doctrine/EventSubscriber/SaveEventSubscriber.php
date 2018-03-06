<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Member;
use Eccube\Request\Context;

class SaveEventSubscriber implements EventSubscriber
{
    /**
     * @var Context
     */
    protected $requestContext;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @param Context $requestContext
     */
    public function __construct(Context $requestContext, EccubeConfig $eccubeConfig)
    {
        $this->requestContext = $requestContext;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setCreateDate')) {
            $entity->setCreateDate(new \DateTime());
        }
        if (method_exists($entity, 'setUpdateDate')) {
            $entity->setUpdateDate(new \DateTime());
        }
        if (method_exists($entity, 'setCurrencyCode')) {
            $currency = $this->eccubeConfig->get('currency');
            $entity->setCurrencyCode($currency);
        }
        if (method_exists($entity, 'setCreator')) {
            $user = $this->requestContext->getCurrentUser();
            if ($user instanceof Member) {
                $entity->setCreator($user);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setUpdateDate')) {
            $entity->setUpdateDate(new \DateTime());
        }

        if (method_exists($entity, 'setCreator')) {
            $user = $this->requestContext->getCurrentUser();
            if ($user instanceof Member) {
                $entity->setCreator($user);
            }
        }
    }
}
