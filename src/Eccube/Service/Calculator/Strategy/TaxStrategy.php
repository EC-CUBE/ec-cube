<?php

namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Entity\ShipmentItem;
use Eccube\Service\Calculator\ShipmentItemCollection;

class TaxStrategy implements CalculateStrategyInterface
{
    protected $app;
    protected $Order;

    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    public function execute(ShipmentItemCollection $ShipmentItems)
    {
        // map でやりたい
        /* @var ShipmentItem $ShipmentItem */
        foreach ($ShipmentItems as $ShipmentItem) {
            $tax = $this->app['eccube.service.tax_rule']
                ->calcTax($ShipmentItem->getPrice(), $ShipmentItem->getTaxRate(), $ShipmentItem->getTaxRule());
            $ShipmentItem->setPriceIncTax($ShipmentItem->getPrice() + $tax);
        }
    }

    public function setApplication(Application $app)
    {
        $this->app = $app;
        return $this;
    }

    public function setOrder(Order $Order)
    {
        $this->Order = $Order;
        return $this;
    }
}
