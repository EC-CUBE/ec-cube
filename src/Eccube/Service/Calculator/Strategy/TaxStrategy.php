<?php

namespace Eccube\Service\Calculator\Strategy;

use Eccube\Entity\OrderDetail;

class TaxStrategy implements CalculateStrategyInterface
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    public function execute(&$OrderDetails)
    {
        foreach ($OrderDetails as &$OrderDetail) {
            $tax = $this->app['eccube.service.tax_rule']
                ->calcTax($OrderDetail->getPrice(), $OrderDetail->getTaxRate(), $OrderDetail->getTaxRule());
            $OrderDetail->setPriceIncTax($OrderDetail->getPrice() + $tax);
        }
    }
}
