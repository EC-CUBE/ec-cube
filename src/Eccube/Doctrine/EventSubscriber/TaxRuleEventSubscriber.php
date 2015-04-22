<?php

namespace Eccube\Doctrine\EventSubscriber;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

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
            Events::postLoad,
            Events::postPersist,
            Events::postUpdate,
        );
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof \Eccube\Entity\ProductClass) {
            $entity->setPrice01IncTax($this->taxRateService->getPriceIncTax($entity->getPrice01(), $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->taxRateService->getPriceIncTax($entity->getPrice02(), $entity->getProduct(), $entity));
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof \Eccube\Entity\ProductClass) {
            $entity->setPrice01IncTax($this->taxRateService->getPriceIncTax($entity->getPrice01(), $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->taxRateService->getPriceIncTax($entity->getPrice02(), $entity->getProduct(), $entity));
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof \Eccube\Entity\ProductClass) {
            $entity->setPrice01IncTax($this->taxRateService->getPriceIncTax($entity->getPrice01(), $entity->getProduct(), $entity));
            $entity->setPrice02IncTax($this->taxRateService->getPriceIncTax($entity->getPrice02(), $entity->getProduct(), $entity));
        }
    }
}
