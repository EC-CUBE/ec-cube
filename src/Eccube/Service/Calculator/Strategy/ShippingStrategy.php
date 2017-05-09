<?php
namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Entity\ShipmentItem;
use Eccube\Service\Calculator\ShipmentItemCollection;

class ShippingStrategy implements CalculateStrategyInterface
{
    /* @var Application $app */
    protected $app;

    /* @var Order $Order */
    protected $Order;

    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    public function execute(ShipmentItemCollection $ShipmentItems)
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

//        // 送料が存在しない場合は追加
//        if (!$ShipmentItems->hasProductByName('送料')) {
//            $ShipmentItem = new ShipmentItem();
//            $ShipmentItem->setProductName("送料")
//                ->setPrice($delivery_fee_total)
//                ->setPriceIncTax($delivery_fee_total)
//                ->setTaxRate(0)
//                ->setQuantity(1);
//            $this->Order->setDeliveryFeeTotal($delivery_fee_total);
//            $ShipmentItems->append($ShipmentItem);
//        }
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
