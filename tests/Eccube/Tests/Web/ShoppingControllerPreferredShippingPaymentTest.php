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

namespace Eccube\Tests\Web;

use Eccube\Entity\Customer;
use Eccube\Entity\Delivery;
use Eccube\Entity\Order;
use Eccube\Entity\Payment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 配送方法・支払い方法の保存と復元の機能テスト.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6819
 */
final class ShoppingControllerPreferredShippingPaymentTest extends AbstractShoppingControllerTestCase
{
    /**
     * 会員に優先配送方法・優先支払方法を保存する.
     */
    private function setPreferred(Customer $Customer, ?Payment $Payment, ?Delivery $Delivery): void
    {
        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);
        $this->entityManager->flush();
    }

    private function findPayment(int $id): Payment
    {
        $Payment = $this->entityManager->getRepository(Payment::class)->find($id);
        $this->assertInstanceOf(Payment::class, $Payment);

        return $Payment;
    }

    private function findDelivery(int $id): Delivery
    {
        $Delivery = $this->entityManager->getRepository(Delivery::class)->find($id);
        $this->assertInstanceOf(Delivery::class, $Delivery);

        return $Delivery;
    }

    private function findProcessingOrder(Customer $Customer): Order
    {
        $Order = $this->entityManager->getRepository(Order::class)->findOneBy(['Customer' => $Customer]);
        $this->assertInstanceOf(Order::class, $Order);

        return $Order;
    }

    /**
     * 非会員購入フローで注文手続き画面まで進める.
     *
     * @return array<string, mixed>
     */
    private function createNonmemberFormData(): array
    {
        $faker = $this->getFaker();
        $email = $faker->safeEmail;
        $form = $this->createShippingFormData();
        $form['email'] = [
            'first' => $email,
            'second' => $email,
        ];

        return $form;
    }

    private function scenarioConfirmAsGuest(): \Symfony\Component\DomCrawler\Crawler
    {
        $this->scenarioCartIn();
        $this->scenarioInput($this->createNonmemberFormData());
        $this->client->followRedirect();

        return $this->scenarioConfirm();
    }

    /**
     * 保存情報がある会員は注文手続き画面に情報ボックスと復元ボタンが表示される.
     */
    public function testIndexWithPreferredShowsInfoBox(): void
    {
        $Customer = $this->createCustomer();
        $Payment = $this->findPayment(3);
        $Delivery = $this->findDelivery(1);
        $this->setPreferred($Customer, $Payment, $Delivery);

        $this->scenarioCartIn($Customer);
        $crawler = $this->scenarioConfirm($Customer);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $preferredBox = $crawler->filter('.ec-orderPreferred');
        $this->assertCount(1, $preferredBox);
        $this->assertStringContainsString('保存された設定があります', $preferredBox->text());
        $this->assertStringContainsString($Delivery->getName(), $preferredBox->text());
        $this->assertStringContainsString($Payment->getMethod(), $preferredBox->text());
        $this->assertStringContainsString('この設定を使用する', $preferredBox->text());
    }

    /**
     * 保存情報がない会員には情報ボックスが表示されない.
     */
    public function testIndexWithoutPreferredHidesInfoBox(): void
    {
        $Customer = $this->createCustomer();

        $this->scenarioCartIn($Customer);
        $crawler = $this->scenarioConfirm($Customer);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('.ec-orderPreferred'));
    }

    /**
     * ゲストユーザーには情報ボックスが表示されない.
     */
    public function testIndexWithGuestHidesInfoBox(): void
    {
        $crawler = $this->scenarioConfirmAsGuest();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('.ec-orderPreferred'));
    }

    /**
     * 復元で保存された配送方法・支払い方法が受注へ適用され, 成功メッセージが表示される.
     */
    public function testRestorePreferred(): void
    {
        $Customer = $this->createCustomer();
        $Payment = $this->findPayment(3);
        $Delivery = $this->findDelivery(1);
        $this->setPreferred($Customer, $Payment, $Delivery);

        $this->scenarioCartIn($Customer);
        $this->scenarioConfirm($Customer);

        // 保存値と異なる支払い方法へ変更しておく.
        $Order = $this->findProcessingOrder($Customer);
        $OtherPayment = $this->findPayment(4);
        $Order->setPayment($OtherPayment);
        $Order->setPaymentMethod($OtherPayment->getMethod());
        $this->entityManager->flush();

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('shopping_restore_preferred'),
            ['_token' => '_dummy']
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        $this->entityManager->refresh($Order);
        $this->assertSame($Payment->getId(), $Order->getPayment()->getId());
        $this->assertSame($Payment->getMethod(), $Order->getPaymentMethod());
        $Shipping = $Order->getShippings()->first();
        $this->assertNotFalse($Shipping);
        $this->entityManager->refresh($Shipping);
        $this->assertSame($Delivery->getId(), $Shipping->getDelivery()->getId());
        $this->assertSame($Delivery->getName(), $Shipping->getShippingDeliveryName());

        // リダイレクト後の画面に成功メッセージが表示される.
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('保存された設定を適用しました', $crawler->html());
    }

    /**
     * 保存された支払い方法が非公開の場合は復元されず, 警告メッセージが表示される.
     */
    public function testRestorePreferredWithInvisiblePayment(): void
    {
        $Customer = $this->createCustomer();
        $Payment = $this->findPayment(3);
        $Delivery = $this->findDelivery(1);
        $this->setPreferred($Customer, $Payment, $Delivery);

        $Payment->setVisible(false);
        $this->entityManager->flush();

        $this->scenarioCartIn($Customer);
        $this->scenarioConfirm($Customer);

        $Order = $this->findProcessingOrder($Customer);
        $beforePaymentId = $Order->getPayment()->getId();

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('shopping_restore_preferred'),
            ['_token' => '_dummy']
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping')));

        $this->entityManager->refresh($Order);
        $this->assertSame($beforePaymentId, $Order->getPayment()->getId());

        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('保存された支払い方法が現在利用できません', $crawler->html());
    }

    /**
     * ゲストユーザーは復元エンドポイントへアクセスできない(403).
     */
    public function testRestorePreferredForbiddenForGuest(): void
    {
        $this->scenarioConfirmAsGuest();

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('shopping_restore_preferred'),
            ['_token' => '_dummy']
        );

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * 会員かつ単一配送先の場合, 注文確認画面に保存チェックボックスが表示される.
     */
    public function testConfirmShowsSaveCheckboxForMember(): void
    {
        $Customer = $this->createCustomer();

        $this->scenarioCartIn($Customer);
        $this->scenarioConfirm($Customer);
        $crawler = $this->scenarioComplete($Customer, $this->generateUrl('shopping_confirm'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $checkbox = $crawler->filter('input[name="_shopping_order[save_preferred_shipping_payment]"]');
        $this->assertCount(1, $checkbox);
        $this->assertStringContainsString('この配送方法と支払い方法を保存する', $crawler->html());
    }

    /**
     * ゲストユーザーには保存チェックボックスが表示されない.
     */
    public function testConfirmHidesSaveCheckboxForGuest(): void
    {
        $this->scenarioConfirmAsGuest();
        $crawler = $this->scenarioComplete(null, $this->generateUrl('shopping_confirm'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('input[name="_shopping_order[save_preferred_shipping_payment]"]'));
    }

    /**
     * チェックボックスONで注文確定すると配送方法・支払い方法が保存され, 既存値は上書きされる.
     */
    public function testCheckoutWithSaveOnPersistsPreferred(): void
    {
        $Customer = $this->createCustomer();
        // 既存の保存値(上書きされることを確認するため, 確定時と異なる支払い方法を設定).
        $this->setPreferred($Customer, $this->findPayment(4), $this->findDelivery(1));

        $this->scenarioCartIn($Customer);
        $this->scenarioConfirm($Customer);
        $this->scenarioComplete($Customer, $this->generateUrl('shopping_confirm'));

        $this->loginTo($Customer);
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('shopping_checkout'),
            [
                '_shopping_order' => [
                    '_token' => 'dummy',
                    'save_preferred_shipping_payment' => '1',
                ],
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_complete')));

        $this->entityManager->clear();
        /** @var Customer $found */
        $found = $this->entityManager->getRepository(Customer::class)->find($Customer->getId());
        $this->assertNotNull($found->getPreferredPayment());
        $this->assertNotNull($found->getPreferredDelivery());
        // scenarioComplete は Payment=3, Delivery=1 で注文する.
        $this->assertSame(3, $found->getPreferredPayment()->getId());
        $this->assertSame(1, $found->getPreferredDelivery()->getId());
    }

    /**
     * チェックボックスOFFで注文確定しても保存されない.
     */
    public function testCheckoutWithoutSaveDoesNotPersistPreferred(): void
    {
        $Customer = $this->createCustomer();

        $this->scenarioCartIn($Customer);
        $this->scenarioConfirm($Customer);
        $this->scenarioComplete($Customer, $this->generateUrl('shopping_confirm'));
        $this->scenarioCheckout($Customer);

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_complete')));

        $this->entityManager->clear();
        /** @var Customer $found */
        $found = $this->entityManager->getRepository(Customer::class)->find($Customer->getId());
        $this->assertNull($found->getPreferredPayment());
        $this->assertNull($found->getPreferredDelivery());
    }
}
