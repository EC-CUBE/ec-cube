<?php

namespace Eccube\Service\Order;

class TaxCalculator implements Calculator {
    
    public function calculate(Eccube\Entity\Order $order) {
        $tax = $order->getTax();
        $orderDetail = $order->getOrderDetail();
        foreach ($orderDetail as $detail) {
            $price = $detail->getPrice();
            $quantity = $detail->getQuantity();
            
        }
        return $tax;
    }

    public function getName() {
        return 'calc.tax';
    }
}
