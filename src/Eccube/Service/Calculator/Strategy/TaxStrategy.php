<?php

namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Entity\PurchaseInterface;
use Eccube\Entity\OrderItem;
use Eccube\Service\Calculator\OrderItemCollection;

class TaxStrategy implements CalculateStrategyInterface
{
    protected $app;
    protected $Order;

    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    public function execute(OrderItemCollection $OrderItems)
    {
        // map でやりたい
        /* @var OrderItem $OrderItem */
        foreach ($OrderItems as $OrderItem) {
            $tax = $this->app['eccube.service.tax_rule']
                ->calcTax($OrderItem->getPrice(), $OrderItem->getTaxRate(), $OrderItem->getTaxRule());
            $OrderItem->setPriceIncTax($OrderItem->getPrice() + $tax);
        }
    }

    public function setApplication(Application $app)
    {
        $this->app = $app;
        return $this;
    }

    public function setOrder(PurchaseInterface $Order)
    {
        $this->Order = $Order;
        return $this;
    }

    public function getTargetTypes()
    {
        return [Order::class];
    }
}
