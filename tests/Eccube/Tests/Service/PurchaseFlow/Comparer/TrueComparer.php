<?php

namespace Eccube\Tests\Service\PurchaseFlow\Comparer;

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\Comparer\ItemComparer;

class TrueComparer implements ItemComparer
{
    public function compare(ItemInterface $Item1, ItemInterface $Item2)
    {
        return true;
    }
}
