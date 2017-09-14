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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\Processor\AddItemProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Service\PurchaseFlow\Comparer\FalseComparer;
use Eccube\Tests\Service\PurchaseFlow\Comparer\TrueComparer;

class AddItemProcessorTest extends EccubeTestCase
{
    /**
     * @var PurchaseContext
     */
    protected $purchaseContext;

    /**
     * @var Cart
     */
    protected $Cart;

    /**
     * @var ItemInterface
     */
    protected $Item1;

    /**
     * @var ItemInterface
     */
    protected $Item2;

    /**
     * @var TrueComparer
     */
    protected $trueComparer;

    /**
     * @var FalseComparer
     */
    protected $falseComparer;

    public function setUp()
    {
        parent::setUp();

        $this->Cart = new Cart();
        $this->purchaseContext = new PurchaseContext($this->Cart);
        $this->Item1 = $this->createCartItem(2);
        $this->Item2 = $this->createCartItem(3);
        $this->trueComparer = new TrueComparer();
        $this->falseComparer = new FalseComparer();
    }

    public function testProcess_noItems()
    {
        $quantity = $this->Item1->getQuantity();

        $processor = new AddItemProcessor($this->trueComparer);
        $result = $processor->process($this->Item1, $this->purchaseContext);

        $Items = $this->Cart->getCartItems();

        $this->assertEquals(1, count($Items));
        $this->assertEquals($quantity, $Items[0]->getQuantity());
        $this->assertTrue($result->isSuccess());
    }

    public function testProcess_alreadyExists()
    {
        $quantity1 = $this->Item1->getQuantity();
        $quantity2 = $this->Item2->getQuantity();
        $this->Cart->addItem($this->Item1);

        $processor = new AddItemProcessor($this->trueComparer);
        $result = $processor->process($this->Item2, $this->purchaseContext);

        $Items = $this->Cart->getCartItems();

        $this->assertEquals(1, count($Items));
        $this->assertEquals($quantity1 + $quantity2, $Items[0]->getQuantity());
        $this->assertTrue($result->isWarning());
    }

    public function testProcess_notExists()
    {
        $quantity1 = $this->Item1->getQuantity();
        $quantity2 = $this->Item2->getQuantity();
        $this->Cart->addItem($this->Item1);

        $processor = new AddItemProcessor($this->falseComparer);
        $result = $processor->process($this->Item2, $this->purchaseContext);

        $Items = $this->Cart->getCartItems();

        $this->assertEquals(2, count($Items));
        $this->assertEquals($quantity1, $Items[0]->getQuantity());
        $this->assertEquals($quantity2, $Items[1]->getQuantity());
        $this->assertTrue($result->isSuccess());
    }

    protected function createCartItem($quantity = 1)
    {
        $Product = $this->createProduct();

        $CartItem = new CartItem();
        $CartItem
            ->setQuantity($quantity)
            ->setProductClass($Product->getProductClasses()->first())

        ;
        return $CartItem;
    }
}
