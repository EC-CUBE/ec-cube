<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Service\PurchaseFlow\Processor\OrderMemoPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

final class OrderMemoPreprocessorTest extends EccubeTestCase
{
    private ?OrderMemoPreprocessor $processor = null;

    private ?Order $Order = null;

    private ?Product $Product = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = new OrderMemoPreprocessor();
        $Customer = $this->createCustomer();
        $this->Product = $this->createProduct('test', 1);
        $this->Order = $this->createOrderWithProductClasses($Customer, $this->Product->getProductClasses()->toArray());
        $this->entityManager->flush();
    }

    public function testCopyMemoToProductItem(): void
    {
        $this->Product->setOrderMemo('梱包時は割れ物注意');

        $this->processor->process($this->Order, new PurchaseContext());

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $this->assertSame('梱包時は割れ物注意', $OrderItem->getOrderMemo());
        }
    }

    public function testCopyNullMemo(): void
    {
        $this->Product->setOrderMemo(null);

        $this->processor->process($this->Order, new PurchaseContext());

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $this->assertNull($OrderItem->getOrderMemo());
        }
    }

    public function testSameMemoIsNotAppendedTwice(): void
    {
        $this->Product->setOrderMemo('梱包時は割れ物注意');

        // 既に同一文言が入っている明細に対しては追記しない(冪等)
        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $OrderItem->setOrderMemo('梱包時は割れ物注意');
        }

        $this->processor->process($this->Order, new PurchaseContext());

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $this->assertSame('梱包時は割れ物注意', $OrderItem->getOrderMemo());
        }
    }

    public function testDifferentMemoIsAppended(): void
    {
        $this->Product->setOrderMemo('商品メモ');

        // 既存メモがある明細には、改行区切りで追記し既存メモは残す
        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $OrderItem->setOrderMemo('既存メモ');
        }

        $this->processor->process($this->Order, new PurchaseContext());

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $this->assertSame("既存メモ\n商品メモ", $OrderItem->getOrderMemo());
        }
    }

    public function testPartialMatchIsStillAppended(): void
    {
        // 商品メモが既存メモ行の部分文字列に偶然含まれていても, 行として一致しなければ追記する.
        $this->Product->setOrderMemo('注意');

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $OrderItem->setOrderMemo('取扱注意事項あり');
        }

        $this->processor->process($this->Order, new PurchaseContext());

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $this->assertSame("取扱注意事項あり\n注意", $OrderItem->getOrderMemo());
        }
    }

    public function testExactLineMatchInMultilineMemoIsNotAppended(): void
    {
        // 複数行メモのうち1行が商品メモと完全一致する場合は追記しない(冪等).
        $this->Product->setOrderMemo('商品メモ');

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $OrderItem->setOrderMemo("他メモ\n商品メモ");
        }

        $this->processor->process($this->Order, new PurchaseContext());

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $this->assertSame("他メモ\n商品メモ", $OrderItem->getOrderMemo());
        }
    }

    public function testNonProductItemIsNotCopied(): void
    {
        $this->Product->setOrderMemo('商品メモ');

        // 送料明細(商品メモを持つ Product を紐づけても、商品明細でなければコピーされない)
        $DeliveryFeeType = $this->entityManager->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $FeeItem = new OrderItem();
        $this->assertInstanceOf(OrderItemType::class, $DeliveryFeeType);
        $FeeItem->setOrderItemType($DeliveryFeeType)
            ->setProduct($this->Product)
            ->setProductName('送料')
            ->setPrice('500')
            ->setQuantity('1');
        $this->Order->addOrderItem($FeeItem);

        $this->processor->process($this->Order, new PurchaseContext());

        $this->assertFalse($FeeItem->isProduct());
        $this->assertNull($FeeItem->getOrderMemo());
    }
}
