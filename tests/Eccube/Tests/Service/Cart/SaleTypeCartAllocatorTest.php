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
use Eccube\Service\Cart\SaleTypeCartAllocator;
use Eccube\Tests\EccubeTestCase;

class SaleTypeCartAllocatorTest extends EccubeTestCase
{
    /**
     * @var SaleTypeCartAllocator
     */
    private $allocator;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->allocator = new SaleTypeCartAllocator();
    }

    public function testAllocate()
    {
        $Product = $this->createProduct();
        /* @var \Eccube\Entity\ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];
        $CartItem = new CartItem();
        $CartItem->setProductClass($ProductClass);

        $expected = (string) $ProductClass->getSaleType()->getId();
        $actual = $this->allocator->allocate($CartItem);
        self::assertEquals($expected, $actual);
    }
}
