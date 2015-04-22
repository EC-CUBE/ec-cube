<?php

namespace Eccube\Service\Order;

// 税率計算(軽減税率非対応)
class TaxCalculator extends Calculator {

    public $taxRule;

    public function __construct(\Eccube\Application $app) {
        parent::__construct($app);
    }

    public function setTaxRule($taxRule) {
        $this->taxRule = $taxRule;
    }

    public function calcOrderDetail(\Eccube\Entity\Order $order, \Eccube\Entity\OrderDetail $orderDetail) {
        parent::calcOrderDetail($order, $orderDetail);
        $price = $orderDetail->getPrice();
        $quantity = $orderDetail->getQuantiry();
        $tax = $price * $quantity * $this->taxRule->getTaxRate();
        $order->setTax($order->getTax() + $tax);
        $orderDetail->setTaxRate($this->taxRule->getTaxRate());
        $orderDetail->setTaxRuleId($this->taxRule->getTaxRuleId());
    }
}

