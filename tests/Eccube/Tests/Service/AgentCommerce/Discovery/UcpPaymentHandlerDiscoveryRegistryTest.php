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

namespace Eccube\Tests\Service\AgentCommerce\Discovery;

use Eccube\Entity\Order;
use Eccube\Service\AgentCommerce\Discovery\UcpPaymentHandlerDiscoveryRegistry;
use Eccube\Service\AgentCommerce\Discovery\UcpProfileBuilder;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerInterface;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerRegistry;
use Eccube\Service\AgentCommerce\Payment\PaymentOutcome;
use Eccube\Service\AgentCommerce\Payment\UcpPaymentHandlerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Layer 1 (pure logic) tests for UcpPaymentHandlerDiscoveryRegistry.
 *
 * 登録済み UCP 決済ハンドラから UCP discovery profile の payment_handlers が
 * 自動生成されることを純ロジックで検証する。reverse-domain キー・値は配列・
 * 各エントリ id/version 必須 (UCP profile schema 準拠)・UCP 以外のハンドラは除外・
 * 寄与が無ければ空 ([]= 空オブジェクト {}) を確認する。
 */
final class UcpPaymentHandlerDiscoveryRegistryTest extends TestCase
{
    public function testCollectsUcpHandlersAsReverseDomainRegistry(): void
    {
        $registry = $this->discoveryRegistry([
            $this->ucpHandler('dev.ucp.payment.card'),
            $this->ucpHandler('com.example.wallet'),
        ]);

        $collected = $registry->collect();

        // reverse-domain キーのレジストリ。値はハンドラオブジェクトの配列。
        $this->assertSame(['dev.ucp.payment.card', 'com.example.wallet'], array_keys($collected));
        $this->assertSame([
            ['id' => 'dev.ucp.payment.card', 'version' => UcpProfileBuilder::UCP_VERSION],
        ], $collected['dev.ucp.payment.card']);
    }

    public function testEntryHasRequiredIdAndVersion(): void
    {
        $registry = $this->discoveryRegistry([$this->ucpHandler('dev.ucp.payment.card')]);

        $entry = $registry->collect()['dev.ucp.payment.card'][0];

        // payment_handler は id 必須 (payment_handler schema) / version 必須 (entity schema)。
        $this->assertArrayHasKey('id', $entry, 'payment_handler entry requires "id"');
        $this->assertArrayHasKey('version', $entry, 'payment_handler entry requires "version"');
        $this->assertSame('dev.ucp.payment.card', $entry['id']);
        $this->assertSame(UcpProfileBuilder::UCP_VERSION, $entry['version']);
    }

    public function testExcludesNonUcpHandlers(): void
    {
        // 非 UCP (base のみ) のハンドラは discovery の payment_handlers に含めない。
        $registry = $this->discoveryRegistry([$this->nonUcpHandler('legacy_card')]);

        $this->assertSame([], $registry->collect());
    }

    public function testReturnsEmptyWhenNoHandlersRegistered(): void
    {
        $registry = $this->discoveryRegistry([]);

        $this->assertSame([], $registry->collect());
    }

    /**
     * @param list<AgentCheckoutPaymentHandlerInterface> $handlers
     */
    private function discoveryRegistry(array $handlers): UcpPaymentHandlerDiscoveryRegistry
    {
        return new UcpPaymentHandlerDiscoveryRegistry(new AgentCheckoutPaymentHandlerRegistry($handlers));
    }

    private function ucpHandler(string $handlerId): UcpPaymentHandlerInterface
    {
        return new InMemoryUcpDiscoveryHandler($handlerId);
    }

    private function nonUcpHandler(string $handlerId): AgentCheckoutPaymentHandlerInterface
    {
        return new InMemoryBasePaymentHandler($handlerId);
    }
}

/**
 * discovery テスト用の in-memory UCP 決済ハンドラ.
 */
final readonly class InMemoryUcpDiscoveryHandler implements UcpPaymentHandlerInterface
{
    public function __construct(private string $handlerId)
    {
    }

    public function getHandlerId(): string
    {
        return $this->handlerId;
    }

    public function exchangePaymentToken(array $credential): array
    {
        return [];
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
        return true;
    }
}

/**
 * discovery テスト用の in-memory 非 UCP (base のみ) 決済ハンドラ.
 */
final readonly class InMemoryBasePaymentHandler implements AgentCheckoutPaymentHandlerInterface
{
    public function __construct(private string $handlerId)
    {
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
        return true;
    }
}
