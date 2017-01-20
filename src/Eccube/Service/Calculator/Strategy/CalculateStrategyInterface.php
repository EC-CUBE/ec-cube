<?php

namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Service\Calculator\OrderDetailCollection;

interface CalculateStrategyInterface
{
    public function execute(OrderDetailCollection $OrderDetails);

    public function setApplication(Application $app);

    public function setOrder(Order $Order);
}
