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
use Eccube\Entity\OrderDetail;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ShipmentItem;

class TaxRuleEventSubscriber implements EventSubscriber
{
    /**
     * @var \Eccube\Service\TaxRuleService
     */
    private $taxRateService;

    public function __construct(\Eccube\Service\TaxRuleService $taxRateService)
    {
        $this->taxRateService = $taxRateService;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::postLoad,
            Events::postPersist,
            Events::postUpdate,
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->taxRateService->getPriceIncTax($entity->getPrice01(), $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->taxRateService->getPriceIncTax($entity->getPrice02(), $entity->getProduct(), $entity));
        }
        if ($entity instanceof OrderDetail) {
            $entity->setPriceIncTax($entity->getPrice() + $this->taxRateService->calcTax($entity->getPrice(), $entity->getTaxRate(), $entity->getTaxRule()));
        }
        if ($entity instanceof ShipmentItem) {
            $entity->setPriceIncTax($this->taxRateService->getPriceIncTax($entity->getPrice(), $entity->getProduct(), $entity->getProductClass()));
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->taxRateService->getPriceIncTax($entity->getPrice01(), $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->taxRateService->getPriceIncTax($entity->getPrice02(), $entity->getProduct(), $entity));
        }
        if ($entity instanceof OrderDetail) {
            $entity->setPriceIncTax($entity->getPrice() + $this->taxRateService->calcTax($entity->getPrice(), $entity->getTaxRate(), $entity->getTaxRule()));
        }
        if ($entity instanceof ShipmentItem) {
            $entity->setPriceIncTax($this->taxRateService->getPriceIncTax($entity->getPrice(), $entity->getProduct(), $entity->getProductClass()));
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->taxRateService->getPriceIncTax($entity->getPrice01(), $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->taxRateService->getPriceIncTax($entity->getPrice02(), $entity->getProduct(), $entity));
        }
        if ($entity instanceof OrderDetail) {
            $entity->setPriceIncTax($entity->getPrice() + $this->taxRateService->calcTax($entity->getPrice(), $entity->getTaxRate(), $entity->getTaxRule()));
        }
        if ($entity instanceof ShipmentItem) {
            $entity->setPriceIncTax($this->taxRateService->getPriceIncTax($entity->getPrice(), $entity->getProduct(), $entity->getProductClass()));
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof ProductClass) {
            $entity->setPrice01IncTax($this->taxRateService->getPriceIncTax($entity->getPrice01(), $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->taxRateService->getPriceIncTax($entity->getPrice02(), $entity->getProduct(), $entity));
        }
        if ($entity instanceof OrderDetail) {
            $entity->setPriceIncTax($entity->getPrice() + $this->taxRateService->calcTax($entity->getPrice(), $entity->getTaxRate(), $entity->getTaxRule()));
        }
        if ($entity instanceof ShipmentItem) {
            $entity->setPriceIncTax($this->taxRateService->getPriceIncTax($entity->getPrice(), $entity->getProduct(), $entity->getProductClass()));
        }
    }
}
