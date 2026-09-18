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

/**
 * 受注管理用メモのコピー処理(スナップショット・上書き)の単体テスト。
 *
 * 注文確定時点の商品メモを商品明細へ「常に上書き」でコピーする(Issue #6821 §3.2)。
 * 追記や冪等判定は行わない。「確定後に商品側メモを変更しても既存受注明細のメモは
 * 変わらない」(§8)という要件は、本 Preprocessor を order フローに登録せず
 * shopping フローでのみ実行することで担保する(配線は OrderMemoFlowTest が検証)。
 */
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

    public function testExistingMemoIsOverwritten(): void
    {
        // 明細に既存メモがあっても、確定時点の商品メモで常に上書きする(追記しない)。
        $this->Product->setOrderMemo('商品メモ');

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $OrderItem->setOrderMemo('既存メモ');
        }

        $this->processor->process($this->Order, new PurchaseContext());

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $this->assertSame('商品メモ', $OrderItem->getOrderMemo());
        }
    }

    public function testNullProductMemoOverwritesExistingMemoToNull(): void
    {
        // 商品メモが未設定(null)なら、明細の既存メモも null で上書きされる。
        $this->Product->setOrderMemo(null);

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $OrderItem->setOrderMemo('既存メモ');
        }

        $this->processor->process($this->Order, new PurchaseContext());

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $this->assertNull($OrderItem->getOrderMemo());
        }
    }

    public function testChangedProductMemoIsReflectedOnReprocess(): void
    {
        // 同一フロー内で再処理した場合、常に最新の商品メモで上書きされる(スナップショット)。
        $this->Product->setOrderMemo('旧メモ');
        $this->processor->process($this->Order, new PurchaseContext());

        $this->Product->setOrderMemo('新メモ');
        $this->processor->process($this->Order, new PurchaseContext());

        foreach ($this->Order->getProductOrderItems() as $OrderItem) {
            $this->assertSame('新メモ', $OrderItem->getOrderMemo());
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
