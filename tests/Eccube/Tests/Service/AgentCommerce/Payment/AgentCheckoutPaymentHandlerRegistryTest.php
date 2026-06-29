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

use Eccube\Entity\Order;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerInterface;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerRegistry;
use Eccube\Service\AgentCommerce\Payment\PaymentOutcome;
use Eccube\Service\AgentCommerce\Payment\UcpPaymentHandlerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (純ロジック) tests for AgentCheckoutPaymentHandlerRegistry.
 *
 * タグ付きハンドラ群から handler_id / supports による解決を検証する。
 * 具象ハンドラは決済プラグインが寄与するため、テストでは in-memory スタブを用いる。
 */
final class AgentCheckoutPaymentHandlerRegistryTest extends TestCase
{
    public function testResolvesUcpHandlerByHandlerId(): void
    {
        $card = new InMemoryUcpPaymentHandler('dev.ucp.payment.card');
        $bank = new InMemoryUcpPaymentHandler('dev.ucp.payment.bank');
        $registry = new AgentCheckoutPaymentHandlerRegistry([$card, $bank]);

        $this->assertSame($card, $registry->resolveUcpByHandlerId('dev.ucp.payment.card'));
        $this->assertSame($bank, $registry->resolveUcpByHandlerId('dev.ucp.payment.bank'));
        $this->assertNotInstanceOf(UcpPaymentHandlerInterface::class, $registry->resolveUcpByHandlerId('dev.ucp.payment.unknown'));
    }

    public function testUcpHandlersFiltersOnlyUcp(): void
    {
        $ucp = new InMemoryUcpPaymentHandler('dev.ucp.payment.card');
        // 非 UCP ハンドラを混在させ、ucpHandlers() が UCP のみを返す (フィルタの本質) ことを保証する。
        $nonUcp = new InMemoryNonUcpPaymentHandler();
        $registry = new AgentCheckoutPaymentHandlerRegistry([$nonUcp, $ucp]);

        $this->assertSame([$ucp], $registry->ucpHandlers());
    }

    public function testResolveUcpByHandlerIdThrowsOnDuplicate(): void
    {
        $a = new InMemoryUcpPaymentHandler('dev.ucp.payment.card');
        $b = new InMemoryUcpPaymentHandler('dev.ucp.payment.card');
        $registry = new AgentCheckoutPaymentHandlerRegistry([$a, $b]);

        $this->expectException(\RuntimeException::class);
        $registry->resolveUcpByHandlerId('dev.ucp.payment.card');
    }

    public function testResolveForOrderUsesSupports(): void
    {
        $supporting = new InMemoryUcpPaymentHandler('dev.ucp.payment.card', supports: true);
        $registry = new AgentCheckoutPaymentHandlerRegistry([$supporting]);

        $this->assertSame($supporting, $registry->resolveForOrder(new Order()));
    }

    public function testResolveForOrderReturnsNullWhenNoneSupport(): void
    {
        $registry = new AgentCheckoutPaymentHandlerRegistry([new InMemoryUcpPaymentHandler('x', supports: false)]);

        $this->assertNotInstanceOf(AgentCheckoutPaymentHandlerInterface::class, $registry->resolveForOrder(new Order()));
    }
}

/**
 * テスト用の UCP 決済ハンドラスタブ (本番の具象は決済プラグインが提供する).
 */
final readonly class InMemoryUcpPaymentHandler implements UcpPaymentHandlerInterface, AgentCheckoutPaymentHandlerInterface
{
    public function __construct(
        private string $handlerId,
        private bool $supports = false,
    ) {
    }

    public function getHandlerId(): string
    {
        return $this->handlerId;
    }

    public function exchangePaymentToken(array $credential): array
    {
        return ['gateway_token' => 'tok_test', 'source' => $credential];
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
        return $this->supports;
    }
}

/**
 * UCP ではない決済ハンドラのスタブ (ucpHandlers のフィルタ検証用).
 */
final readonly class InMemoryNonUcpPaymentHandler implements AgentCheckoutPaymentHandlerInterface
{
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
        return false;
    }

    public function getHandlerId(): string
    {
        return 'non_ucp_handler';
    }
}
