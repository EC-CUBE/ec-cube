<?php
namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;

class ShippingStrategy implements CalculateStrategyInterface
{
    protected $app;
    protected $Order;

    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    public function execute(&$OrderDetails)
    {
        // 送料をすべて足す
        $delivery_fee_total = array_reduce(
            array_map(
                function ($Shipping) {
                    return $Shipping->getShippingDeliveryFee();
                },
                $this->Order->getShippings()->toArray()
            ),
            function ($carry, $item) {
                return $carry += $item;
            }
        );
        $OrderDetail = new OrderDetail();
        $OrderDetail->setProductName("送料")
            ->setPrice($delivery_fee_total)
            ->setPriceIncTax($delivery_fee_total)
            ->setQuantity(1);
        $OrderDetails[] = $OrderDetail;
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
