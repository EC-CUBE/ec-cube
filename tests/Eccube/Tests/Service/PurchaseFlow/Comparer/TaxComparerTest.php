<?php

namespace Eccube\Tests\Service\PurchaseFlow\Comparer;

use Eccube\Entity\CartItem;
use Eccube\Entity\ShipmentItem;
use Eccube\Service\PurchaseFlow\Comparer\TaxComparer;
use Eccube\Tests\EccubeTestCase;

class TaxComparerTest extends EccubeTestCase
{
    /**
     * @var ShipmentItem
     */
    protected $Item1;

    /**
     * @var ShipmentItem
     */
    protected $Item2;

    /**
     * @var TaxComparer
     */
    protected $comparer;

    public function setUp()
    {
        parent::setUp();

        $this->comparer = new TaxComparer();

        $this->Item1 = new ShipmentItem();
        $this->Item1
            ->setTaxRate(8)
            ->setTaxRule(1);

        $this->Item2 = new ShipmentItem();
        $this->Item2
            ->setTaxRate(8)
            ->setTaxRule(1);
    }

    public function testCompare()
    {
        $this->assertTrue($this->comparer->compare($this->Item1, $this->Item2));
    }

    public function testCompare_differentTaxRate()
    {
        $this->Item1->setTaxRate(10);
        $this->assertFalse($this->comparer->compare($this->Item1, $this->Item2));
    }

    public function testCompare_differentTaxRule()
    {
        $this->Item1->setTaxRate(2);
        $this->assertFalse($this->comparer->compare($this->Item1, $this->Item2));
    }

    public function testCompare_notShipmentItem()
    {
        $CartItem = new CartItem();
        $this->assertTrue($this->comparer->compare($CartItem, $CartItem));
    }
}
