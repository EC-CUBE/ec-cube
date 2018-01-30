<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
