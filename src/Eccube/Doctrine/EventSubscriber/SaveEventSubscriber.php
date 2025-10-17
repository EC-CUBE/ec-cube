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
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Member;
use Eccube\Request\Context;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class SaveEventSubscriber
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
     * @param LifecycleEventArgs<\Doctrine\ORM\EntityManagerInterface> $args
     *
     * @return void
     */
    public function prePersist(LifecycleEventArgs $args): void
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
     * @param LifecycleEventArgs<\Doctrine\ORM\EntityManagerInterface> $args
     *
     * @return void
     */
    public function preUpdate(LifecycleEventArgs $args): void
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
