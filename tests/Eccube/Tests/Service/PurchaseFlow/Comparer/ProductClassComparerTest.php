<?php

namespace Eccube\Tests\Service\PurchaseFlow\Comparer;

use Eccube\Entity\CartItem;
use Eccube\Entity\ProductClass;
use Eccube\Service\PurchaseFlow\Comparer\ProductClassComparer;
use Eccube\Tests\EccubeTestCase;

class ProductClassComparerTest extends EccubeTestCase
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
     * @var ProductClass
     */
    protected $ProductClass1;

    /**
     * @var ProductClass
     */
    protected $ProductClass2;

    /**
     * @var ProductClassComparer
     */
    protected $comparer;

    public function setUp()
    {
        parent::setUp();

        $this->comparer = new ProductClassComparer();

        $Product = $this->createProduct();
        $this->ProductClass1 = $Product->getProductClasses()->get(0);
        $this->ProductClass2 = $Product->getProductClasses()->get(1);

        $this->Item1 = new CartItem();
        $this->Item1->setObject($this->ProductClass1);

        $this->Item2 = new CartItem();
        $this->Item2->setObject($this->ProductClass1);
    }

    public function testCompare()
    {
        $this->assertTrue($this->comparer->compare($this->Item1, $this->Item2));
    }

    public function testCompare_differentProductClass()
    {
        $this->Item2->setObject($this->ProductClass2);

        $this->assertFalse($this->comparer->compare($this->Item1, $this->Item2));
    }
}
