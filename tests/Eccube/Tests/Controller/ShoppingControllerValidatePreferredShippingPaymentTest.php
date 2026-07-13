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

namespace Eccube\Tests\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Controller\ShoppingController;
use Eccube\DependencyInjection\Facade\LoggerFacade;
use Eccube\Entity\Customer;
use Eccube\Entity\Delivery;
use Eccube\Entity\Order;
use Eccube\Entity\Payment;
use Eccube\Entity\Shipping;
use Eccube\Log\Logger;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\PaymentRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ShoppingController::validatePreferredShippingPayment の単体テスト.
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6819
 */
final class ShoppingControllerValidatePreferredShippingPaymentTest extends TestCase
{
    private ShoppingController|MockObject $controller;

    private DeliveryRepository|MockObject $deliveryRepository;

    private PaymentRepository|MockObject $paymentRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetLoggerFacade();
        LoggerFacade::init($this->createStub(ContainerInterface::class), $this->createStub(Logger::class));

        $this->deliveryRepository = $this->createMock(DeliveryRepository::class);
        $this->paymentRepository = $this->createMock(PaymentRepository::class);

        $this->controller = $this->getMockBuilder(ShoppingController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $reflection = new \ReflectionClass($this->controller);
        $reflection->getProperty('deliveryRepository')->setValue($this->controller, $this->deliveryRepository);
        $reflection->getProperty('paymentRepository')->setValue($this->controller, $this->paymentRepository);
    }

    protected function tearDown(): void
    {
        $this->resetLoggerFacade();

        parent::tearDown();
    }

    /**
     * 会員IDが存在しない場合, すべての戻り値がnullまたはfalseであること.
     */
    public function testWithoutCustomerId(): void
    {
        $Customer = $this->createCustomerWithId(null);

        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertNull($result['preferredPaymentId']);
        $this->assertNull($result['preferredPaymentName']);
        $this->assertNull($result['preferredDeliveryId']);
        $this->assertNull($result['preferredDeliveryName']);
        $this->assertFalse($result['isMultipleShipping']);
        $this->assertNull($result['preferredUnavailableReason']);
    }

    /**
     * 保存情報が存在しない場合, preferredUnavailableReasonがnullであること.
     */
    public function testWithoutPreferredInfo(): void
    {
        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment();
        $Customer->setPreferredDelivery();

        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertNull($result['preferredUnavailableReason']);
    }

    /**
     * 保存済み Payment が非公開の場合, 支払い方法が利用不可の理由が返ること.
     */
    public function testWithInvisiblePayment(): void
    {
        $Payment = $this->createMock(Payment::class);
        $Payment->method('isVisible')->willReturn(false);

        $Delivery = $this->createMock(Delivery::class);
        $Delivery->method('isVisible')->willReturn(true);

        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);

        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertSame('front.shopping.preferred_payment_unavailable', $result['preferredUnavailableReason']);
    }

    /**
     * 保存済み Delivery が非公開の場合, 配送方法が利用不可の理由が返ること.
     */
    public function testWithInvisibleDelivery(): void
    {
        $Payment = $this->createMock(Payment::class);
        $Payment->method('isVisible')->willReturn(true);

        $Delivery = $this->createMock(Delivery::class);
        $Delivery->method('isVisible')->willReturn(false);

        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);

        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertSame('front.shopping.preferred_delivery_unavailable', $result['preferredUnavailableReason']);
    }

    /**
     * Payment のみ保存されている場合(参照先削除等), 配送方法が利用不可の理由が返ること.
     */
    public function testWithPaymentOnly(): void
    {
        $Payment = $this->createMock(Payment::class);
        $Payment->method('isVisible')->willReturn(true);

        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery();

        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertSame('front.shopping.preferred_delivery_unavailable', $result['preferredUnavailableReason']);
    }

    /**
     * Delivery のみ保存されている場合(参照先削除等), 支払い方法が利用不可の理由が返ること.
     */
    public function testWithDeliveryOnly(): void
    {
        $Delivery = $this->createMock(Delivery::class);
        $Delivery->method('isVisible')->willReturn(true);

        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment();
        $Customer->setPreferredDelivery($Delivery);

        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertSame('front.shopping.preferred_payment_unavailable', $result['preferredUnavailableReason']);
    }

    /**
     * 利用可能な配送方法が存在しない場合, 組み合わせ不可の理由が返ること.
     */
    public function testWithoutAvailableDeliveries(): void
    {
        $Payment = $this->createMock(Payment::class);
        $Payment->method('isVisible')->willReturn(true);
        $Payment->method('getId')->willReturn(1);

        $Delivery = $this->createMock(Delivery::class);
        $Delivery->method('isVisible')->willReturn(true);
        $Delivery->method('getId')->willReturn(1);

        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);

        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());
        $Order->method('getSaleTypes')->willReturn([]);

        $this->deliveryRepository->method('getDeliveries')->willReturn([]);

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertSame('front.shopping.preferred_incompatible_combination', $result['preferredUnavailableReason']);
    }

    /**
     * 保存された配送方法が利用可能な配送方法に含まれていない場合, 組み合わせ不可の理由が返ること.
     */
    public function testWithIncompatibleDelivery(): void
    {
        $Payment = $this->createMock(Payment::class);
        $Payment->method('isVisible')->willReturn(true);
        $Payment->method('getId')->willReturn(1);

        $Delivery = $this->createMock(Delivery::class);
        $Delivery->method('isVisible')->willReturn(true);
        $Delivery->method('getId')->willReturn(1);

        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);

        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());
        $Order->method('getSaleTypes')->willReturn([]);

        // 利用可能な配送方法に保存された配送方法(ID:1)が含まれない.
        $availableDelivery = $this->createMock(Delivery::class);
        $availableDelivery->method('getId')->willReturn(2);
        $this->deliveryRepository->method('getDeliveries')->willReturn([$availableDelivery]);

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertSame('front.shopping.preferred_incompatible_combination', $result['preferredUnavailableReason']);
    }

    /**
     * 保存された支払い方法が利用可能な支払い方法に含まれていない場合, 組み合わせ不可の理由が返ること.
     */
    public function testWithIncompatiblePayment(): void
    {
        $Payment = $this->createMock(Payment::class);
        $Payment->method('isVisible')->willReturn(true);
        $Payment->method('getId')->willReturn(1);

        $Delivery = $this->createMock(Delivery::class);
        $Delivery->method('isVisible')->willReturn(true);
        $Delivery->method('getId')->willReturn(1);

        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);

        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());
        $Order->method('getSaleTypes')->willReturn([]);

        $availableDelivery = $this->createMock(Delivery::class);
        $availableDelivery->method('getId')->willReturn(1);
        $this->deliveryRepository->method('getDeliveries')->willReturn([$availableDelivery]);

        // 利用可能な支払い方法に保存された支払い方法(ID:1)が含まれない.
        $availablePayment = $this->createMock(Payment::class);
        $availablePayment->method('getId')->willReturn(2);
        $availablePayment->method('isVisible')->willReturn(true);
        $this->paymentRepository->method('findAllowedPayments')->willReturn([$availablePayment]);

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertSame('front.shopping.preferred_incompatible_combination', $result['preferredUnavailableReason']);
    }

    /**
     * すべての検証をパスした場合, ID・名称が設定され理由がnullであること.
     */
    public function testSuccess(): void
    {
        $Payment = $this->createMock(Payment::class);
        $Payment->method('isVisible')->willReturn(true);
        $Payment->method('getId')->willReturn(1);
        $Payment->method('getMethod')->willReturn('クレジットカード');

        $Delivery = $this->createMock(Delivery::class);
        $Delivery->method('isVisible')->willReturn(true);
        $Delivery->method('getId')->willReturn(1);
        $Delivery->method('getName')->willReturn('宅配便');

        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);

        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());
        $Order->method('getSaleTypes')->willReturn([]);

        $availableDelivery = $this->createMock(Delivery::class);
        $availableDelivery->method('getId')->willReturn(1);
        $this->deliveryRepository->method('getDeliveries')->willReturn([$availableDelivery]);

        $availablePayment = $this->createMock(Payment::class);
        $availablePayment->method('getId')->willReturn(1);
        $availablePayment->method('isVisible')->willReturn(true);
        $this->paymentRepository->method('findAllowedPayments')->willReturn([$availablePayment]);

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertSame(1, $result['preferredPaymentId']);
        $this->assertSame('クレジットカード', $result['preferredPaymentName']);
        $this->assertSame(1, $result['preferredDeliveryId']);
        $this->assertSame('宅配便', $result['preferredDeliveryName']);
        $this->assertNull($result['preferredUnavailableReason']);
    }

    /**
     * 複数配送先の場合, isMultipleShippingがtrueであること.
     */
    public function testMultipleShipping(): void
    {
        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment();
        $Customer->setPreferredDelivery();

        $shipping1 = $this->createStub(Shipping::class);
        $shipping2 = $this->createStub(Shipping::class);
        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection([$shipping1, $shipping2]));

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertTrue($result['isMultipleShipping']);
    }

    /**
     * 単一配送先の場合, isMultipleShippingがfalseであること.
     */
    public function testSingleShipping(): void
    {
        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment();
        $Customer->setPreferredDelivery();

        $shipping1 = $this->createStub(Shipping::class);
        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection([$shipping1]));

        $result = $this->callValidatePreferredShippingPayment($Customer, $Order);

        $this->assertFalse($result['isMultipleShipping']);
    }

    /**
     * DeliveryRepository::getDeliveries が受注の販売種別で呼び出されること.
     */
    public function testCallsDeliveryRepositoryWithSaleTypes(): void
    {
        $Payment = $this->createMock(Payment::class);
        $Payment->method('isVisible')->willReturn(true);
        $Payment->method('getId')->willReturn(1);

        $Delivery = $this->createMock(Delivery::class);
        $Delivery->method('isVisible')->willReturn(true);
        $Delivery->method('getId')->willReturn(1);

        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);

        $saleTypes = ['dummy_sale_type'];
        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());
        $Order->method('getSaleTypes')->willReturn($saleTypes);

        $availableDelivery = $this->createMock(Delivery::class);
        $availableDelivery->method('getId')->willReturn(1);
        $this->deliveryRepository->expects($this->once())
            ->method('getDeliveries')
            ->with($saleTypes)
            ->willReturn([$availableDelivery]);

        $availablePayment = $this->createMock(Payment::class);
        $availablePayment->method('getId')->willReturn(1);
        $availablePayment->method('isVisible')->willReturn(true);
        $this->paymentRepository->method('findAllowedPayments')->willReturn([$availablePayment]);

        $this->callValidatePreferredShippingPayment($Customer, $Order);
    }

    /**
     * PaymentRepository::findAllowedPayments が一致した配送方法で呼び出されること.
     */
    public function testCallsPaymentRepositoryWithMatchedDelivery(): void
    {
        $Payment = $this->createMock(Payment::class);
        $Payment->method('isVisible')->willReturn(true);
        $Payment->method('getId')->willReturn(1);

        $Delivery = $this->createMock(Delivery::class);
        $Delivery->method('isVisible')->willReturn(true);
        $Delivery->method('getId')->willReturn(1);

        $Customer = $this->createCustomerWithId(1);
        $Customer->setPreferredPayment($Payment);
        $Customer->setPreferredDelivery($Delivery);

        $Order = $this->createMock(Order::class);
        $Order->method('getShippings')->willReturn(new ArrayCollection());
        $Order->method('getSaleTypes')->willReturn([]);

        $availableDelivery = $this->createMock(Delivery::class);
        $availableDelivery->method('getId')->willReturn(1);
        $this->deliveryRepository->method('getDeliveries')->willReturn([$availableDelivery]);

        $availablePayment = $this->createMock(Payment::class);
        $availablePayment->method('getId')->willReturn(1);
        $availablePayment->method('isVisible')->willReturn(true);
        $this->paymentRepository->expects($this->once())
            ->method('findAllowedPayments')
            ->with([$availableDelivery], true)
            ->willReturn([$availablePayment]);

        $this->callValidatePreferredShippingPayment($Customer, $Order);
    }

    /**
     * LoggerFacade のシングルトンをリセットする.
     */
    private function resetLoggerFacade(): void
    {
        $ref = new \ReflectionClass(LoggerFacade::class);
        $ref->getProperty('instance')->setValue(null, null);
    }

    /**
     * 指定IDを持つ Customer を生成する(IDはIDENTITY採番のためリフレクションで設定する).
     */
    private function createCustomerWithId(?int $id): Customer
    {
        $Customer = new Customer();
        $ref = new \ReflectionProperty(Customer::class, 'id');
        $ref->setValue($Customer, $id);

        return $Customer;
    }

    /**
     * private メソッド validatePreferredShippingPayment を呼び出す.
     *
     * @return array{preferredPaymentId: int|null, preferredPaymentName: string|null, preferredDeliveryId: int|null, preferredDeliveryName: string|null, isMultipleShipping: bool, preferredUnavailableReason: string|null}
     */
    private function callValidatePreferredShippingPayment(Customer $Customer, Order $Order, string $logPrefix = '[保存情報検証]'): array
    {
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('validatePreferredShippingPayment');

        return $method->invoke($this->controller, $Customer, $Order, $logPrefix);
    }
}
