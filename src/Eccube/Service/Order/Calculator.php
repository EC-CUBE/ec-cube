<?php
namespace Eccube\Service\Order;

abstract class Calculator {

    protected $app;

    public function __construct(\Eccube\Application $app) {
        $this->app = $app;
    }

    public function calcOrder(\Eccube\Entity\Order $order) {

    }

    public function calcOrderDetail(\Eccube\Entity\Order $order, \Eccube\Entity\OrderDetail $orderDetail) {

    }
}
