<?php

namespace Eccube\Service\Order;

// ポイント計算
class PointCalculator extends Calculator {
    public $baseInfo;
    public $customer;

    public function __construct(\Eccube\Application $app) {
        parent::__construct($app);
        $this->baseInfo = $this->app['eccube.repository.baseinfo']->find(1);
    }

    public function setBaseInfo($baseInfo) {
        $this->baseInfo = $baseInfo;
    }

    public function setCustomer($customer) {
        $this->customer = $customer;
    }

    public function calcOrder(\Eccube\Entity\Order $order) {
        parent::calcOrder($order);
        if (!is_null($this->customer)) {
            // 誕生日判定してポイントつける
            // $order->setBirthPoint(10);
        }
    }

    public function calcOrderDetail(\Eccube\Entity\Order $order, \Eccube\Entity\OrderDetail $orderDetail) {
        parent::calcOrderDetail($order, $orderDetail);
        $price = $orderDetail->getPrice();
        $quantity = $orderDetail->getQuantiry();
        $point = $this->calcPoint($price, $quantity);
        $order->setAddPoint($order->getAddPoint() + $point);
    }

    public function calcPoint($price, $quantiry) {
        return 0;// ポイント計算する処理;
    }
}