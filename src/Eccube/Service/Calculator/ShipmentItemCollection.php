<?php

namespace Eccube\Service\Calculator;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\ShipmentItem;

class ShipmentItemCollection extends \Doctrine\Common\Collections\ArrayCollection
{
    public function __construct($ShipmentItems)
    {
        // $ShipmentItems が Collection だったら toArray(); する
        parent::__construct($ShipmentItems);
    }

    public function reduce(\Closure $func, $initial = null)
    {
        return array_reduce($this->toArray(), $func, $initial);
    }

    // 明細種別ごとに返すメソッド作る
    public function getProductClasses()
    {
        return $this->filter(
            function($ShipmentItem) {
                return $ShipmentItem->isProduct();
            });
    }

    public function getDeliveryFees()
    {
        return $this->filter(
            function($ShipmentItem) {
                return $ShipmentItem->isDeliveryFee();
            });
    }

    public function getCharges()
    {
        return $this->filter(
            function($ShipmentItem) {
                return $ShipmentItem->isCharge();
            });
    }

    public function getDiscounts()
    {
        return $this->filter(
            function($ShipmentItem) {
                return $ShipmentItem->isDiscount();
            });
    }

    /**
     * 同名の明細が存在するかどうか.
     *
     * TODO 暫定対応. 本来は明細種別でチェックする.
     */
    public function hasProductByName($productName)
    {
        $ShipmentItems = $this->filter(
            function ($ShipmentItem) use ($productName) {
                /* @var ShipmentItem $ShipmentItem */
                return $ShipmentItem->getProductName() == $productName;
            });
        return !$ShipmentItems->isEmpty();
    }
    // map, filter, reduce も実装したい

    /**
     * 指定した受注明細区分の明細が存在するかどうか
     * @param OrderItemType $OrderItemType 受注区分
     * @return boolean
     */
    public function hasItemByOrderItemType($OrderItemType)
    {
        $filteredItems = $this->filter(function($ShipmentItem) use ($OrderItemType) {
            /* @var ShipmentItem $ShipmentItem */
            return $ShipmentItem->getOrderItemType() && $ShipmentItem->getOrderItemType()->getId() == $OrderItemType->getId();
        });
        return !$filteredItems->isEmpty();
    }
}
