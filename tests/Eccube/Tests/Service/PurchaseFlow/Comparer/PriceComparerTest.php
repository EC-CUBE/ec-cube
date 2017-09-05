<?php

namespace Eccube\Tests\Service\PurchaseFlow\Comparer;

use Eccube\Entity\CartItem;
use Eccube\Service\PurchaseFlow\Comparer\PriceComparer;
use Eccube\Tests\EccubeTestCase;

class PriceComparerTest extends EccubeTestCase
{
    /**
     * @var CartItem
     */
    protected $Item1;

    /**
     * @var CartItem
     */
    protected $Item2;

    /**
     * @var PriceComparer
     */
    protected $comparer;

    public function setUp()
    {
        parent::setUp();

        $this->comparer = new PriceComparer();

        $this->Item1 = new CartItem();
        $this->Item1->setPrice(100);

        $this->Item2 = new CartItem();
        $this->Item2->setPrice(100);
    }

    public function testCompare()
    {
        $this->assertTrue($this->comparer->compare($this->Item1, $this->Item2));
    }

    public function testCompare_differentPrice()
    {
        $this->Item2->setPrice($this->Item2->getPrice() + 10);

        $this->assertFalse($this->comparer->compare($this->Item1, $this->Item2));
    }
}
