<?php
namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;
use Eccube\Service\Calculator\OrderDetailCollection;

class ShippingStrategy implements CalculateStrategyInterface
{
    protected $app;
    protected $Order;

    public function __construct(Application $app = null)
    {
        $this->app = $app;
    }

    public function execute(OrderDetailCollection $OrderDetails)
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

        // 送料が存在しない場合は追加
        if (!$OrderDetails->hasProductByName('送料')) {
            $OrderDetail = new OrderDetail();
            $OrderDetail->setProductName("送料")
                ->setPrice($delivery_fee_total)
                ->setPriceIncTax($delivery_fee_total)
                ->setTaxRate(0)
                ->setQuantity(1);
            $this->Order->setDeliveryFeeTotal($delivery_fee_total);
            $OrderDetails->append($OrderDetail);
        }
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
