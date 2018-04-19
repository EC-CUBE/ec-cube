<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Tests\Repository;

use Eccube\Common\Constant;
use Eccube\Entity\Delivery;
use Eccube\Entity\Master\ProductType;
use Eccube\Entity\PaymentOption;
use Eccube\Repository\PaymentRepository;
use Eccube\Tests\EccubeTestCase;

class PaymentRepositoryTest extends EccubeTestCase
{

    public function test_findAllowedPayment()
    {
        $productTypes = array(7, 6);
        $productTypes = array_unique($productTypes);

        // $paymentOption = $app['eccube.repository.payment_option']->getPaymentOption($productTypes);
        $deliveries = $this->app['eccube.repository.delivery']->getDeliveries($productTypes);

        // 支払方法を取得
        $payments = $this->app['eccube.repository.payment']->findAllowedPayments($deliveries);

        $this->expected = 0;
        $this->actual = count($payments);
        $this->verify('存在しない商品種別を指定しているため取得できない');

        if (count($productTypes) > 1) {
            $deliveries = $this->app['eccube.repository.delivery']->findAllowedDeliveries($productTypes, $payments);
        }
    }

    public function testFindAllowedPaymentWithDefault()
    {
        $productTypes = array(1);
        $productTypes = array_unique($productTypes);

        // $paymentOption = $app['eccube.repository.payment_option']->getPaymentOption($productTypes);
        $deliveries = $this->app['eccube.repository.delivery']->getDeliveries($productTypes);

        // 支払方法を取得
        $payments = $this->app['eccube.repository.payment']->findAllowedPayments($deliveries);

        $this->expected = 4;
        $this->actual = count($payments);
        $this->verify();
    }

    public function testFindAllowedPaymentWithDeliveryOnly()
    {
        $productTypes = array(1, 2);
        $productTypes = array_unique($productTypes);

        // $paymentOption = $app['eccube.repository.payment_option']->getPaymentOption($productTypes);
        $deliveries = $this->app['eccube.repository.delivery']->getDeliveries($productTypes);

        // 支払方法を取得
        $payments = $this->app['eccube.repository.payment']->findAllowedPayments($deliveries);

        $this->expected = 1;
        $this->actual = count($payments);
        $this->verify('商品種別共通の支払い方法は'.$this->expected.'種類です');
    }

    /**
     * 共通する支払い方法が存在しない場合.
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1162
     */
    public function testFindAllowedPaymentWithExclusion()
    {
        $productTypes = array(1, 2);
        $productTypes = array_unique($productTypes);

        // ProductType 1 と 2 で, 共通する支払い方法を削除しておく
        $PaymentOption = $this
            ->app['orm.em']
            ->getRepository('\Eccube\Entity\PaymentOption')
            ->findOneBy(
                array(
                    'delivery_id' => 1,
                    'payment_id' => 3
                )
            );
        $this->assertNotNull($PaymentOption);
        $this->app['orm.em']->remove($PaymentOption);
        $this->app['orm.em']->flush();

        $deliveries = $this->app['eccube.repository.delivery']->getDeliveries($productTypes);

        // 支払方法を取得
        $payments = $this->app['eccube.repository.payment']->findAllowedPayments($deliveries);

        $this->expected = 0;
        $this->actual = count($payments);
        $this->verify('商品種別共通の支払い方法は'.$this->expected.'種類です');
    }

    /**
     * 同じ商品種別ならどの支払い方法でも選択可能
     * @link https://github.com/EC-CUBE/ec-cube/pull/2325
     */
    public function testFindAllowedPayment_SameProductType()
    {
        $typeA = $this->createProductType('テスト種別A', 100);

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->app['eccube.repository.payment'];

        $payment1 = $paymentRepository->find(1);
        $payment2 = $paymentRepository->find(2);
        $payment3 = $paymentRepository->find(3);

        {
            $delivery1 = $this->createDelivery('テスト配送1', $typeA, array($payment1, $payment2));
            $delivery2 = $this->createDelivery('テスト配送2', $typeA, array($payment1));

            $actual = $paymentRepository->findAllowedPayments(array($delivery1, $delivery2));

            $actualIds = array_values(array_map(function($p) { return $p['id']; }, $actual));
            self::assertEquals(array(1, 2), $actualIds);
        }
        {
            $delivery1 = $this->createDelivery('テスト配送1', $typeA, array($payment1, $payment2));
            $delivery2 = $this->createDelivery('テスト配送2', $typeA, array($payment3));

            $actual = $paymentRepository->findAllowedPayments(array($delivery1, $delivery2));

            $actualIds = array_values(array_map(function($p) { return $p['id']; }, $actual));
            self::assertEquals(array(1, 2, 3), $actualIds);
        }
    }

    /**
     * 異なる商品種別なら共通する支払方法のみ選択可能
     * @link https://github.com/EC-CUBE/ec-cube/pull/2325
     */
    public function testFindAllowedPayment_DifferentProductType()
    {
        $typeA = $this->createProductType('テスト種別A', 100);
        $typeB = $this->createProductType('テスト種別B', 101);

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->app['eccube.repository.payment'];

        $payment1 = $paymentRepository->find(1);
        $payment2 = $paymentRepository->find(2);
        $payment3 = $paymentRepository->find(3);

        // 共通する支払方法がある場合
        {
            $delivery1 = $this->createDelivery('テスト配送1', $typeA, array($payment1, $payment2));
            $delivery2 = $this->createDelivery('テスト配送2', $typeB, array($payment1));

            $actual = $paymentRepository->findAllowedPayments(array($delivery1, $delivery2));

            $actualIds = array_values(array_map(function($p) { return $p['id']; }, $actual));
            self::assertEquals(array(1), $actualIds);
        }

        // 共通する支払方法がない場合
        {
            $delivery1 = $this->createDelivery('テスト配送1', $typeA, array($payment1, $payment2));
            $delivery2 = $this->createDelivery('テスト配送2', $typeB, array($payment3));

            $actual = $paymentRepository->findAllowedPayments(array($delivery1, $delivery2));

            $actualIds = array_values(array_map(function($p) { return $p['id']; }, $actual));
            self::assertEquals(array(), $actualIds);
        }
    }

    private function createProductType($name, $id)
    {
        $productType = new ProductType();
        $productType->setName($name);
        $productType->setId($id);
        $productType->setRank($id);
        $this->app['orm.em']->persist($productType);
        $this->app['orm.em']->flush($productType);
        return $productType;
    }

    private function createDelivery($name, ProductType $productType, $payments = array())
    {
        $newDelivery = new Delivery();
        $newDelivery->setName($name);
        $newDelivery->setServiceName($name);
        $newDelivery->setProductType($productType);
        $newDelivery->setCreator($this->createMember());
        $newDelivery->setDelFlg(Constant::DISABLED);

        $this->app['orm.em']->persist($newDelivery);
        $this->app['orm.em']->flush($newDelivery);

        /** @var Payment $payment */
        foreach ($payments as $payment) {
            $option = new PaymentOption();
            $option->setDeliveryId($newDelivery->getId());
            $option->setDelivery($newDelivery);
            $option->setPaymentId($payment->getId());
            $option->setPayment($payment);
            $this->app['orm.em']->persist($option);
            $this->app['orm.em']->flush($option);
        }

        return $newDelivery;
    }

    public function testFindAllArray()
    {
        $Results = $this->app['eccube.repository.payment']->findAllArray();

        $this->assertTrue(is_array($Results));

        $this->expected = '銀行振込';
        $this->actual = $Results[3]['method'];
        $this->verify();
    }

    public function testFindPayments()
    {
        $productTypes = array(1);
        $productTypes = array_unique($productTypes);

        // $paymentOption = $app['eccube.repository.payment_option']->getPaymentOption($productTypes);
        $deliveries = $this->app['eccube.repository.delivery']->getDeliveries($productTypes);

        // 支払方法を取得
        $payments = $this->app['eccube.repository.payment']->findPayments($deliveries[0]);

        $this->expected = 4;
        $this->actual = count($payments);
        $this->verify();

        $this->assertTrue(is_array($payments[0]));
    }

    public function testFindPaymentsAsObjects()
    {
        $productTypes = array(1);
        $productTypes = array_unique($productTypes);

        // $paymentOption = $app['eccube.repository.payment_option']->getPaymentOption($productTypes);
        $deliveries = $this->app['eccube.repository.delivery']->getDeliveries($productTypes);

        // 支払方法を取得
        $payments = $this->app['eccube.repository.payment']->findPayments($deliveries[0], true);

        $this->expected = 4;
        $this->actual = count($payments);
        $this->verify();

        $this->assertTrue(is_object($payments[0]));
    }
}
