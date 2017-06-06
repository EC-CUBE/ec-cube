<?php
namespace Eccube\Service\Calculator\Strategy;

use Eccube\Application;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Order;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Service\Calculator\ShipmentItemCollection;

class ChargeStrategy implements CalculateStrategyInterface
{
    /* @var Application $app */
    protected $app;

    /* @var Order $Order */
    protected $Order;

    /** @var OrderItemTypeRepository */
    protected $OrderItemTypeRepository;

    public function execute(ShipmentItemCollection $ShipmentItems)
    {
        // 手数料の受注明細区分
        $ChargeType = $this->app['eccube.repository.master.order_item_type']->find(OrderItemType::CHARGE);
        // TODO
        $TaxInclude = $this->app['orm.em']->getRepository(TaxDisplayType::class)->find(TaxDisplayType::INCLUDED);
        $Taxion = $this->app['orm.em']->getRepository(TaxType::class)->find(TaxType::TAXATION);

        if (!$ShipmentItems->hasItemByOrderItemType($ChargeType)) {
            $Payment = $this->Order->getPayment();
            if (is_object($Payment) && $Payment->getCharge() > 0) {
                $ShipmentItem = new ShipmentItem();
                $ShipmentItem->setProductName("手数料")
                    ->setPrice($Payment->getCharge())
                    ->setPriceIncTax($Payment->getCharge())
                    ->setTaxRate(8)
                    ->setQuantity(1)
                    ->setOrderItemType($ChargeType)
                    ->setTaxDisplayType($TaxInclude)
                    ->setTaxType($Taxion);
                $ShipmentItems->add($ShipmentItem);
            }
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
