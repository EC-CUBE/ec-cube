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
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Eccube\Entity\ProductClass;
use Eccube\Service\TaxRuleService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TaxRuleEventSubscriber implements EventSubscriber
{
    /**
     * @var TaxRuleService
     */
    protected $container;

    /**
     * TaxRuleEventSubscriber constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getTaxRuleService()
    {
        return $this->container->get(TaxRuleService::class);
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::postLoad,
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice01(),
                $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice02(),
                $entity->getProduct(), $entity));
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice01(),
                $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice02(),
                $entity->getProduct(), $entity));
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice01(),
                $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice02(),
                $entity->getProduct(), $entity));
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice01(),
                $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice02(),
                $entity->getProduct(), $entity));
        }
    }
}
