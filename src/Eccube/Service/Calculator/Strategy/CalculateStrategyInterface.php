<?php

namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Entity\PurchaseInterface;
use Eccube\Service\Calculator\ShipmentItemCollection;

interface CalculateStrategyInterface
{
    public function execute(ShipmentItemCollection $ShipmentItems);

    public function setApplication(Application $app);

    public function setOrder(PurchaseInterface $Order);

    public function getTargetTypes();
}
