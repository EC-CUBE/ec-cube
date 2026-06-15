<?php

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

namespace Eccube\Service\AgentCommerce\Ucp\Signature;

use Eccube\Service\AgentCommerce\Exception\UcpSignatureException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * エージェント (プラットフォーム) の UCP プロファイルを取得し、署名検証用の公開鍵 JWK を抽出する.
 *
 * `UCP-Agent` ヘッダの profile URL から `/.well-known/ucp` 相当のプロファイル文書を取得する。
 * - **HTTPS 必須・リダイレクト追従禁止** (RFC 9421 / UCP signatures.md の鍵探索要件)。
 * - 取得した文書の `signing_keys[]` (EC 公開鍵 JWK) を返す。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP signatures.md (key discovery: profile.signing_keys[], no redirects)
 */
class UcpProfileFetcher
{
    /**
     * @var \Closure(string): (list<string>|false) ホスト名 -> 解決 IP 群を返すリゾルバ
     */
    private readonly \Closure $hostResolver;

    /**
     * @param HttpClientInterface          $httpClient   profile 取得クライアント
     * @param (callable(string): (list<string>|false))|null $hostResolver A レコード解決器 (既定は gethostbynamel、テストで差し替え可)
     */
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        ?callable $hostResolver = null,
    ) {
        $this->hostResolver = $hostResolver !== null
            ? \Closure::fromCallable($hostResolver)
            : gethostbynamel(...);
    }

    /**
     * profile URL から signing_keys[] (JWK 配列) を取得する.
     *
     * @return list<array<string, mixed>> 公開鍵 JWK の配列
     *
     * @throws UcpSignatureException 取得失敗 / 文書不正 / 鍵が無い場合
     */
    public function fetchSigningKeys(string $profileUrl): array
    {
        if (parse_url($profileUrl, PHP_URL_SCHEME) !== 'https') {
            throw new UcpSignatureException('Agent profile URL must be HTTPS.');
        }

        // SSRF 対策: profile URL は UCP-Agent ヘッダ由来 (エージェント供給) のため、
        // 内部アドレスへの到達を HTTP リクエスト前に遮断する。許可ドメインリスト
        // (UcpRequestSignatureVerifier 側) と併用する多層防御。
        $host = parse_url($profileUrl, PHP_URL_HOST);
        if (!is_string($host) || $host === '') {
            throw new UcpSignatureException('Agent profile URL has no host.');
        }
        $this->assertPublicHost($host);

        try {
            $response = $this->httpClient->request('GET', $profileUrl, [
                // 鍵探索ではリダイレクトを追従しない (なりすまし防止)。
                'max_redirects' => 0,
                'headers' => ['Accept' => 'application/json'],
            ]);

            if ($response->getStatusCode() !== 200) {
                // 上流ステータスコードはそのまま返さない (内部探索のオラクルを避ける)。
                throw new UcpSignatureException('Agent profile fetch did not return a successful response.');
            }

            /** @var array<string, mixed> $document */
            $document = $response->toArray(false);
        } catch (UcpSignatureException $e) {
            throw $e;
        } catch (HttpClientExceptionInterface|\JsonException $e) {
            throw new UcpSignatureException('Failed to fetch or parse the agent profile.', 0, $e);
        }

        return $this->extractSigningKeys($document);
    }

    /**
     * ホストが公開アドレスへ解決されることを確認する (loopback/RFC1918/link-local/reserved を拒否).
     *
     * SSRF 対策の中核。IP リテラルはそのまま、ドメインは A レコード解決後の全 IP を検査する。
     * 注: 解決と HttpClient の名前解決の間の DNS rebinding は残存リスクであり、厳格な運用では
     * UcpRequestSignatureVerifier のドメイン許可リスト併用を推奨する。IPv6 のみのホストは
     * gethostbynamel() が解決できず fail-closed (拒否) となる。
     */
    private function assertPublicHost(string $host): void
    {
        // IP リテラル (v4/v6) はそのまま、ホスト名は A レコードを解決する。
        $ips = filter_var($host, FILTER_VALIDATE_IP) !== false ? [$host] : (($this->hostResolver)($host) ?: []);
        if ($ips === []) {
            throw new UcpSignatureException('Agent profile host could not be resolved to a public address.');
        }

        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                throw new UcpSignatureException('Agent profile host resolves to a non-public address.');
            }
        }
    }

    /**
     * プロファイル文書から signing_keys[] を抽出する.
     *
     * @param array<string, mixed> $document
     *
     * @return list<array<string, mixed>>
     */
    private function extractSigningKeys(array $document): array
    {
        // signing_keys はラッパー直下、または ucp オブジェクト配下のいずれもあり得るため両対応。
        $signingKeys = $document['signing_keys'] ?? null;
        if (!is_array($signingKeys) && isset($document['ucp']) && is_array($document['ucp'])) {
            $signingKeys = $document['ucp']['signing_keys'] ?? null;
        }

        if (!is_array($signingKeys) || $signingKeys === []) {
            throw new UcpSignatureException('Agent profile does not advertise any signing_keys.');
        }

        $keys = [];
        foreach ($signingKeys as $key) {
            if (is_array($key) && isset($key['kty']) && $key['kty'] === 'EC') {
                /** @var array<string, mixed> $key */
                $keys[] = $key;
            }
        }

        if ($keys === []) {
            throw new UcpSignatureException('Agent profile has no EC public keys in signing_keys.');
        }

        return $keys;
    }
}
