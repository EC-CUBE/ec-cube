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
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\IpUtils;
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
    /** profile 応答の最大バイト数. 署名鍵の配布には十分で、巨大な応答でメモリを消費させない. */
    public const MAX_PROFILE_BYTES = 65536;

    /** 取得した signing_keys をキャッシュする秒数. 鍵のローテーションはこの時間だけ遅れて反映される. */
    public const CACHE_TTL_SECONDS = 300;

    /**
     * @var \Closure(string): (list<string>|false) ホスト名 -> 解決 IP 群を返すリゾルバ
     */
    private readonly \Closure $hostResolver;

    /**
     * 本番の $httpClient は DI で `NoPrivateNetworkHttpClient` に包んで渡す (services.yaml)。
     * 接続時点の宛先 IP を検査するため、本クラスの名前解決と HTTP クライアントの名前解決の間で
     * 応答を変える DNS rebinding も遮断できる。本クラスの事前検査は、その手前で弾く多層防御。
     *
     * @param HttpClientInterface          $httpClient   profile 取得クライアント
     * @param (callable(string): (list<string>|false))|null $hostResolver A レコード解決器 (既定は gethostbynamel、テストで差し替え可)
     * @param CacheItemPoolInterface|null  $cache        signing_keys のキャッシュ (null ならキャッシュしない)
     */
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        ?callable $hostResolver = null,
        private readonly ?CacheItemPoolInterface $cache = null,
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
        // 内部アドレスへの到達を HTTP リクエスト前に遮断する。接続時点の検査
        // (NoPrivateNetworkHttpClient) と許可ドメインリスト (UcpRequestSignatureVerifier 側) を併用する多層防御。
        $host = parse_url($profileUrl, PHP_URL_HOST);
        if (!is_string($host) || $host === '') {
            throw new UcpSignatureException('Agent profile URL has no host.');
        }
        $this->assertPublicHost($host);

        $cache = $this->cache;
        if ($cache === null) {
            return $this->fetchFromProfile($profileUrl);
        }

        $cacheItem = $cache->getItem('ucp_profile_signing_keys_'.hash('sha256', $profileUrl));
        if ($cacheItem->isHit()) {
            $cached = $cacheItem->get();
            if (is_array($cached) && $cached !== []) {
                /** @var list<array<string, mixed>> $cached */
                return $cached;
            }
        }

        $keys = $this->fetchFromProfile($profileUrl);

        // 取得に成功した鍵だけをキャッシュする (失敗はキャッシュせず、次のリクエストで取り直す)。
        $cacheItem->set($keys);
        $cacheItem->expiresAfter(self::CACHE_TTL_SECONDS);
        $cache->save($cacheItem);

        return $keys;
    }

    /**
     * @return list<array<string, mixed>>
     *
     * @throws UcpSignatureException
     */
    private function fetchFromProfile(string $profileUrl): array
    {
        try {
            $response = $this->httpClient->request('GET', $profileUrl, [
                // 鍵探索ではリダイレクトを追従しない (なりすまし防止)。
                'max_redirects' => 0,
                'headers' => ['Accept' => 'application/json'],
                // 応答が無いエージェント profile で署名検証がハングしないよう上限を設ける
                // (timeout=接続/アイドル, max_duration=総時間)。
                'timeout' => 5,
                'max_duration' => 10,
                // 巨大な応答でメモリを消費させないよう、上限を超えた時点で受信を打ち切る。
                'on_progress' => static function (int $downloaded, int $total): void {
                    if ($downloaded > self::MAX_PROFILE_BYTES || $total > self::MAX_PROFILE_BYTES) {
                        throw new UcpSignatureException('Agent profile is too large.');
                    }
                },
            ]);

            if ($response->getStatusCode() !== 200) {
                // 上流ステータスコードはそのまま返さない (内部探索のオラクルを避ける)。
                throw new UcpSignatureException('Agent profile fetch did not return a successful response.');
            }

            $content = $response->getContent(false);
            if (strlen($content) > self::MAX_PROFILE_BYTES) {
                throw new UcpSignatureException('Agent profile is too large.');
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
     * IP リテラルはそのまま、ドメインは A レコード解決後の全 IP を検査する。PHP のフィルタが通す
     * CGNAT (100.64.0.0/10) やベンチマーク用 (198.18.0.0/15) 等も {@link IpUtils::PRIVATE_SUBNETS} で拒否する
     * (クラウドのメタデータ サービスがこの帯域にある環境がある)。接続時点の検査 (DNS rebinding 対策) は
     * DI で包む `NoPrivateNetworkHttpClient` が担う。IPv6 のみのホストは gethostbynamel() が解決できず
     * fail-closed (拒否) となる。
     */
    private function assertPublicHost(string $host): void
    {
        // IP リテラル (v4/v6) はそのまま、ホスト名は A レコードを解決する。
        $ips = filter_var($host, FILTER_VALIDATE_IP) !== false ? [$host] : (($this->hostResolver)($host) ?: []);
        if ($ips === []) {
            throw new UcpSignatureException('Agent profile host could not be resolved to a public address.');
        }

        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
                || IpUtils::isPrivateIp($ip)
            ) {
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
