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

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class PointEventSubscriber implements EventSubscriber
{
    /**
     * @var string 
     */
    private $point_rule;

    /**
     * @var \Eccube\Service\TaxRuleService
     */
    private $taxRateService;

    /**
     * __construct
     * 
     * @param string $point_rule
     * @param \Eccube\Service\TaxRuleService $taxRateService
     */
    public function __construct($point_rule, \Eccube\Service\TaxRuleService $taxRateService)
    {
        $this->point_rule = $point_rule;
        $this->taxRateService = $taxRateService;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::postLoad,
            Events::postPersist,
            Events::postUpdate,
        );
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof \Eccube\Entity\ProductClass) {
            $point = $entity->getPrice02() * $entity->getPointRate() / 100;
            $entity->setPoint($this->taxRateService->roundByCalcRule($point, $this->point_rule));
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof \Eccube\Entity\ProductClass) {
            $point = $entity->getPrice02() * $entity->getPointRate() / 100;
            $entity->setPoint($this->taxRateService->roundByCalcRule($point, $this->point_rule));
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof \Eccube\Entity\ProductClass) {
            $point = $entity->getPrice02() * $entity->getPointRate() / 100;
            $entity->setPoint($this->taxRateService->roundByCalcRule($point, $this->point_rule));
        }
    }
}
