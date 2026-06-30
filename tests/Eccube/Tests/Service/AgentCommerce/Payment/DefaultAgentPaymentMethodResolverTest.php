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

namespace Eccube\Tests\Service\AgentCommerce\Payment;

use Eccube\Entity\Delivery;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Payment;
use Eccube\Entity\ProductClass;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerInterface;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerRegistry;
use Eccube\Service\AgentCommerce\Payment\DefaultAgentPaymentMethodResolver;
use Eccube\Service\AgentCommerce\Payment\PaymentOutcome;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (pure logic) tests for DefaultAgentPaymentMethodResolver.
 *
 * エージェント注文の支払方法解決が handler_id 駆動・sort_no 非依存であることを、
 * リポジトリをモック・ハンドラを in-memory にした純ロジックで検証する。
 * 「登録ハンドラが扱える Payment のみを候補とする」「該当無しは沈黙フォールバックせず
 * null + 元の割当を復元」「明示 handler_id の尊重」「割当済み Payment の優先」を確認する。
 */
final class DefaultAgentPaymentMethodResolverTest extends TestCase
{
    private const HANDLER_ID = 'card';

    private const SUPPORTED_METHOD_CLASS = 'Plugin\\Sample\\AgentCard';

    private const UNSUPPORTED_METHOD_CLASS = 'Plugin\\Sample\\BankTransfer';

    public function testResolvesPaymentSupportedByRequestedHandler(): void
    {
        // 候補は [銀行振込(sort_no 先頭), エージェントカード] の順。ハンドラはカードのみ扱う。
        $bankTransfer = $this->payment('bank', self::UNSUPPORTED_METHOD_CLASS);
        $agentCard = $this->payment('card', self::SUPPORTED_METHOD_CLASS);
        $resolver = $this->resolver(
            handlers: [$this->handler(self::HANDLER_ID, self::SUPPORTED_METHOD_CLASS)],
            candidatePayments: [$bankTransfer, $agentCard],
        );
        $order = $this->orderWithSaleType();

        $resolved = $resolver->resolve($order, self::HANDLER_ID);

        // sort_no 先頭の銀行振込でなく、ハンドラが扱えるカードが選ばれる (sort_no 非依存)。
        $this->assertSame($agentCard, $resolved);
        $this->assertSame($agentCard, $order->getPayment());
        $this->assertSame('card', $order->getPaymentMethod());
    }

    public function testReturnsNullWhenRequestedHandlerNotRegistered(): void
    {
        $resolver = $this->resolver(
            handlers: [$this->handler(self::HANDLER_ID, self::SUPPORTED_METHOD_CLASS)],
            candidatePayments: [$this->payment('card', self::SUPPORTED_METHOD_CLASS)],
        );
        $order = $this->orderWithSaleType();

        $resolved = $resolver->resolve($order, 'not-registered');

        // 未登録 handler_id は沈黙フォールバックせず null。注文の支払方法も据え置き (null のまま)。
        // resolve()/getPayment() は ?Payment 型のため rector が assertNull を assertNotInstanceOf へ
        // 変換する (false/[] は型上返せず実質 null 固定)。method 名 (?string) は assertNull で締める。
        $this->assertNotInstanceOf(Payment::class, $resolved);
        $this->assertNotInstanceOf(Payment::class, $order->getPayment());
        $this->assertNull($order->getPaymentMethod());
    }

    public function testReturnsNullWhenNoCandidateSupportsRequestedHandler(): void
    {
        $original = $this->payment('bank', self::UNSUPPORTED_METHOD_CLASS);
        $resolver = $this->resolver(
            handlers: [$this->handler(self::HANDLER_ID, self::SUPPORTED_METHOD_CLASS)],
            candidatePayments: [$this->payment('bank2', self::UNSUPPORTED_METHOD_CLASS)],
        );
        $order = $this->orderWithSaleType();
        $order->setPayment($original);
        $order->setPaymentMethod($original->getMethod());

        $resolved = $resolver->resolve($order, self::HANDLER_ID);

        // 該当 Payment 無し → null。元の割当 (銀行振込) を復元し注文を汚さない。
        $this->assertNotInstanceOf(Payment::class, $resolved);
        $this->assertSame($original, $order->getPayment());
        $this->assertSame('bank', $order->getPaymentMethod());
    }

    public function testKeepsAlreadyAssignedSupportedPaymentWithoutQueryingRepositories(): void
    {
        $agentCard = $this->payment('card', self::SUPPORTED_METHOD_CLASS);
        $deliveryRepository = $this->createMock(DeliveryRepository::class);
        // 割当済み Payment が適合するなら候補探索 (販売種別→配送業者) は行わない。
        $deliveryRepository->expects($this->never())->method('getDeliveries');
        $paymentRepository = $this->createMock(PaymentRepository::class);
        $paymentRepository->expects($this->never())->method('findAllowedPayments');
        $registry = new AgentCheckoutPaymentHandlerRegistry([$this->handler(self::HANDLER_ID, self::SUPPORTED_METHOD_CLASS)]);
        $resolver = new DefaultAgentPaymentMethodResolver($paymentRepository, $deliveryRepository, $registry);

        $order = $this->orderWithSaleType();
        $order->setPayment($agentCard);

        $resolved = $resolver->resolve($order, self::HANDLER_ID);

        $this->assertSame($agentCard, $resolved);
        $this->assertSame($agentCard, $order->getPayment());
    }

    public function testWithoutHandlerIdResolvesAgentCapableDefault(): void
    {
        $bankTransfer = $this->payment('bank', self::UNSUPPORTED_METHOD_CLASS);
        $agentCard = $this->payment('card', self::SUPPORTED_METHOD_CLASS);
        $resolver = $this->resolver(
            handlers: [$this->handler(self::HANDLER_ID, self::SUPPORTED_METHOD_CLASS)],
            candidatePayments: [$bankTransfer, $agentCard],
        );
        $order = $this->orderWithSaleType();

        // handler_id 省略 (create の見積算出): エージェント決済可能な既定を選ぶ。
        $resolved = $resolver->resolve($order);

        $this->assertSame($agentCard, $resolved);
        $this->assertSame($agentCard, $order->getPayment());
    }

    public function testReturnsNullWhenOrderHasNoSaleType(): void
    {
        $resolver = $this->resolver(
            handlers: [$this->handler(self::HANDLER_ID, self::SUPPORTED_METHOD_CLASS)],
            candidatePayments: [$this->payment('card', self::SUPPORTED_METHOD_CLASS)],
        );

        // 販売種別が無い注文は候補を引けない → null。
        $resolved = $resolver->resolve(new Order());

        $this->assertNotInstanceOf(Payment::class, $resolved);
    }

    public function testThrowsWhenHandlerIdIsRegisteredByMultipleHandlers(): void
    {
        $resolver = $this->resolver(
            handlers: [
                $this->handler('dup', self::SUPPORTED_METHOD_CLASS),
                $this->handler('dup', self::SUPPORTED_METHOD_CLASS),
            ],
            candidatePayments: [],
        );

        // 同一 handler_id の重複登録は非決定的選択を避けるため例外。
        $this->expectException(\RuntimeException::class);
        $resolver->resolve($this->orderWithSaleType(), 'dup');
    }

    /**
     * 候補 Payment と in-memory ハンドラを束ねた resolver を作る.
     *
     * @param list<AgentCheckoutPaymentHandlerInterface> $handlers
     * @param list<Payment>                              $candidatePayments
     */
    private function resolver(array $handlers, array $candidatePayments): DefaultAgentPaymentMethodResolver
    {
        $deliveryRepository = $this->createMock(DeliveryRepository::class);
        $deliveryRepository->method('getDeliveries')->willReturn([new Delivery()]);
        $paymentRepository = $this->createMock(PaymentRepository::class);
        $paymentRepository->method('findAllowedPayments')->willReturn($candidatePayments);

        return new DefaultAgentPaymentMethodResolver(
            $paymentRepository,
            $deliveryRepository,
            new AgentCheckoutPaymentHandlerRegistry($handlers),
        );
    }

    /**
     * 指定の method_class を扱う in-memory 決済ハンドラ.
     *
     * methodClass はコアに焼かず、ハンドラの supports() 内だけで判定する
     * (将来 PSP が別 methodClass を出しても resolver は無改変)。
     */
    private function handler(string $handlerId, string $supportedMethodClass): AgentCheckoutPaymentHandlerInterface
    {
        return new InMemoryMethodClassPaymentHandler($handlerId, $supportedMethodClass);
    }

    private function payment(string $method, string $methodClass): Payment
    {
        return (new Payment())->setMethod($method)->setMethodClass($methodClass);
    }

    /**
     * 販売種別を 1 つ持つ注文 (getSaleTypes() が非空になる) を組み立てる.
     */
    private function orderWithSaleType(): Order
    {
        $ProductClass = new ProductClass();
        $ProductClass->setSaleType(new SaleType());
        $OrderItem = new OrderItem();
        $OrderItem->setProductClass($ProductClass);
        $Order = new Order();
        $Order->addOrderItem($OrderItem);

        return $Order;
    }
}

/**
 * supports() を method_class 一致だけで判定する in-memory 決済ハンドラ (テスト専用).
 */
final readonly class InMemoryMethodClassPaymentHandler implements AgentCheckoutPaymentHandlerInterface
{
    public function __construct(
        private string $handlerId,
        private string $supportedMethodClass,
    ) {
    }

    public function getHandlerId(): string
    {
        return $this->handlerId;
    }

    public function authorize(Order $order, array $paymentData): PaymentOutcome
    {
        return PaymentOutcome::completed();
    }

    public function capture(Order $order, array $paymentData): PaymentOutcome
    {
        return PaymentOutcome::completed();
    }

    public function supports(Order $order): bool
    {
        return $order->getPayment()?->getMethodClass() === $this->supportedMethodClass;
    }
}
