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

namespace Eccube\Tests\Service\AgentCommerce\Ucp\Signature;

use Eccube\Kernel;
use Eccube\Service\AgentCommerce\Exception\UcpSignatureException;
use Eccube\Service\AgentCommerce\Ucp\Signature\UcpProfileFetcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\NoPrivateNetworkHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Layer 4 (SSRF 対策) tests for UcpProfileFetcher.
 *
 * profile URL は UCP-Agent ヘッダ由来 (エージェント供給) のため、内部アドレスへの到達を
 * HTTP リクエスト前に遮断することを検証する。HTTPS 必須・公開アドレス限定。
 */
final class UcpProfileFetcherTest extends TestCase
{
    /**
     * @return MockHttpClient HTTP リクエストが発生したら失敗する (SSRF 遮断はリクエスト前)
     */
    private function failingClient(): MockHttpClient
    {
        return new MockHttpClient(static function (): MockResponse {
            throw new \LogicException('HTTP request must not be performed for a blocked host.');
        });
    }

    public function testRejectsNonHttps(): void
    {
        $fetcher = new UcpProfileFetcher($this->failingClient());

        $this->expectException(UcpSignatureException::class);
        $fetcher->fetchSigningKeys('http://agent.example/.well-known/ucp');
    }

    public function testRejectsLoopbackLiteral(): void
    {
        $fetcher = new UcpProfileFetcher($this->failingClient());

        $this->expectException(UcpSignatureException::class);
        $this->expectExceptionMessage('non-public address');
        $fetcher->fetchSigningKeys('https://127.0.0.1/.well-known/ucp');
    }

    public function testRejectsCloudMetadataLinkLocalLiteral(): void
    {
        $fetcher = new UcpProfileFetcher($this->failingClient());

        $this->expectException(UcpSignatureException::class);
        $this->expectExceptionMessage('non-public address');
        // クラウドメタデータエンドポイント (169.254.169.254) は link-local。
        $fetcher->fetchSigningKeys('https://169.254.169.254/.well-known/ucp');
    }

    public function testRejectsPrivateRangeResolution(): void
    {
        // ドメインが RFC1918 アドレスへ解決するケース (DNS リバインディング型攻撃)。
        $fetcher = new UcpProfileFetcher($this->failingClient(), static fn (string $host): array => ['10.0.0.5']);

        $this->expectException(UcpSignatureException::class);
        $this->expectExceptionMessage('non-public address');
        $fetcher->fetchSigningKeys('https://internal.attacker.example/.well-known/ucp');
    }

    public function testAllowsPublicHostAndReturnsKeys(): void
    {
        $profile = ['signing_keys' => [['kid' => 'k1', 'kty' => 'EC', 'crv' => 'P-256', 'x' => 'AA', 'y' => 'BB']]];
        $client = new MockHttpClient(new MockResponse((string) json_encode($profile), ['http_code' => 200]));
        $fetcher = new UcpProfileFetcher($client, static fn (string $host): array => ['93.184.216.34']);

        $keys = $fetcher->fetchSigningKeys('https://agent.example/.well-known/ucp');

        $this->assertCount(1, $keys);
        $this->assertSame('k1', $keys[0]['kid']);
    }

    public function testRejectsUnresolvableHost(): void
    {
        $fetcher = new UcpProfileFetcher($this->failingClient(), static fn (string $host): false => false);

        $this->expectException(UcpSignatureException::class);
        $this->expectExceptionMessage('could not be resolved');
        $fetcher->fetchSigningKeys('https://nx.example/.well-known/ucp');
    }

    /**
     * PHP の FILTER_FLAG_NO_PRIV_RANGE / NO_RES_RANGE が通す内部向け帯域も拒否する.
     *
     * @return iterable<string, array{string}>
     */
    public static function internalRangesNotCoveredByFilterVarProvider(): iterable
    {
        yield 'CGNAT (クラウドのメタデータ サービスがある環境)' => ['100.100.100.200'];
        yield 'ベンチマーク用 (RFC 2544)' => ['198.18.0.1'];
    }

    #[DataProvider(methodName: 'internalRangesNotCoveredByFilterVarProvider')]
    public function testRejectsInternalRangesNotCoveredByFilterVar(string $ip): void
    {
        $fetcher = new UcpProfileFetcher($this->failingClient(), static fn (string $host): array => [$ip]);

        $this->expectException(UcpSignatureException::class);
        $this->expectExceptionMessage('non-public address');
        $fetcher->fetchSigningKeys('https://agent.example/.well-known/ucp');
    }

    public function testRejectsOversizedProfile(): void
    {
        $profile = (string) json_encode([
            'signing_keys' => [['kid' => 'k1', 'kty' => 'EC', 'crv' => 'P-256', 'x' => 'AA', 'y' => 'BB']],
            'padding' => str_repeat('x', UcpProfileFetcher::MAX_PROFILE_BYTES),
        ]);
        $client = new MockHttpClient(new MockResponse($profile, ['http_code' => 200]));
        $fetcher = new UcpProfileFetcher($client, static fn (string $host): array => ['93.184.216.34']);

        $this->expectException(UcpSignatureException::class);
        $fetcher->fetchSigningKeys('https://agent.example/.well-known/ucp');
    }

    public function testCachesSigningKeysPerProfileUrl(): void
    {
        $profile = ['signing_keys' => [['kid' => 'k1', 'kty' => 'EC', 'crv' => 'P-256', 'x' => 'AA', 'y' => 'BB']]];
        $requests = 0;
        $client = new MockHttpClient(static function () use (&$requests, $profile): MockResponse {
            ++$requests;

            return new MockResponse((string) json_encode($profile), ['http_code' => 200]);
        });
        $fetcher = new UcpProfileFetcher($client, static fn (string $host): array => ['93.184.216.34'], new ArrayAdapter());

        $fetcher->fetchSigningKeys('https://agent.example/.well-known/ucp');
        $keys = $fetcher->fetchSigningKeys('https://agent.example/.well-known/ucp');

        $this->assertSame('k1', $keys[0]['kid']);
        $this->assertSame(1, $requests, '同じ profile への 2 回目の検証では取得しない');
    }

    /**
     * 本番の DI では、接続時点で宛先を検査する NoPrivateNetworkHttpClient を通して取得する.
     */
    public function testContainerWrapsProfileHttpClientWithPrivateNetworkGuard(): void
    {
        $kernel = new Kernel('test', false);
        $kernel->boot();
        try {
            $client = $kernel->getContainer()->get('test.service_container')->get('eccube.agent_commerce.ucp_profile_http_client');
            $this->assertInstanceOf(NoPrivateNetworkHttpClient::class, $client);
        } finally {
            $kernel->shutdown();
        }
    }
}
