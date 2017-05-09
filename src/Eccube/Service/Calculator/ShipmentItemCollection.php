<?php

namespace Eccube\Service\Calculator;

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
        return new self(array_filter(
            $this->getArrayCopy(),
            function ($ShipmentItem) {
                /* @var ShipmentItem $ShipmentItem */
                if ($ShipmentItem->getProductClass()) {
                    return true;
                }
                return false;
            }
        ));
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
}
