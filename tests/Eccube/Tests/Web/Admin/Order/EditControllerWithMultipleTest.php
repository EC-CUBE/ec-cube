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

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\Shipping;
use Eccube\Entity\TaxRule;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\TaxRuleRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * 複数配送の受注に対する Admin\Order\EditController のテストケース.
 *
 * 複数配送時は OrderType::addShippingForm が Shipping のフォームを追加しないため,
 * 明細 (OrderItems) のみが編集対象となる.
 * OrderType::associateOrderAndShipping が既存明細の Shipping を移動させてしまうと,
 * 全商品が第1配送先へ集約される退行となるため, その分布が保持されることを検証する.
 */
final class EditControllerWithMultipleTest extends AbstractEditControllerTestCase
{
    protected ?Customer $Customer = null;

    protected ?Product $Product = null;

    protected ?OrderRepository $orderRepository = null;

    protected ?TaxRuleRepository $taxRuleRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Product = $this->createProduct();
        $this->orderRepository = $this->entityManager->getRepository(Order::class);
        $this->taxRuleRepository = $this->entityManager->getRepository(TaxRule::class);
    }

    /**
     * 複数配送の受注で編集画面が表示できること.
     */
    public function testRoutingAdminOrderEditWithMultiple(): void
    {
        $Order = $this->createMultiShippingOrder();
        $this->assertTrue($Order->isMultiple(), '複数配送の受注であること');

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_order_edit', ['id' => $Order->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * 複数配送の受注を明細を変更せずに保存しても, 明細と配送の紐付けが変わらないこと.
     *
     * OrderType::associateOrderAndShipping で既存明細の Shipping を上書きすると,
     * 全明細が第1配送先へ集約される退行が発生する.
     */
    public function testShippingAssociationIsPreservedOnEdit(): void
    {
        $Order = $this->createMultiShippingOrder();
        $orderId = $Order->getId();

        $expected = $this->getProductItemShippingMap($Order);
        $this->assertCount(2, array_unique($expected), '商品明細が2つの配送先に分かれていること');

        $formData = $this->createFormDataForMultipleEdit($Order);

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $orderId]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );

        $this->assertTrue(
            $this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $orderId])),
            '受注編集が保存されリダイレクトされること'
        );

        $this->entityManager->clear();
        /** @var Order $EditedOrder */
        $EditedOrder = $this->orderRepository->find($orderId);
        $actual = $this->getProductItemShippingMap($EditedOrder);

        $this->assertEquals($expected, $actual, '商品明細の shipping_id 分布が保存前後で変わらないこと');
    }

    /**
     * 複数配送の受注で明細を削除しても, 他の配送に紐づく明細が保持されること.
     */
    public function testOrderEditWithShippingItemDelete(): void
    {
        $Order = $this->createMultiShippingOrder();
        $orderId = $Order->getId();

        $formData = $this->createFormDataForMultipleEdit($Order);

        // 第2配送先に紐づく商品明細を1件削除する.
        $Items = $Order->getItems()->toArray();
        $SecondShipping = $this->getSecondShipping($Order);
        $deletedItemId = null;
        foreach ($Items as $index => $Item) {
            if ($Item->isProduct() && $Item->getShipping()->getId() === $SecondShipping->getId()) {
                $deletedItemId = $Item->getId();
                // インデックスを維持したまま削除する (再インデックスするとスロットがずれる).
                unset($formData['OrderItems'][$index]);
                break;
            }
        }
        $this->assertNotNull($deletedItemId, '削除対象の商品明細が存在すること');

        // 削除対象以外の商品明細の紐付けを期待値とする.
        $expected = $this->getProductItemShippingMap($Order);
        unset($expected[$deletedItemId]);

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_order_edit', ['id' => $orderId]),
            [
                'order' => $formData,
                'mode' => 'register',
            ]
        );

        $this->assertTrue(
            $this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $orderId])),
            '受注編集が保存されリダイレクトされること'
        );

        $this->entityManager->clear();
        /** @var Order $EditedOrder */
        $EditedOrder = $this->orderRepository->find($orderId);
        $actual = $this->getProductItemShippingMap($EditedOrder);

        $this->assertArrayNotHasKey($deletedItemId, $actual, '削除した明細が残っていないこと');
        $this->assertEquals($expected, $actual, '削除されなかった明細の shipping_id 分布が保持されること');
    }

    /**
     * 商品明細の id => shipping_id のマップを返す.
     *
     * @return array<int, int>
     */
    private function getProductItemShippingMap(Order $Order): array
    {
        $map = [];
        foreach ($Order->getOrderItems() as $OrderItem) {
            if (!$OrderItem->isProduct()) {
                continue;
            }
            $Shipping = $OrderItem->getShipping();
            $map[$OrderItem->getId()] = $Shipping ? $Shipping->getId() : null;
        }
        ksort($map);

        return $map;
    }

    /**
     * 複数配送の受注編集用フォームデータを作成する.
     *
     * 複数配送時は Shipping のフォームが追加されないため, Shipping を除外する.
     * OrderItems は, フォームへセットされる Order::getItems() (ソート済み) の順で生成する.
     *
     * @return array<string, mixed>
     */
    private function createFormDataForMultipleEdit(Order $Order): array
    {
        $formData = $this->createFormDataForEdit($Order);
        unset($formData['Shipping']);

        $Items = $Order->getItems()->toArray();
        $formData['OrderItems'] = $this->createOrderItemsFormDataEdit($Items);
        // createOrderItemsFormDataEdit は tax_type を出力しないため, 明細から補完する.
        // (未送信だと課税区分が失われ, 値引き明細の集計でエラーになる)
        foreach ($Items as $index => $Item) {
            $formData['OrderItems'][$index]['tax_type'] = $Item->getTaxType()->getId();
        }

        return $formData;
    }

    /**
     * 第1配送先以外の Shipping を返す.
     */
    private function getSecondShipping(Order $Order): Shipping
    {
        $Shippings = $Order->getShippings();
        $this->assertGreaterThan(1, $Shippings->count());

        return $Shippings->last();
    }

    /**
     * 配送を2件持つ受注を生成する.
     *
     * Generator::createOrder() は ProductClass を複数渡しても Shipping を1つしか作らないため,
     * 2件目の Shipping と, それに紐づく商品明細を手動で組み立てる.
     */
    private function createMultiShippingOrder(): Order
    {
        $Order = $this->createOrder($this->Customer);
        $Order->setOrderStatus($this->entityManager->find(OrderStatus::class, OrderStatus::NEW));

        /** @var Shipping $FirstShipping */
        $FirstShipping = $Order->getShippings()->first();

        $SecondShipping = new Shipping();
        $SecondShipping->copyProperties($this->Customer);
        $SecondShipping
            ->setOrder($Order)
            ->setName01('配送先')
            ->setName02('2')
            ->setKana01('ハイソウサキ')
            ->setKana02('ニ')
            ->setPref($FirstShipping->getPref())
            ->setDelivery($FirstShipping->getDelivery())
            ->setShippingDeliveryName($FirstShipping->getDelivery()->getName())
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime());
        $Order->addShipping($SecondShipping);
        $this->entityManager->persist($SecondShipping);

        $ProductClass = $this->Product->getProductClasses()[0];
        $OrderItem = new OrderItem();
        $OrderItem
            ->setShipping($SecondShipping)
            ->setOrder($Order)
            ->setProductClass($ProductClass)
            ->setProduct($this->Product)
            ->setProductName($this->Product->getName())
            ->setProductCode($ProductClass->getCode())
            ->setPrice((string) $ProductClass->getPrice02())
            ->setQuantity('1')
            ->setTaxType($this->entityManager->find(TaxType::class, TaxType::TAXATION))
            ->setTaxDisplayType($this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED))
            ->setOrderItemType($this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT))
        ;
        // 税率・丸め規則は PurchaseFlow の TaxProcessor 相当の設定を明細へ反映する.
        // (未設定だと受注編集画面の税率別集計でエラーになる)
        $TaxRule = $this->taxRuleRepository->getByRule($this->Product, $ProductClass);
        $OrderItem
            ->setTaxRate($TaxRule->getTaxRate())
            ->setTaxAdjust($TaxRule->getTaxAdjust())
            ->setRoundingType($TaxRule->getRoundingType())
        ;
        $Order->addOrderItem($OrderItem);
        $SecondShipping->addOrderItem($OrderItem);
        $this->entityManager->persist($OrderItem);

        $this->entityManager->flush();

        return $Order;
    }
}
