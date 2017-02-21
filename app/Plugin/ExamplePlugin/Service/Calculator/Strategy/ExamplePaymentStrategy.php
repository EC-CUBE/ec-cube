<?php

namespace Plugin\ExamplePlugin\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;
use Eccube\Service\Calculator\OrderDetailCollection;
use Eccube\Service\Calculator\Strategy\CalculateStrategyInterface;

class ExamplePaymentStrategy implements CalculateStrategyInterface
{
    protected $app;
    protected $Order;

    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    public function execute(OrderDetailCollection $OrderDetails)
    {
        // OrderDetail に実施したい処理を記述する
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
