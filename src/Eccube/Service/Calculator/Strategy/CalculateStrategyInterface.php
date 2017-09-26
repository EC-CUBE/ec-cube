<?php

namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Entity\PurchaseInterface;
use Eccube\Service\Calculator\OrderItemCollection;

interface CalculateStrategyInterface
{
    public function execute(OrderItemCollection $OrderItems);

    public function setApplication(Application $app);

    public function setOrder(PurchaseInterface $Order);

    public function getTargetTypes();
}
