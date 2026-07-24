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

namespace Eccube\Tests\Service\AgentCommerce\Acp;

use Eccube\Service\AgentCommerce\Acp\AcpMessageSigner;
use Eccube\Service\AgentCommerce\Security\KeyStoreInterface;
use PHPUnit\Framework\TestCase;

/**
 * Layer 0 / Layer 5: ACP Webhook の Merchant-Signature (HMAC-SHA256) 生成・検証.
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP openapi.agentic_checkout_webhook.yaml (Merchant-Signature)
 */
final class AcpMessageSignerTest extends TestCase
{
    private AcpMessageSigner $signer;

    protected function setUp(): void
    {
        $keyStore = new class implements KeyStoreInterface {
            private ?string $value = 'test-shared-secret-0123456789abcdef';

            public function read(string $purpose): ?string
            {
                return $this->value;
            }

            public function write(string $purpose, string $pem): void
            {
                $this->value = $pem;
            }
        };

        $this->signer = new AcpMessageSigner($keyStore);
    }

    public function testSignProducesTimestampAndHexSignature(): void
    {
        $signature = $this->signer->sign('{"type":"order_create"}', 1750000000);

        // MUST: ヘッダ形式は t=<unix>,v1=<hex>。
        $this->assertMatchesRegularExpression('/\At=1750000000,v1=[0-9a-f]{64}\z/', $signature, 'Merchant-Signature は t=<unix>,v1=<64桁hex>');
    }

    public function testVerifyRoundTrip(): void
    {
        $body = '{"type":"order_update","data":{}}';
        $timestamp = 1750000000;
        $signature = $this->signer->sign($body, $timestamp);

        $this->assertTrue($this->signer->verify($body, $signature, 300, $timestamp), '同一 body/secret の署名は検証成功');
    }

    public function testVerifyRejectsTamperedBody(): void
    {
        $signature = $this->signer->sign('{"amount":100}', 1750000000);

        $this->assertFalse($this->signer->verify('{"amount":999}', $signature, 300, 1750000000), 'body 改竄は検証失敗');
    }

    public function testVerifyRejectsExpiredTimestamp(): void
    {
        $signature = $this->signer->sign('{}', 1750000000);

        // now が timestamp から 301 秒乖離 → 許容窓 (300秒) 超過で reject (MUST: timestamp 検証)。
        $this->assertFalse($this->signer->verify('{}', $signature, 300, 1750000301), 'タイムスタンプ超過は検証失敗');
    }

    public function testVerifyRejectsMalformedHeader(): void
    {
        $this->assertFalse($this->signer->verify('{}', 'garbage', 300, 1750000000), '不正フォーマットは検証失敗');
        $this->assertFalse($this->signer->verify('{}', 'v1=abcd', 300, 1750000000), 't 欠落は検証失敗');
    }
}
