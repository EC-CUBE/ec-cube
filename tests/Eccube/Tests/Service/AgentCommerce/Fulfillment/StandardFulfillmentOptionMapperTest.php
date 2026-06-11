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

namespace Eccube\Tests\Service\AgentCommerce\Fulfillment;

use Eccube\Entity\DeliveryDuration;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Payment;
use Eccube\Entity\ProductClass;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Service\AgentCommerce\Fulfillment\StandardFulfillmentOptionMapper;
use Eccube\Tests\EccubeTestCase;

/**
 * Layer 3 tests for StandardFulfillmentOptionMapper.
 *
 * fixtures の Delivery/DeliveryFee/Payment/PaymentOption を用いて、配送先 Pref に対する
 * 送料・代引手数料 (PaymentOption -> Payment::getCharge())・配送日数 (DeliveryDuration の
 * 明細横断 max) が正しく解決されることを検証する。金額は minor unit (JPY=0桁) で確認する。
 */
final class StandardFulfillmentOptionMapperTest extends EccubeTestCase
{
    private ?StandardFulfillmentOptionMapper $mapper = null;

    private ?PrefRepository $prefRepository = null;

    private ?PaymentRepository $paymentRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = self::getContainer()->get(StandardFulfillmentOptionMapper::class);
        $this->prefRepository = self::getContainer()->get(PrefRepository::class);
        $this->paymentRepository = self::getContainer()->get(PaymentRepository::class);
    }

    private function pref(int $id): Pref
    {
        /** @var Pref $pref */
        $pref = $this->prefRepository->find($id);

        return $pref;
    }

    private function firstProductClass(string $name): ProductClass
    {
        $Product = $this->createProduct($name, 1);

        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];

        return $ProductClass;
    }

    public function testReturnsOptionsWithShippingFeeAndPaymentOptions(): void
    {
        $ProductClass = $this->firstProductClass('配送テスト商品');

        $options = $this->mapper->mapForDestination([$ProductClass], $this->pref(27), 'JPY');

        $this->assertNotEmpty($options, '利用可能な配送方法が 1 件以上返る');
        $option = $options[0];
        $this->assertGreaterThan(0, $option->deliveryId, 'deliveryId が解決される');
        $this->assertNotSame('', $option->name, '配送方法名が解決される');
        $this->assertGreaterThanOrEqual(0, $option->shippingFeeMinor, '送料が minor unit (整数) で解決される');
        $this->assertSame('JPY', $option->currencyCode);
        $this->assertIsArray($option->paymentOptions, '支払方法の選択肢が配列で返る');
    }

    public function testCodChargeResolvedViaPaymentOption(): void
    {
        // 代金引換 (id=4) に手数料を設定し、PaymentOption 経由で minor unit に解決されることを確認。
        /** @var Payment $cod */
        $cod = $this->paymentRepository->find(4);
        $cod->setCharge('330');
        $this->entityManager->flush();

        $ProductClass = $this->firstProductClass('代引テスト商品');
        $options = $this->mapper->mapForDestination([$ProductClass], $this->pref(13), 'JPY');

        $codChargeMinor = null;
        foreach ($options as $option) {
            foreach ($option->paymentOptions as $paymentOption) {
                if ($paymentOption->paymentId === 4) {
                    $codChargeMinor = $paymentOption->chargeMinor;
                    break 2;
                }
            }
        }

        $this->assertNotNull($codChargeMinor, '代金引換が支払選択肢に含まれる');
        $this->assertSame(330, $codChargeMinor, '代引手数料が Payment::getCharge() から minor unit (JPY=330) で解決される');
    }

    public function testDeliveryDaysIsMaxAcrossItems(): void
    {
        $pc2days = $this->firstProductClass('配送2日商品');
        $pc2days->setDeliveryDuration($this->createDuration('お届け2日', 2));
        $pc5days = $this->firstProductClass('配送5日商品');
        $pc5days->setDeliveryDuration($this->createDuration('お届け5日', 5));
        $this->entityManager->flush();

        $options = $this->mapper->mapForDestination([$pc2days, $pc5days], $this->pref(27), 'JPY');

        $this->assertNotEmpty($options);
        $this->assertSame(5, $options[0]->estimatedDeliveryDays, '配送日数は明細横断の最大値 (2 と 5 -> 5)');
    }

    public function testBackorderItemYieldsNullDeliveryDays(): void
    {
        $pcNormal = $this->firstProductClass('通常配送商品');
        $pcNormal->setDeliveryDuration($this->createDuration('お届け3日', 3));
        $pcBackorder = $this->firstProductClass('お取り寄せ商品');
        $pcBackorder->setDeliveryDuration($this->createDuration('お取り寄せ', -1));
        $this->entityManager->flush();

        $options = $this->mapper->mapForDestination([$pcNormal, $pcBackorder], $this->pref(27), 'JPY');

        $this->assertNotEmpty($options);
        $this->assertNull($options[0]->estimatedDeliveryDays, 'お取り寄せ (duration<0) が含まれると配送日数は未確定 (null)');
    }

    private function createDuration(string $name, int $duration): DeliveryDuration
    {
        $DeliveryDuration = new DeliveryDuration();
        $DeliveryDuration->setName($name)->setDuration($duration)->setSortNo($duration + 100);
        $this->entityManager->persist($DeliveryDuration);
        $this->entityManager->flush();

        return $DeliveryDuration;
    }
}
