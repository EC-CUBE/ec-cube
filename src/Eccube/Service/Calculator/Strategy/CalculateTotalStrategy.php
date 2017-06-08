<?php
namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Cart;
use Eccube\Entity\Order;
use Eccube\Entity\PurchaseInterface;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Service\Calculator\ShipmentItemCollection;

/**
 * 明細の合計を集計して Order にセットする.
 */
class CalculateTotalStrategy implements CalculateStrategyInterface
{
    /* @var Application $app */
    protected $app;

    /* @var Order $Order */
    protected $Order;

    /** @var OrderItemTypeRepository */
    protected $OrderItemTypeRepository;

    public function execute(ShipmentItemCollection $ShipmentItems)
    {
        $total = $ShipmentItems->reduce(
            function($total, $ShipmentItem) {
                return $total + $ShipmentItem->getPrice() * $ShipmentItem->getQuantity();
            }, 0
        );
        $this->Order->setTotal($total);
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
        return [Order::class, Cart::class];
    }
}
