<?php
namespace Eccube\Service\Calculator\Strategy;

use Eccube\Entity\OrderDetail;

class ShippingStrategy implements CalculateStrategyInterface
{
    public function execute(&$OrderDetails)
    {
        $OrderDetail = new OrderDetail();
        $OrderDetail->setProductName("送料")
            ->setPrice(1000)
            ->setPriceIncTax(1000)
            ->setQuantity(1);
        $OrderDetails[] = $OrderDetail;
    }
}
