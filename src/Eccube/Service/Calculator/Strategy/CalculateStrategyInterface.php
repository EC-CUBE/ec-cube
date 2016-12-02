<?php

namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;

interface CalculateStrategyInterface
{
    // 引数に型パラメータを指定したい
    public function execute(&$OrderDetails);

    public function setApplication(Application $app);

    public function setOrder(Order $Order);
}
