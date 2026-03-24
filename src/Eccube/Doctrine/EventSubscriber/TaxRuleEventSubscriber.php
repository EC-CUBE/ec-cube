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

use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Eccube\Entity\ProductClass;
use Eccube\Service\TaxRuleService;

class TaxRuleEventSubscriber
{
    /**
     * @var TaxRuleService
     */
    protected $taxRuleService;

    /**
     * TaxRuleEventSubscriber constructor.
     */
    public function __construct(TaxRuleService $taxRuleService)
    {
        $this->taxRuleService = $taxRuleService;
    }

    public function getTaxRuleService()
    {
        return $this->taxRuleService;
    }

    public function prePersist(PrePersistEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice01() === null ? '0' : $entity->getPrice01(),
                $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice02() === null ? '0' : $entity->getPrice02(),
                $entity->getProduct(), $entity));
        }
    }

    public function postLoad(PostLoadEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice01() === null ? '0' : $entity->getPrice01(),
                $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice02() === null ? '0' : $entity->getPrice02(),
                $entity->getProduct(), $entity));
        }
    }

    public function postPersist(PostPersistEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice01() === null ? '0' : $entity->getPrice01(),
                $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice02() === null ? '0' : $entity->getPrice02(),
                $entity->getProduct(), $entity));
        }
    }

    public function postUpdate(PostUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice01() === null ? '0' : $entity->getPrice01(),
                $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->getTaxRuleService()->getPriceIncTax($entity->getPrice02() === null ? '0' : $entity->getPrice02(),
                $entity->getProduct(), $entity));
        }
    }
}
