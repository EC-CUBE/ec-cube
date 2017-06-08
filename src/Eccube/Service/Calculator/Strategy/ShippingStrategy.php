<?php
namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Order;
use Eccube\Entity\PurchaseInterface;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Service\Calculator\ShipmentItemCollection;

class ShippingStrategy implements CalculateStrategyInterface
{
    /* @var Application $app */
    protected $app;

    /* @var Order $Order */
    protected $Order;

    /** @var OrderItemTypeRepository */
    protected $OrderItemTypeRepository;

    public function execute(ShipmentItemCollection $ShipmentItems)
    {
        // 送料の受注明細区分
        $DeliveryFeeType = $this->app['eccube.repository.master.order_item_type']->find(OrderItemType::DELIVERY_FEE);
        // TODO
        $TaxInclude = $this->app['orm.em']->getRepository(TaxDisplayType::class)->find(TaxDisplayType::INCLUDED);
        $Taxion = $this->app['orm.em']->getRepository(TaxType::class)->find(TaxType::TAXATION);

        // 配送ごとに送料の明細を作成
        foreach ($this->Order->getShippings() as $Shipping) {
            /* @var Shipping $Shipping */
            $sio = new ShipmentItemCollection($Shipping->getShipmentItems()->toArray());
            if (!$sio->hasItemByOrderItemType($DeliveryFeeType)) {
                $ShipmentItem = new ShipmentItem();
                $ShipmentItem->setProductName("送料")
                    ->setPrice($Shipping->getShippingDeliveryFee())
                    ->setPriceIncTax($Shipping->getShippingDeliveryFee())
                    ->setTaxRate(8)
                    ->setQuantity(1)
                    ->setOrderItemType($DeliveryFeeType)
                    ->setShipping($Shipping)
                    ->setTaxDisplayType($TaxInclude)
                    ->setTaxType($Taxion);
                $ShipmentItems->add($ShipmentItem);
                $Shipping->addShipmentItem($ShipmentItem);
            }
        }
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
