<?php

namespace Eccube\Service\CartComparator\Strategy;

interface CartComparatorStrategyInterface
{
    /**
     * @param \Eccube\Entity\CartItem $CartItem1
     * @param \Eccube\Entity\CartItem $CartItem2
     * @return \Eccube\Entity\CartItem
     */
    public function compare($CartItem1, $CartItem2);
}
