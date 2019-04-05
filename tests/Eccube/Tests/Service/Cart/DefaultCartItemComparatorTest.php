<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Service\Cart;

use Eccube\Entity\CartItem;
use Eccube\Entity\ProductClass;
use Eccube\Service\Cart\ProductClassComparator;
use Eccube\Tests\EccubeTestCase;

class DefaultCartItemComparatorTest extends EccubeTestCase
{
    /**
     * @var ProductClassComparator
     */
    private $comparator;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->comparator = new ProductClassComparator();
    }

    public function testCompareSameCartItem()
    {
        $CartItem = $this->cartItem($this->createProduct()->getProductClasses()[0]);
        self::assertTrue($this->comparator->compare($CartItem, $CartItem));
    }

    public function testCompareDifferentCartItemWithSameProductClass()
    {
        $ProductClass = $this->createProduct()->getProductClasses()[0];
        $CartItem1 = $this->cartItem($ProductClass);
        $CartItem2 = $this->cartItem($ProductClass);
        self::assertTrue($this->comparator->compare($CartItem1, $CartItem2));
        self::assertTrue($this->comparator->compare($CartItem2, $CartItem1));
    }

    public function testCompareDifferentCartItemWithDifferentProductClass()
    {
        $Product = $this->createProduct('test', 2);
        $CartItem1 = $this->cartItem($Product->getProductClasses()[0]);
        $CartItem2 = $this->cartItem($Product->getProductClasses()[1]);
        self::assertFalse($this->comparator->compare($CartItem1, $CartItem2));
        self::assertFalse($this->comparator->compare($CartItem2, $CartItem1));
    }

    private function cartItem(ProductClass $ProductClass)
    {
        $result = new CartItem();
        $result->setProductClass($ProductClass);

        return $result;
    }
}
