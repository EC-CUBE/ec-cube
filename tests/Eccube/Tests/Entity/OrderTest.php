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

namespace Eccube\Tests\Entity;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Shipping;
use Eccube\Entity\TaxRule;
use Eccube\Service\TaxRuleService;
use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Fixture\Generator;
use PHPUnit\Framework\Attributes\Group;

/**
 * AbstractEntity test cases.
 *
 * @author Kentaro Ohkouchi
 */
final class OrderTest extends EccubeTestCase
{
    protected ?Customer $Customer = null;

    protected ?Order $Order = null;

    protected ?TaxRule $TaxRule = null;

    protected ?string $rate = null;

    protected ?TaxRuleService $taxRuleService = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $this->TaxRule = $this->entityManager->getRepository(TaxRule::class)->getByRule();
        $this->rate = $this->TaxRule->getTaxRate();
        $this->taxRuleService = static::getContainer()->get(TaxRuleService::class);
    }

    public function testConstructor()
    {
        $OrderStatus = $this->entityManager->getRepository(OrderStatus::class)->find(OrderStatus::PROCESSING);
        $Order = new Order($OrderStatus);

        $this->expected = '0';

        $this->actual = $Order->getDiscount();
        $this->verify();

        $this->actual = $Order->getSubTotal();
        $this->verify();

        $this->actual = $Order->getTotal();
        $this->verify();

        $this->actual = $Order->getPaymentTotal();
        $this->verify();

        $this->actual = $Order->getCharge();
        $this->verify();

        $this->actual = $Order->getTax();
        $this->verify();

        $this->actual = $Order->getDeliveryFeeTotal();
        $this->verify();

        $this->assertSame($OrderStatus, $Order->getOrderStatus());
    }

    public function testConstructor2()
    {
        $Order = new Order();

        $this->expected = '0';

        $this->actual = $Order->getDiscount();
        $this->verify();

        $this->actual = $Order->getSubTotal();
        $this->verify();

        $this->actual = $Order->getTotal();
        $this->verify();

        $this->actual = $Order->getPaymentTotal();
        $this->verify();

        $this->actual = $Order->getCharge();
        $this->verify();

        $this->actual = $Order->getTax();
        $this->verify();

        $this->actual = $Order->getDeliveryFeeTotal();
        $this->verify();

        $this->assertNotInstanceOf(OrderStatus::class, $Order->getOrderStatus());
    }

    public function testGetSaleTypes()
    {
        $this->expected = [$this->entityManager->getRepository(SaleType::class)->find(1)];
        $this->actual = $this->Order->getSaleTypes();
        $this->verify();
    }

    #[Group(name: 'decimal')]
    public function testGetTotalPrice()
    {
        $faker = $this->getFaker();
        /** @var Order $Order */
        $Order = static::getContainer()->get(Generator::class)->createOrder(
            $this->Customer,
            [],
            null,
            $faker->randomNumber(5),
            $faker->randomNumber(5)
        );
        // 元の計算式: $Order->getSubTotal() + $Order->getCharge() + $Order->getDeliveryFeeTotal() - $Order->getDiscount();
        $this->expected = bcadd(
            bcadd(bcadd($Order->getSubTotal(), $Order->getCharge(), 2), $Order->getDeliveryFeeTotal(), 2),
            bcsub('0', $Order->getDiscount(), 2),
            2
        );
        $this->actual = $Order->getTotalPrice();
        $this->verify();
    }

    public function testGetMergedProductOrderItems()
    {
        $quantity = '5';    // 配送先あたりの商品の個数
        $times = '2';       // 複数配送の配送先数
        // テストデータの準備
        $Product = new Product();
        $ProductClass = new ProductClass();
        $Order = new Order();
        $ItemProduct = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        foreach (range(1, $times) as $i) {
            $Shipping = new Shipping();
            $Shipping->setOrder($Order);
            $Order->addShipping($Shipping);
            $OrderItem = new OrderItem();
            $OrderItem->setShipping($Shipping)
                ->setOrder($Order)
                ->setProduct($Product)
                ->setProductName('name')
                ->setPrice('1000')
                ->setTax('0')
                ->setQuantity($quantity)
                ->setProductClass($ProductClass)
                ->setClassCategoryName1('name1')
                ->setClassCategoryName2('name2')
                ->setOrderItemType($ItemProduct)
            ;
            $Shipping->addOrderItem($OrderItem);
            $Order->addOrderItem($OrderItem);
        }
        // 実行
        $OrderItems = $Order->getMergedProductOrderItems();
        // 2個目の明細が1個にまとめられているか
        $this->expected = 1;
        $this->actual = count($OrderItems);
        $this->verify();
        // まとめられた明細の商品の個数が全配送先の合計になっているか
        $OrderItem = $OrderItems[0];
        $this->expected = bcmul($quantity, $times, 0);
        $this->actual = $OrderItem->getQuantity();
        $this->verify();
    }

    public function testGetTaxableItems()
    {
        $Order = $this->createTestOrder();
        $this->assertCount(7, $Order->getTaxableItems());
        /** @var OrderItem $Item */
        foreach ($Order->getTaxableItems() as $Item) {
            $this->assertSame(TaxType::TAXATION, $Item->getTaxType()->getId());
        }
    }

    #[Group(name: 'decimal')]
    public function testGetTaxableTotal()
    {
        $Order = $this->createTestOrder();
        $this->assertSame('790187.00', $Order->getTaxableTotal());
    }

    #[Group(name: 'decimal')]
    public function testGetTaxableTotalByTaxRate()
    {
        $Order = $this->createTestOrder();
        $this->assertSame(['10' => '724431.00', '8' => '65756.00'], $Order->getTaxableTotalByTaxRate());
    }

    public function testGetTaxableDiscountItems()
    {
        $Order = $this->createTestOrder();
        $this->assertCount(2, $Order->getTaxableDiscountItems());
    }

    #[Group(name: 'decimal')]
    public function testGetTaxableDiscount()
    {
        $Order = $this->createTestOrder();
        $this->assertSame('-94694.00', $Order->getTaxableDiscount());
    }

    public function testGetTaxFreeDiscountItems()
    {
        $Order = $this->createTestOrder();
        $this->assertCount(2, $Order->getTaxFreeDiscountItems());
        /** @var OrderItem $Item */
        foreach ($Order->getTaxFreeDiscountItems() as $Item) {
            $this->assertNotSame(TaxType::TAXATION, $Item->getTaxType()->getId());
        }
    }

    #[Group(name: 'decimal')]
    public function testGetTaxFreeDiscount()
    {
        $Order = $this->createTestOrder();

        $this->assertSame('-7159.00', $Order->getTaxFreeDiscount());
    }

    #[Group(name: 'decimal')]
    public function testGetTotalByTaxRate()
    {
        $Order = $this->createTestOrder();

        $this->assertSame('65160', $Order->getTotalByTaxRate()['8'], '8%対象値引き後合計');
        $this->assertSame('717868', $Order->getTotalByTaxRate()['10'], '10%対象値引き後合計');
    }

    #[Group(name: 'decimal')]
    public function testGetTaxByTaxRate()
    {
        $Order = $this->createTestOrder();

        $this->assertSame('4827', $Order->getTaxByTaxRate()['8'], '8%対象値引き後消費税額');
        $this->assertSame('65261', $Order->getTaxByTaxRate()['10'], '10%対象値引き後消費税額');
    }

    /**
     * 値引きを税率で按分・丸めした合計が, 実際の課税支払額と ±1円 ずれるケース (#6335).
     *
     * 10%対象 165円 / 8%対象 755円 に 不課税値引 92円 を按分すると, 按分前の値引額は
     * 10%: 16.5円 / 8%: 75.5円 となり, 値引後の税込合計を四捨五入すると総和が支払額より
     * +1円多くなる. 誤差を最高税率(10%)バケットへ寄せ, 総和を支払額(828円)に一致させる.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6335
     */
    #[Group(name: 'decimal')]
    public function testGetTotalByTaxRateWithRoundingDiff()
    {
        $Order = $this->createRoundingDiffOrder(RoundingType::ROUND);

        $total = $Order->getTotalByTaxRate();

        $this->assertSame('680', $total['8'], '8%対象値引き後合計');
        $this->assertSame('148', $total['10'], '10%対象値引き後合計(誤差-1円を補正)');
        $this->assertSame('828', bcadd($total['8'], $total['10'], 0), '税率別合計の総和が支払額と一致していません');
    }

    /**
     * 基本税率が切り捨て設定 + 値引きで税率別合計がずれるケース (#6335).
     *
     * 切り捨て(FLOOR)では按分後の税込合計が切り下がるため, 補正前は総和が支払額より
     * 小さくなる. 誤差を最高税率(10%)バケットへ寄せて支払額(828円)に一致させる.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6335#issuecomment-2626666163
     */
    #[Group(name: 'decimal')]
    public function testGetTotalByTaxRateWithFloorRounding()
    {
        $Order = $this->createRoundingDiffOrder(RoundingType::FLOOR);

        $total = $Order->getTotalByTaxRate();

        $this->assertSame('679', $total['8'], '8%対象値引き後合計');
        $this->assertSame('149', $total['10'], '10%対象値引き後合計(誤差+1円を補正)');
        $this->assertSame('828', bcadd($total['8'], $total['10'], 0), '税率別合計の総和が支払額と一致していません');
    }

    /**
     * 税率別の税込合計と消費税額が整合する (補正後の税込合計から割戻し計算する) (#6335).
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6335
     */
    #[Group(name: 'decimal')]
    public function testGetTaxByTaxRateDerivedFromCorrectedTotal()
    {
        $Order = $this->createRoundingDiffOrder(RoundingType::ROUND);

        $tax = $Order->getTaxByTaxRate();

        // 8%: round(680 * 8 / 108) = 50, 10%: round(148 * 10 / 110) = 13
        $this->assertSame('50', $tax['8'], '8%対象消費税額');
        $this->assertSame('13', $tax['10'], '10%対象消費税額');
    }

    /**
     * 少額ポイント(1ポイント=1円)利用時の按分でも総和が支払額に一致する (#6335).
     *
     * 8%対象 5,000円 / 10%対象 5,000円 に 不課税値引 1円 を按分すると, 両税率とも
     * 按分前の値引額が 0.5円 となり丸めで誤差が生じる. 誤差を最高税率へ寄せる.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6335#issuecomment-2626666163
     */
    #[Group(name: 'decimal')]
    public function testGetTotalByTaxRateWithSmallPoint()
    {
        $Order = $this->createRoundingDiffOrder(RoundingType::ROUND, 5000, 5000, -1);

        $total = $Order->getTotalByTaxRate();

        $this->assertSame('9999', bcadd((string) $total['8'], (string) $total['10'], 0), '税率別合計の総和が支払額と一致していません');
    }

    /**
     * 基本税率(10%)が切り捨て設定 + ポイント利用時の実報告シナリオ (#6335 コメント).
     *
     * 実際に発生した受注 (4.2系):
     *   - 税率10%対象: 商品 2,200円 + 送料 770円 = 2,970円 (税込)
     *   - 税率8%対象 : 商品 1,080円 (税込)
     *   - ポイント値引(不課税): -2円
     *   - 合計(支払額): 4,048円
     * 補正前は 10%対象 2,968円 / 8%対象 1,079円 となり総和 4,047円 で支払額と 1円ずれていた.
     * 誤差(+1円)を最高税率(10%)へ寄せ, 10%対象 2,969円 として総和を 4,048円 に一致させる.
     * 税額(割戻し)は 10%=269円 / 8%=79円 となり, 報告画面の「うち消費税」と一致する.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6335#issuecomment-2626666163
     */
    #[Group(name: 'decimal')]
    public function testGetTotalByTaxRateWithFloorAndPoint()
    {
        $Order = $this->createRoundingDiffOrder(RoundingType::FLOOR, 2970, 1080, -2);

        $total = $Order->getTotalByTaxRate();
        $this->assertSame('1079', $total['8'], '8%対象値引き後合計');
        $this->assertSame('2969', $total['10'], '10%対象値引き後合計(誤差+1円を補正)');
        $this->assertSame('4048', bcadd($total['8'], $total['10'], 0), '税率別合計の総和が支払額(4,048円)と一致していません');

        $tax = $Order->getTaxByTaxRate();
        $this->assertSame('79', $tax['8'], '8%対象消費税額');
        $this->assertSame('269', $tax['10'], '10%対象消費税額');
    }

    /**
     * RoundingType 未設定の課税明細があっても致命的エラーにならないこと.
     *
     * 通常は PurchaseFlow の TaxProcessor が必ず設定するが, 明細を直接組み立てた場合は
     * null になり得る. getTaxByTaxRate() は元から null を読み飛ばすため, 両者で
     * 集計対象の税率がずれると OrderPdfService が getTaxByTaxRate()[$rate] で
     * 未定義キーを引く. 税率キーの集合が一致することを検証する.
     */
    #[Group(name: 'decimal')]
    public function testGetTotalByTaxRateWithoutRoundingType()
    {
        $Order = $this->createTestOrder();

        $Taxation = $this->entityManager->find(TaxType::class, TaxType::TAXATION);
        $this->assertInstanceOf(TaxType::class, $Taxation);
        $ProductItem = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        $this->assertInstanceOf(OrderItemType::class, $ProductItem);
        $TaxExcluded = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);
        $this->assertInstanceOf(TaxDisplayType::class, $TaxExcluded);

        // RoundingType を設定しない課税明細を追加する
        $OrderItem = new OrderItem();
        $OrderItem->setTaxType($Taxation);
        $OrderItem->setTaxRate('20');
        $OrderItem->setPrice('1000');
        $OrderItem->setTax('200');
        $OrderItem->setQuantity('1');
        $OrderItem->setOrderItemType($ProductItem);
        $OrderItem->setTaxDisplayType($TaxExcluded);
        $Order->addOrderItem($OrderItem);

        $total = $Order->getTotalByTaxRate();

        $this->assertArrayNotHasKey('20', $total, 'RoundingType 未設定の税率は集計対象外');
        $this->assertSame(
            array_keys($Order->getTaxByTaxRate()),
            array_keys($total),
            'getTotalByTaxRate と getTaxByTaxRate の税率が一致すること'
        );
    }

    /**
     * 丸め誤差検証用の受注を生成する.
     *
     * ポイント利用 (不課税値引) を課税対象の税率別合計で按分する最小構成
     * (10%/8% の課税商品 + ポイント明細). PointHelper::addPointDiscountItem と同じく
     * ポイント明細は OrderItemType::POINT / TaxType::NON_TAXABLE で生成する.
     *
     * @param int $roundingTypeId 税率別の丸め規則 (RoundingType::ROUND|FLOOR|CEIL)
     * @param int $total10        10%対象の税込合計
     * @param int $total8         8%対象の税込合計
     * @param int $discount       ポイント値引額 (負数)
     */
    private function createRoundingDiffOrder(int $roundingTypeId, int $total10 = 165, int $total8 = 755, int $discount = -92): Order
    {
        $Taxation = $this->entityManager->find(TaxType::class, TaxType::TAXATION);
        $NonTaxable = $this->entityManager->find(TaxType::class, TaxType::NON_TAXABLE);
        $ProductItem = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        $PointItem = $this->entityManager->find(OrderItemType::class, OrderItemType::POINT);
        $TaxIncluded = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $RoundingType = $this->entityManager->find(RoundingType::class, $roundingTypeId);

        $data = [
            [$Taxation, 10, $total10, $ProductItem],
            [$Taxation, 8, $total8, $ProductItem],
            [$NonTaxable, 0, $discount, $PointItem],
        ];

        $Order = new Order();
        foreach ($data as $row) {
            $OrderItem = new OrderItem();
            $OrderItem->setTaxType($row[0]);
            $OrderItem->setTaxRate((string) $row[1]);
            $OrderItem->setPrice((string) $row[2]);
            $OrderItem->setTax('0');
            $OrderItem->setQuantity('1');
            $OrderItem->setOrderItemType($row[3]);
            $OrderItem->setTaxDisplayType($TaxIncluded);
            $OrderItem->setRoundingType($RoundingType);

            $Order->addOrderItem($OrderItem);
        }

        return $Order;
    }

    protected function createTestOrder()
    {
        $Taxation = $this->entityManager->find(TaxType::class, TaxType::TAXATION);
        $NonTaxable = $this->entityManager->find(TaxType::class, TaxType::NON_TAXABLE);
        $TaxExempt = $this->entityManager->find(TaxType::class, TaxType::TAX_EXEMPT);

        $ProductItem = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        $DeliveryFee = $this->entityManager->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $Charge = $this->entityManager->find(OrderItemType::class, OrderItemType::CHARGE);
        $DiscountItem = $this->entityManager->find(OrderItemType::class, OrderItemType::DISCOUNT);

        $TaxIncluded = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $TaxExcluded = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);

        $RoundingType = $this->TaxRule->getRoundingType();

        // 税率ごとに金額を集計する
        $data = [
            [$Taxation, 10, 71141, round(71141 * (10 / 100)), 5, $ProductItem, $TaxExcluded, $RoundingType],    // 商品明細
            [$Taxation, 10, 92778, round(92778 * (10 / 100)), 4, $ProductItem, $TaxExcluded, $RoundingType],    // 商品明細
            [$Taxation, 8, 15221, round(15221 * (8 / 100)), 5, $ProductItem, $TaxExcluded, $RoundingType],      // 商品明細
            [$Taxation, 10, -71141, round(-71141 * (10 / 100)), 1, $DiscountItem, $TaxExcluded, $RoundingType],  // 課税値引き
            [$Taxation, 8, -15221, round(-15221 * (8 / 100)), 1, $DiscountItem, $TaxExcluded, $RoundingType],    // 課税値引き
            [$Taxation, 10, 1000, round(1000 * (10 / 100)), 1, $DeliveryFee, $TaxIncluded, $RoundingType],    // 送料
            [$Taxation, 10, 2187, round(1000 * (10 / 100)), 1, $Charge, $TaxIncluded, $RoundingType],    // 手数料
            [$NonTaxable, 0, -7000, 0, 1, $DiscountItem, $TaxIncluded, $RoundingType],    // 不課税明細
            [$TaxExempt, 0, -159, 0, 1, $DiscountItem, $TaxIncluded, $RoundingType],     // 非課税明細
        ];

        $Order = new Order();
        foreach ($data as $row) {
            $OrderItem = new OrderItem();
            $OrderItem->setTaxType($row[0]);
            $OrderItem->setTaxRate((string) $row[1]);
            $OrderItem->setPrice((string) $row[2]);
            $OrderItem->setTax((string) $row[3]);
            $OrderItem->setQuantity((string) $row[4]);
            $OrderItem->setOrderItemType($row[5]);
            $OrderItem->setTaxDisplayType($row[6]);
            $OrderItem->setRoundingType($row[7]);

            $Order->addOrderItem($OrderItem);
        }

        return $Order;
    }
}
