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

    // map, filter, reduce も実装したい
}
