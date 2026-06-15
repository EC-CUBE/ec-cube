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

use Eccube\Service\AgentCommerce\Exception\UcpSignatureException;
use Eccube\Service\AgentCommerce\Ucp\Signature\Rfc9421SignatureBaseBuilder;
use Eccube\Service\AgentCommerce\Ucp\Signature\UcpProfileFetcher;
use Eccube\Service\AgentCommerce\Ucp\Signature\UcpRequestSignatureVerifier;
use phpseclib3\Crypt\EC;
use phpseclib3\Crypt\EC\PrivateKey;
use phpseclib3\Crypt\EC\PublicKey;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Layer 4 (認証/署名) tests for UcpRequestSignatureVerifier.
 *
 * RFC 9421 / ES256 のインバウンド署名検証を、実 EC 鍵で署名したリクエストとモック profile を用いて
 * 検証する。正常系のほか、改竄署名・別鍵・Content-Digest 不一致・許可外ドメインを reject することを確認する。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP signatures.md (RFC 9421, ES256, Content-Digest)
 * @see https://www.rfc-editor.org/rfc/rfc9421
 */
final class UcpRequestSignatureVerifierTest extends TestCase
{
    private const KID = 'agent-key-1';

    private const PROFILE_URL = 'https://agent.example/.well-known/ucp';

    private PrivateKey $privateKey;

    private Rfc9421SignatureBaseBuilder $baseBuilder;

    protected function setUp(): void
    {
        /** @var PrivateKey $key */
        $key = EC::createKey('secp256r1');
        $this->privateKey = $key;
        $this->baseBuilder = new Rfc9421SignatureBaseBuilder();
    }

    public function testVerifiesValidSignature(): void
    {
        $request = $this->signedRequest($this->privateKey->getPublicKey());
        $verifier = $this->verifierWithKey($this->privateKey->getPublicKey());

        $agent = $verifier->verify($request);

        $this->assertSame(self::PROFILE_URL, $agent->profileUrl, '正しい署名は検証を通過しエージェント情報を返す');
    }

    public function testRejectsTamperedSignature(): void
    {
        $request = $this->signedRequest($this->privateKey->getPublicKey(), tamperSignature: true);
        $verifier = $this->verifierWithKey($this->privateKey->getPublicKey());

        $this->expectException(UcpSignatureException::class);
        $verifier->verify($request);
    }

    public function testRejectsWrongPublicKey(): void
    {
        /** @var PrivateKey $other */
        $other = EC::createKey('secp256r1');
        $request = $this->signedRequest($this->privateKey->getPublicKey());
        // profile は別人の公開鍵を広告する。
        $verifier = $this->verifierWithKey($other->getPublicKey());

        $this->expectException(UcpSignatureException::class);
        $verifier->verify($request);
    }

    public function testRejectsContentDigestMismatch(): void
    {
        $request = $this->signedRequest($this->privateKey->getPublicKey());
        // ボディを差し替えて Content-Digest を不整合にする。
        $request->headers->set('Content-Digest', 'sha-256=:'.base64_encode(hash('sha256', 'tampered', true)).':');
        $verifier = $this->verifierWithKey($this->privateKey->getPublicKey());

        $this->expectException(UcpSignatureException::class);
        $this->expectExceptionMessage('Content-Digest');
        $verifier->verify($request);
    }

    public function testRejectsDisallowedDomain(): void
    {
        $request = $this->signedRequest($this->privateKey->getPublicKey());
        $verifier = $this->verifierWithKey($this->privateKey->getPublicKey(), ['trusted.example']);

        $this->expectException(UcpSignatureException::class);
        $this->expectExceptionMessage('not allowed');
        $verifier->verify($request);
    }

    public function testRejectsMissingUcpAgentHeader(): void
    {
        $request = Request::create('https://shop.example/ucp/checkout-sessions', 'POST', [], [], [], [], '{}');
        $verifier = $this->verifierWithKey($this->privateKey->getPublicKey());

        $this->expectException(UcpSignatureException::class);
        $this->expectExceptionMessage('UCP-Agent');
        $verifier->verify($request);
    }

    /**
     * 指定公開鍵を signing_keys[] に広告する profile を返す verifier を組み立てる.
     *
     * @param list<string> $allowedDomains
     */
    private function verifierWithKey(PublicKey $advertisedKey, array $allowedDomains = []): UcpRequestSignatureVerifier
    {
        $profile = ['signing_keys' => [$this->toJwk($advertisedKey, self::KID)]];
        $httpClient = new MockHttpClient(new MockResponse((string) json_encode($profile), ['http_code' => 200]));
        $fetcher = new UcpProfileFetcher($httpClient);

        return new UcpRequestSignatureVerifier($fetcher, $this->baseBuilder, $allowedDomains);
    }

    /**
     * 実 EC 秘密鍵で署名した POST リクエストを生成する.
     */
    private function signedRequest(PublicKey $publicKey, bool $tamperSignature = false): Request
    {
        $body = '{"line_items":[{"item":{"id":"1"},"quantity":1}]}';
        $request = Request::create('https://shop.example/ucp/checkout-sessions', 'POST', [], [], [], [], $body);
        $request->headers->set('UCP-Agent', sprintf('profile="%s"', self::PROFILE_URL));
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('Content-Digest', 'sha-256=:'.base64_encode(hash('sha256', $body, true)).':');

        $components = ['@method', '@authority', '@path', 'ucp-agent', 'content-digest', 'content-type'];
        $params = sprintf('(%s);keyid="%s";alg="ecdsa-p256-sha256"', implode(' ', array_map(static fn (string $c): string => '"'.$c.'"', $components)), self::KID);

        $base = $this->baseBuilder->build($request, $components, $params);
        $raw = $this->privateKey->withSignatureFormat('IEEE')->withHash('sha256')->sign($base);
        if ($tamperSignature) {
            $raw[0] = $raw[0] === "\x00" ? "\x01" : "\x00";
        }

        $request->headers->set('Signature-Input', 'sig1='.$params);
        $request->headers->set('Signature', 'sig1=:'.base64_encode($raw).':');

        return $request;
    }

    /**
     * 公開鍵を kid 付き EC JWK へ変換する.
     *
     * @return array<string, mixed>
     */
    private function toJwk(PublicKey $publicKey, string $kid): array
    {
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($publicKey->toString('JWK'), true);
        if (isset($decoded['keys'][0]) && is_array($decoded['keys'][0])) {
            $decoded = $decoded['keys'][0];
        }

        return [
            'kid' => $kid,
            'kty' => 'EC',
            'crv' => $decoded['crv'] ?? 'P-256',
            'x' => $decoded['x'],
            'y' => $decoded['y'],
        ];
    }
}
