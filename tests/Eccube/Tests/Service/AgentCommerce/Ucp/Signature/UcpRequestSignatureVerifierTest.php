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
use phpseclib4\Crypt\EC;
use phpseclib4\Crypt\EC\PrivateKey;
use phpseclib4\Crypt\EC\PublicKey;
use PHPUnit\Framework\Attributes\DataProvider;
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
        $request = Request::create('https://shop.example/ucp/checkout-sessions', Request::METHOD_POST, [], [], [], [], '{}');
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
    private function verifierWithKey(PublicKey $advertisedKey, array $allowedDomains = [], string $kid = self::KID): UcpRequestSignatureVerifier
    {
        $profile = ['signing_keys' => [$this->toJwk($advertisedKey, $kid)]];
        $httpClient = new MockHttpClient(new MockResponse((string) json_encode($profile), ['http_code' => 200]));
        // テストの profile ホスト (agent.example) は実解決できないため、公開 IP へ解決するスタブを注入する。
        $fetcher = new UcpProfileFetcher($httpClient, static fn (string $host): array => ['93.184.216.34']);

        return new UcpRequestSignatureVerifier($fetcher, $this->baseBuilder, $allowedDomains);
    }

    /**
     * 署名者が covered component を絞った署名は、正規の鍵で署名されていても拒否する.
     *
     * Content-Digest や Idempotency-Key を署名対象から外すと、署名を流用したまま本文や冪等キーを差し替えられる。
     */
    #[DataProvider(methodName: 'incompleteComponentsProvider')]
    public function testRejectsSignatureMissingRequiredComponents(array $components, string $missing): void
    {
        $request = $this->signedRequest($this->privateKey->getPublicKey(), components: $components);
        $verifier = $this->verifierWithKey($this->privateKey->getPublicKey());

        $this->expectException(UcpSignatureException::class);
        $this->expectExceptionMessage($missing);
        $verifier->verify($request);
    }

    /**
     * @return iterable<string, array{list<string>, string}>
     */
    public static function incompleteComponentsProvider(): iterable
    {
        yield '@method のみ' => [['@method'], '@authority'];
        yield 'content-digest が無い' => [['@method', '@authority', '@path', 'ucp-agent', 'idempotency-key', 'content-type'], 'content-digest'];
        yield 'idempotency-key が無い' => [['@method', '@authority', '@path', 'ucp-agent', 'content-digest', 'content-type'], 'idempotency-key'];
        yield 'ucp-agent が無い' => [['@method', '@authority', '@path', 'idempotency-key', 'content-digest', 'content-type'], 'ucp-agent'];
    }

    public function testRejectsExpiredSignature(): void
    {
        $request = $this->signedRequest($this->privateKey->getPublicKey(), extraParams: ';expires='.(time() - 60));
        $verifier = $this->verifierWithKey($this->privateKey->getPublicKey());

        $this->expectException(UcpSignatureException::class);
        $this->expectExceptionMessage('expired');
        $verifier->verify($request);
    }

    /**
     * 引用文字列 (keyid) の中の expires を期限として扱わない (本来の expires が期限切れなら拒否する).
     */
    public function testExpiresInsideQuotedKeyIdIsIgnored(): void
    {
        $request = $this->signedRequest(
            $this->privateKey->getPublicKey(),
            extraParams: sprintf(';expires=%d', time() - 60),
            keyId: sprintf('k;expires=%d', time() + 3600),
        );
        $verifier = $this->verifierWithKey($this->privateKey->getPublicKey(), kid: sprintf('k;expires=%d', time() + 3600));

        $this->expectException(UcpSignatureException::class);
        $this->expectExceptionMessage('expired');
        $verifier->verify($request);
    }

    /**
     * 署名パラメータの解析 (引用文字列の除去) に失敗したら、期限の検査を飛ばさず署名を拒否する.
     */
    public function testRejectsWhenSignatureParamsCannotBeParsed(): void
    {
        $verifier = $this->verifierWithKey($this->privateKey->getPublicKey());
        // 期限切れの expires を付けた signature-params。引用文字列の除去に失敗しても期限の検査を飛ばさないこと。
        $params = sprintf('("@method");keyid="%s";tag="%s";expires=%d', self::KID, str_repeat('a', 1000), time() - 60);
        $assertTimestamps = (new \ReflectionMethod($verifier, 'assertSignatureTimestamps'))->getClosure($verifier);

        // PCRE の上限を下げ、引用文字列を取り除く置換を失敗させる。
        $backtrackLimit = ini_get('pcre.backtrack_limit');
        $jit = ini_get('pcre.jit');
        ini_set('pcre.jit', '0');
        ini_set('pcre.backtrack_limit', '10');
        try {
            $this->expectException(UcpSignatureException::class);
            $this->expectExceptionMessage('could not be parsed');
            $assertTimestamps($params);
        } finally {
            ini_set('pcre.backtrack_limit', (string) $backtrackLimit);
            ini_set('pcre.jit', (string) $jit);
        }
    }

    /**
     * @query は受信したクエリの順序のまま signature base に入れる (並べ替えると署名者の base と一致しない).
     */
    public function testQueryComponentKeepsReceivedOrder(): void
    {
        $request = Request::create('https://shop.example/ucp/checkout-sessions/cs_1?b=2&a=1', Request::METHOD_GET);
        $request->server->set('QUERY_STRING', 'b=2&a=1');

        $base = $this->baseBuilder->build($request, ['@query'], '("@query")');

        $this->assertStringStartsWith('"@query": ?b=2&a=1'."\n", $base);
    }

    public function testAcceptsSignatureWithinValidityWindow(): void
    {
        $now = time();
        $request = $this->signedRequest($this->privateKey->getPublicKey(), extraParams: sprintf(';created=%d;expires=%d', $now, $now + 300));
        $verifier = $this->verifierWithKey($this->privateKey->getPublicKey());

        $this->assertSame(self::PROFILE_URL, $verifier->verify($request)->profileUrl);
    }

    /**
     * 環境変数 (csv) の空値は「許可リスト無し」として扱う (空要素で全ドメインを拒否しない).
     */
    public function testEmptyAllowedDomainEntriesAreIgnored(): void
    {
        $request = $this->signedRequest($this->privateKey->getPublicKey());
        $verifier = $this->verifierWithKey($this->privateKey->getPublicKey(), ['', ' ']);

        $this->assertSame(self::PROFILE_URL, $verifier->verify($request)->profileUrl);
    }

    /**
     * 実 EC 秘密鍵で署名した POST リクエストを生成する.
     *
     * @param list<string>|null $components 署名対象 (null は UCP signatures.md の必須一式)
     */
    private function signedRequest(PublicKey $publicKey, bool $tamperSignature = false, ?array $components = null, string $extraParams = '', string $keyId = self::KID): Request
    {
        $body = '{"line_items":[{"item":{"id":"1"},"quantity":1}]}';
        $request = Request::create('https://shop.example/ucp/checkout-sessions', Request::METHOD_POST, [], [], [], [], $body);
        $request->headers->set('UCP-Agent', sprintf('profile="%s"', self::PROFILE_URL));
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('Idempotency-Key', '550e8400-e29b-41d4-a716-446655440000');
        $request->headers->set('Content-Digest', 'sha-256=:'.base64_encode(hash('sha256', $body, true)).':');

        $components ??= ['@method', '@authority', '@path', 'ucp-agent', 'idempotency-key', 'content-digest', 'content-type'];
        $params = sprintf('(%s);keyid="%s";alg="ecdsa-p256-sha256"%s', implode(' ', array_map(static fn (string $c): string => '"'.$c.'"', $components)), $keyId, $extraParams);

        $base = $this->baseBuilder->build($request, $components, $params);
        $raw = $this->privateKey->withSignatureFormat('IEEE')->withHash('sha256')->sign($base);
        if ($tamperSignature) {
            $raw[0] = $raw[0] === "\x00" ? "\x01" : "\x00";
        }

        $request->headers->set('Signature-Input', 'sig1='.$params);
        $request->headers->set('Signature', 'sig1=:'.base64_encode((string) $raw).':');

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
