<?php
namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Order;
use Eccube\Entity\PurchaseInterface;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Service\Calculator\OrderItemCollection;

/**
 * 送料の合計を集計して Order にセットする.
 */
class CalculateDeliveryFeeStrategy implements CalculateStrategyInterface
{
    /* @var Application $app */
    protected $app;

    /* @var Order $Order */
    protected $Order;

    /** @var OrderItemTypeRepository */
    protected $OrderItemTypeRepository;

    public function execute(OrderItemCollection $OrderItems)
    {
        $delivery_fee = $OrderItems->getDeliveryFees()->reduce(
            function($total, $OrderItem) {
                return $total + $OrderItem->getPrice() * $OrderItem->getQuantity();
            }, 0
        );
        $this->Order->setDeliveryFeeTotal($delivery_fee);
    }

    public function setApplication(Application $app)
    {
        $this->app = $app;
        return $this;
    }

    public function setOrder(PurchaseInterface $Order)
    {
        $this->Order = $Order;
        return $this;
    }

    public function getTargetTypes()
    {
        return [Order::class];
    }
}
