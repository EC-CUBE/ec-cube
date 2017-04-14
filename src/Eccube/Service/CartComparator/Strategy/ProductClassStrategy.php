<?php

namespace Eccube\Service\CartComparator\Strategy;

class ProductClassStrategy implements CartComparatorStrategyInterface
{
    /**
     * @inheritdoc
     */
    public function compare($CartItem1, $CartItem2)
    {
        return
            $CartItem1->getClassId() == $CartItem2->getClassId() &&
            $CartItem1->getClassName() == $CartItem2->getClassName();
    }
}
