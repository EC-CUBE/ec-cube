<?php

namespace Eccube\Service\Calculator;

use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;

class OrderDetailCollection extends \ArrayIterator
{
    public function __construct($OrderDetails, $flags = 0)
    {
        // $OrderDetails が Collection だったら toArray(); する
        parent::__construct($OrderDetails, $flags);
    }

    // 明細種別ごとに返すメソッド作る
    public function getProductClasses()
    {
        return new self(array_filter(
            $this->getArrayCopy(),
            function ($OrderDetail) {
                if ($OrderDetail->getProductClass()) {
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
        $OrderDetails = array_filter($this->getArrayCopy(),
                                     function ($OrderDetail) use ($productName) {
                                         return $OrderDetail->getProductName() == $productName;
                                     });
        return !empty($OrderDetails);
    }
    // map, filter, reduce も実装したい
}
