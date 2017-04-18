<?php

namespace Eccube\Tests\Service\CartComparator\Strategy;

use Eccube\Service\CartComparator\Strategy\CartComparatorStrategyInterface;

class FalseStrategy implements CartComparatorStrategyInterface
{
    /**
     * @inheritdoc
     */
    public function compare($CartItem1, $CartItem2)
    {
        return false;
    }
}
