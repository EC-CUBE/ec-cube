<?php

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
