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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Delivery;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\PaymentOption;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\PaymentOptionRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Tests\EccubeTestCase;

class PaymentRepositoryTest extends EccubeTestCase
{
    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var PaymentOptionRepository
     */
    protected $paymentOptionRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->deliveryRepository = $this->entityManager->getRepository(\Eccube\Entity\Delivery::class);
        $this->paymentRepository = $this->entityManager->getRepository(\Eccube\Entity\Payment::class);
        $this->paymentOptionRepository = $this->entityManager->getRepository(\Eccube\Entity\PaymentOption::class);
    }

    public function testFindAllowedPaymentEmpty()
    {
        $saleTypes = [7, 6];
        $saleTypes = array_unique($saleTypes);

        // $paymentOption = $app['eccube.repository.payment_option']->getPaymentOption($saleTypes);
        $deliveries = $this->deliveryRepository->getDeliveries($saleTypes);

        // 支払方法を取得
        $payments = $this->paymentRepository->findAllowedPayments($deliveries);

        $this->expected = 0;
        $this->actual = count($payments);
        $this->verify('存在しない販売種別を指定しているため取得できない');

        $deliveries = $this->deliveryRepository->findAllowedDeliveries($saleTypes, $payments);
        $this->assertEmpty($deliveries);
    }

    public function testFindAllowedPaymentWithDefault()
    {
        $saleTypes = [1];
        $saleTypes = array_unique($saleTypes);

        // $paymentOption = $app['eccube.repository.payment_option']->getPaymentOption($saleTypes);
        $deliveries = $this->deliveryRepository->getDeliveries($saleTypes);

        // 支払方法を取得
        $payments = $this->paymentRepository->findAllowedPayments($deliveries);

        $this->expected = 4;
        $this->actual = count($payments);
        $this->verify();
    }

    public function testFindAllowedPaymentWithDeliveryOnly()
    {
        $saleTypes = [1, 2];
        $saleTypes = array_unique($saleTypes);

        // $paymentOption = $app['eccube.repository.payment_option']->getPaymentOption($saleTypes);
        $deliveries = $this->deliveryRepository->getDeliveries($saleTypes);

        // 支払方法を取得
        $payments = $this->paymentRepository->findAllowedPayments($deliveries);

        $this->expected = 1;
        $this->actual = count($payments);
        $this->verify('販売種別共通の支払い方法は'.$this->expected.'種類です');
    }

    /**
     * 共通する支払い方法が存在しない場合.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1162
     */
    public function testFindAllowedPaymentWithExclusion()
    {
        $saleTypes = [1, 2];
        $saleTypes = array_unique($saleTypes);

        // SaleType 1 と 2 で, 共通する支払い方法を削除しておく
        $PaymentOption = $this->paymentOptionRepository->findOneBy([
            'delivery_id' => 1,
            'payment_id' => 3,
        ]);
        $this->assertNotNull($PaymentOption);
        $this->entityManager->remove($PaymentOption);
        $this->entityManager->flush();

        $deliveries = $this->deliveryRepository->getDeliveries($saleTypes);

        // 支払方法を取得
        $payments = $this->paymentRepository->findAllowedPayments($deliveries);

        $this->expected = 0;
        $this->actual = count($payments);
        $this->verify('販売種別共通の支払い方法は'.$this->expected.'種類です');
    }

    /**
     * 同じ商品種別ならどの支払い方法でも選択可能
     *
     * @see https://github.com/EC-CUBE/ec-cube/pull/2325
     */
    public function testFindAllowedPaymentSameSaleType()
    {
        $typeA = $this->createSaleType('テスト種別A', 100);

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->paymentRepository;

        $payment1 = $paymentRepository->find(1);
        $payment2 = $paymentRepository->find(2);
        $payment3 = $paymentRepository->find(3);

        $delivery1 = $this->createDelivery('テスト配送1', $typeA, [$payment1, $payment2]);
        $delivery2 = $this->createDelivery('テスト配送2', $typeA, [$payment1]);

        $actual = $paymentRepository->findAllowedPayments([$delivery1, $delivery2]);

        $actualIds = array_values(array_map(function ($p) { return $p['id']; }, $actual));
        self::assertEquals([1, 2], $actualIds);

        $delivery1 = $this->createDelivery('テスト配送1', $typeA, [$payment1, $payment2]);
        $delivery2 = $this->createDelivery('テスト配送2', $typeA, [$payment3]);

        $actual = $paymentRepository->findAllowedPayments([$delivery1, $delivery2]);

        $actualIds = array_values(array_map(function ($p) { return $p['id']; }, $actual));
        self::assertEquals([1, 2, 3], $actualIds);
    }

    /**
     * 異なる商品種別なら共通する支払方法のみ選択可能
     *
     * @see https://github.com/EC-CUBE/ec-cube/pull/2325
     */
    public function testFindAllowedPaymentDifferentSaleType()
    {
        $typeA = $this->createSaleType('テスト種別A', 100);
        $typeB = $this->createSaleType('テスト種別B', 101);

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->paymentRepository;

        $payment1 = $paymentRepository->find(1);
        $payment2 = $paymentRepository->find(2);
        $payment3 = $paymentRepository->find(3);

        // 共通する支払方法がある場合

        $delivery1 = $this->createDelivery('テスト配送1', $typeA, [$payment1, $payment2]);
        $delivery2 = $this->createDelivery('テスト配送2', $typeB, [$payment1]);

        $actual = $paymentRepository->findAllowedPayments([$delivery1, $delivery2]);

        $actualIds = array_values(array_map(function ($p) { return $p['id']; }, $actual));
        self::assertEquals([1], $actualIds);

        // 共通する支払方法がない場合

        $delivery1 = $this->createDelivery('テスト配送1', $typeA, [$payment1, $payment2]);
        $delivery2 = $this->createDelivery('テスト配送2', $typeB, [$payment3]);

        $actual = $paymentRepository->findAllowedPayments([$delivery1, $delivery2]);

        $actualIds = array_values(array_map(function ($p) { return $p['id']; }, $actual));
        self::assertEquals([], $actualIds);
    }

    private function createSaleType($name, $id)
    {
        $SaleType = new SaleType();
        $SaleType->setName($name);
        $SaleType->setId($id);
        $SaleType->setSortNo($id);
        $this->entityManager->persist($SaleType);
        $this->entityManager->flush($SaleType);

        return $SaleType;
    }

    private function createDelivery($name, SaleType $SaleType, $payments = [])
    {
        $newDelivery = new Delivery();
        $newDelivery->setName($name);
        $newDelivery->setServiceName($name);
        $newDelivery->setSaleType($SaleType);
        $newDelivery->setCreator($this->createMember());

        $this->entityManager->persist($newDelivery);
        $this->entityManager->flush($newDelivery);

        /** @var Payment $payment */
        foreach ($payments as $payment) {
            $option = new PaymentOption();
            $option->setDeliveryId($newDelivery->getId());
            $option->setDelivery($newDelivery);
            $option->setPaymentId($payment->getId());
            $option->setPayment($payment);
            $this->entityManager->persist($option);
            $this->entityManager->flush($option);
        }

        return $newDelivery;
    }

    public function testFindAllArray()
    {
        $Results = $this->paymentRepository->findAllArray();

        $this->assertTrue(is_array($Results));

        $this->expected = '銀行振込';
        $this->actual = $Results[3]['method'];
        $this->verify();
    }

    public function testFindPayments()
    {
        $saleTypes = [1];
        $saleTypes = array_unique($saleTypes);

        // $paymentOption = $app['eccube.repository.payment_option']->getPaymentOption($saleTypes);
        $deliveries = $this->deliveryRepository->getDeliveries($saleTypes);

        // 支払方法を取得
        $payments = $this->paymentRepository->findPayments($deliveries[0]);

        $this->expected = 4;
        $this->actual = count($payments);
        $this->verify();

        $this->assertTrue(is_array($payments[0]));
    }

    public function testFindPaymentsAsObjects()
    {
        $saleTypes = [1];
        $saleTypes = array_unique($saleTypes);

        // $paymentOption = $app['eccube.repository.payment_option']->getPaymentOption($saleTypes);
        $deliveries = $this->deliveryRepository->getDeliveries($saleTypes);

        // 支払方法を取得
        $payments = $this->paymentRepository->findPayments($deliveries[0], true);

        $this->expected = 4;
        $this->actual = count($payments);
        $this->verify();

        $this->assertTrue(is_object($payments[0]));
    }
}
