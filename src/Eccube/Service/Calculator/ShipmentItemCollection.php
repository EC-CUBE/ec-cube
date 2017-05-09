<?php

namespace Eccube\Service\Calculator;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\ShipmentItem;

class ShipmentItemCollection extends \ArrayIterator
{
    public function __construct($ShipmentItems, $flags = 0)
    {
        // $ShipmentItems が Collection だったら toArray(); する
        parent::__construct($ShipmentItems, $flags);
    }

    // 明細種別ごとに返すメソッド作る
    public function getProductClasses()
    {
        return $this->subCollection(OrderItemType::PRODUCT);
    }

    public function getDeliveryFees()
    {
        return $this->subCollection(OrderItemType::DELIVERY_FEE);
    }

    /**
     * 指定した受注明細区分だけの明細を取得.
     * @param int $orderItemTypeId 受注明細区分ID
     * @return ShipmentItemCollection
     */
    private function subCollection($orderItemTypeId)
    {
        return new self(array_filter($this->getArrayCopy(), function($ShipmentItem) use ($orderItemTypeId) {
            /* @var ShipmentItem $ShipmentItem */
            return $ShipmentItem->getOrderItemType() && $ShipmentItem->getOrderItemType()->getId() == $orderItemTypeId;
        }));
    }

    /**
     * 同名の明細が存在するかどうか.
     *
     * TODO 暫定対応. 本来は明細種別でチェックする.
     */
    public function hasProductByName($productName)
    {
        $ShipmentItems = array_filter($this->getArrayCopy(),
                                     function ($ShipmentItem) use ($productName) {
                                         /* @var ShipmentItem $ShipmentItem */
                                         return $ShipmentItem->getProductName() == $productName;
                                     });
        return !empty($ShipmentItems);
    }
    // map, filter, reduce も実装したい

    /**
     * 指定した受注明細区分の明細が存在するかどうか
     * @param OrderItemType $OrderItemType 受注区分
     * @return boolean
     */
    public function hasItemByOrderItemType($OrderItemType)
    {
        $filteredItems = array_filter($this->getArrayCopy(), function($ShipmentItem) use ($OrderItemType) {
            /* @var ShipmentItem $ShipmentItem */
            return $ShipmentItem->getOrderItemType() && $ShipmentItem->getOrderItemType()->getId() == $OrderItemType->getId();
        });
        return !empty($filteredItems);
    }
}
