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

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductStock;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Service\PurchaseFlow\Processor\StockDiffProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseException;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class StockDiffProcessorTest extends EccubeTestCase
{
    private ?StockDiffProcessor $processor = null;

    private ?OrderItemTypeRepository $OrderItemTypeRepository = null;

    private ?OrderStatusRepository $OrderStatusRepository = null;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = static::getContainer()->get(StockDiffProcessor::class);
        $this->OrderStatusRepository = $this->entityManager->getRepository(OrderStatus::class);
        $this->OrderItemTypeRepository = $this->entityManager->getRepository(OrderItemType::class);
    }

    /**
     * @param $stock int 在庫数
     * @param $beforeQuantity int 編集前の商品の数量
     * @param $afterQuantity int 編集後の商品の数量
     * @param $isError bool バリデーションエラーがある場合はtrue
     * @param $beforeOrderStatus int 編集前の受注ステータス
     * @param $afterOrderStatus int 編集後の受注ステータス
     */
    #[DataProvider(methodName: 'validateProvider')]
    public function testValidate($stock, $beforeQuantity, $afterQuantity, $isError, $beforeOrderStatus, $afterOrderStatus)
    {
        $Customer = new Customer();

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()->first();

        $OrderItemType = $this->OrderItemTypeRepository->find(OrderItemType::PRODUCT);

        // 編集前の受注
        $BeforeOrder = new Order();
        $BeforeOrderStatus = $this->OrderStatusRepository->find($beforeOrderStatus);
        $BeforeOrder->setOrderStatus($BeforeOrderStatus);
        $BeforeOrder->setCustomer($Customer);
        $ProductClass->setStock((string) $stock);
        $beforeOrderItem = $this->newOrderItem($ProductClass, 1000, $beforeQuantity);
        $beforeOrderItem->setOrderItemType($OrderItemType);
        $BeforeOrder->addOrderItem($beforeOrderItem);

        // 編集後の受注
        $AfterOrder = new Order();
        $AfterOrderStatus = $this->OrderStatusRepository->find($afterOrderStatus);
        $AfterOrder->setOrderStatus($AfterOrderStatus);
        $AfterOrder->setCustomer($Customer);
        $ProductClass->setStock((string) $stock);
        $afterOrderItem = $this->newOrderItem($ProductClass, 1000, $afterQuantity);
        $afterOrderItem->setOrderItemType($OrderItemType);
        $AfterOrder->addOrderItem($afterOrderItem);

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->setFlowType(PurchaseContext::ORDER_FLOW);
        $purchaseFlow->addItemHolderValidator($this->processor);
        $context = new PurchaseContext($BeforeOrder, $Customer);
        $result = $purchaseFlow->validate($AfterOrder, $context);

        $this->expected = $isError;
        $this->actual = $result->hasError();
        $this->verify('バリデーションエラーの有無を判定');

        if ($isError) {
            $productName = $ProductClass->formattedProductName($afterOrderStatus);
            $this->expected = '「'.$productName.'」の在庫が足りません。';
            $this->actual = $result->getErrors()[0]->getMessage();
            $this->verify('バリデーションエラーのメッセージを比較。');
        }
    }

    public static function validateProvider(): \Iterator
    {
        yield [10, 2, 12, false, OrderStatus::NEW, OrderStatus::NEW];
        yield [1, 3, 2, false, OrderStatus::NEW, OrderStatus::PAID];
        yield [1, 1, 2, false, OrderStatus::NEW, OrderStatus::IN_PROGRESS];
        yield [0, 3, 6, false, OrderStatus::NEW, OrderStatus::CANCEL];
        yield [2, 4, 6, false, OrderStatus::NEW, OrderStatus::DELIVERED];
        yield [3, 1, 1, false, OrderStatus::PAID, OrderStatus::PAID];
        yield [1, 1, 1, false, OrderStatus::PAID, OrderStatus::IN_PROGRESS];
        yield [1, 1, 10, false, OrderStatus::PAID, OrderStatus::CANCEL];
        yield [1, 12, 13, false, OrderStatus::PAID, OrderStatus::DELIVERED];
        yield [1, 1, 1, false, OrderStatus::IN_PROGRESS, OrderStatus::IN_PROGRESS];
        yield [1, 1, 1, false, OrderStatus::IN_PROGRESS, OrderStatus::CANCEL];
        yield [1, 1, 1, false, OrderStatus::IN_PROGRESS, OrderStatus::DELIVERED];
        yield [1, 1, 1, false, OrderStatus::CANCEL, OrderStatus::IN_PROGRESS];
        yield [1, 1, 2, false, OrderStatus::CANCEL, OrderStatus::CANCEL];
        yield [1, 1, 2, false, OrderStatus::DELIVERED, OrderStatus::DELIVERED];
        yield [1, 1, 2, false, OrderStatus::DELIVERED, OrderStatus::RETURNED];
        yield [3, 3, 6, false, OrderStatus::RETURNED, OrderStatus::DELIVERED];
        yield [3, 3, 6, false, OrderStatus::RETURNED, OrderStatus::RETURNED];
        yield [10, 2, 13, true, OrderStatus::NEW, OrderStatus::NEW];
        yield [1, 3, 5, true, OrderStatus::NEW, OrderStatus::PAID];
        yield [1, 1, 3, true, OrderStatus::NEW, OrderStatus::IN_PROGRESS];
        yield [0, 3, -1, true, OrderStatus::NEW, OrderStatus::CANCEL];
        yield [2, 4, 7, true, OrderStatus::NEW, OrderStatus::DELIVERED];
        yield [3, 1, 7, true, OrderStatus::PAID, OrderStatus::PAID];
        yield [1, 1, 3, true, OrderStatus::PAID, OrderStatus::IN_PROGRESS];
        yield [1, 1, -2, true, OrderStatus::PAID, OrderStatus::CANCEL];
        yield [1, 12, 14, true, OrderStatus::PAID, OrderStatus::DELIVERED];
        yield [1, 1, 3, true, OrderStatus::IN_PROGRESS, OrderStatus::IN_PROGRESS];
        yield [1, 1, -2, true, OrderStatus::IN_PROGRESS, OrderStatus::CANCEL];
        yield [1, 1, 3, true, OrderStatus::IN_PROGRESS, OrderStatus::DELIVERED];
        yield [1, 1, 3, true, OrderStatus::CANCEL, OrderStatus::IN_PROGRESS];
        yield [0, 1, 2, true, OrderStatus::CANCEL, OrderStatus::CANCEL];
        yield [1, 1, 3, true, OrderStatus::DELIVERED, OrderStatus::DELIVERED];
        yield [1, 1, 3, true, OrderStatus::DELIVERED, OrderStatus::RETURNED];
        yield [3, 3, 7, true, OrderStatus::RETURNED, OrderStatus::DELIVERED];
        yield [3, 3, 7, true, OrderStatus::RETURNED, OrderStatus::RETURNED];
    }

    /**
     * @param $beforeStock int 編集前の在庫数
     * @param $afterStock int 編集後の在庫数
     * @param $beforeQuantity int 編集前の商品の数量
     * @param $afterQuantity int 編集後の商品の数量
     * @param $beforeOrderStatus int 編集前の受注ステータス
     * @param $afterOrderStatus int 編集後の受注ステータス
     *
     * @throws PurchaseException
     */
    #[DataProvider(methodName: 'prepareProvider')]
    public function testPrepare($beforeStock, $afterStock, $beforeQuantity, $afterQuantity, $beforeOrderStatus, $afterOrderStatus)
    {
        $Customer = new Customer();

        /* @var ProductClass $ProductClass */
        $ProductClass = $this->createProduct('テスト', 1)->getProductClasses()->first();

        $OrderItemType = $this->OrderItemTypeRepository->find(OrderItemType::PRODUCT);

        // 編集前の受注
        $BeforeOrder = new Order();
        $BeforeOrderStatus = $this->OrderStatusRepository->find($beforeOrderStatus);
        $BeforeOrder->setOrderStatus($BeforeOrderStatus);
        $BeforeOrder->setCustomer($Customer);
        $ProductClass->setStock((string) $beforeStock);
        $beforeOrderItem = $this->newOrderItem($ProductClass, 1000, $beforeQuantity);
        $beforeOrderItem->setOrderItemType($OrderItemType);
        $BeforeOrder->addOrderItem($beforeOrderItem);

        // 編集後の受注
        $AfterOrder = new Order();
        $AfterOrderStatus = $this->OrderStatusRepository->find($afterOrderStatus);
        $AfterOrder->setOrderStatus($AfterOrderStatus);
        $AfterOrder->setCustomer($Customer);
        $ProductClass->setStock((string) $beforeStock);
        $afterOrderItem = $this->newOrderItem($ProductClass, 1000, $afterQuantity);
        $afterOrderItem->setOrderItemType($OrderItemType);
        $AfterOrder->addOrderItem($afterOrderItem);

        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->setFlowType(PurchaseContext::ORDER_FLOW);
        $purchaseFlow->addPurchaseProcessor($this->processor);
        $context = new PurchaseContext($BeforeOrder, $Customer);
        $purchaseFlow->prepare($AfterOrder, $context);
        $this->expected = $afterStock === null ? null : (string) $afterStock;
        $this->actual = $ProductClass->getStock();
        $this->verify('dtb_product_class の在庫数(stock)が正しくセットされていない。');

        $ProductStock = $ProductClass->getProductStock();
        $this->expected = $afterStock === null ? null : (string) $afterStock;
        $this->assertInstanceOf(ProductStock::class, $ProductStock);
        $this->actual = $ProductStock->getStock();
        $this->verify('dtb_product_stock の在庫数(stock)が正しくセットされていない。');
    }

    public static function prepareProvider(): \Iterator
    {
        yield [10, 0, 2, 12, OrderStatus::NEW, OrderStatus::NEW];
        yield [1, 2, 3, 2, OrderStatus::NEW, OrderStatus::PAID];
        yield [1, 0, 1, 2, OrderStatus::NEW, OrderStatus::IN_PROGRESS];
        yield [0, 1, 3, 2, OrderStatus::NEW, OrderStatus::CANCEL];
        yield [2, 0, 4, 6, OrderStatus::NEW, OrderStatus::DELIVERED];
        yield [3, 3, 1, 1, OrderStatus::PAID, OrderStatus::PAID];
        yield [1, 1, 1, 1, OrderStatus::PAID, OrderStatus::IN_PROGRESS];
        yield [1, 0, 1, 2, OrderStatus::PAID, OrderStatus::CANCEL];
        yield [1, 0, 12, 13, OrderStatus::PAID, OrderStatus::DELIVERED];
        yield [1, 2, 1, 0, OrderStatus::IN_PROGRESS, OrderStatus::IN_PROGRESS];
        yield [1, 1, 1, 1, OrderStatus::IN_PROGRESS, OrderStatus::CANCEL];
        yield [1, 2, 1, 0, OrderStatus::IN_PROGRESS, OrderStatus::DELIVERED];
        yield [1, 2, 1, 0, OrderStatus::CANCEL, OrderStatus::IN_PROGRESS];
        yield [1, 0, 1, 2, OrderStatus::CANCEL, OrderStatus::CANCEL];
        yield [1, 0, 1, 2, OrderStatus::DELIVERED, OrderStatus::DELIVERED];
        yield [1, 0, 1, 2, OrderStatus::DELIVERED, OrderStatus::RETURNED];
        yield [3, 0, 3, 6, OrderStatus::RETURNED, OrderStatus::DELIVERED];
        yield [3, 0, 3, 6, OrderStatus::RETURNED, OrderStatus::RETURNED];
    }

    private function newOrderItem($ProductClass, $price, $quantity)
    {
        $OrderItem = new OrderItem();
        $OrderItem->setProductClass($ProductClass);
        $OrderItem->setPrice((string) $price);
        $OrderItem->setQuantity($quantity);

        return $OrderItem;
    }
}
