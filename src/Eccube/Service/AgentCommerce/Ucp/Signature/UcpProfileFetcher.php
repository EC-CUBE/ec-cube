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
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
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

        try {
            $response = $this->httpClient->request('GET', $profileUrl, [
                // 鍵探索ではリダイレクトを追従しない (なりすまし防止)。
                'max_redirects' => 0,
                'headers' => ['Accept' => 'application/json'],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw new UcpSignatureException(sprintf('Agent profile fetch returned HTTP %d (redirects are not followed).', $statusCode));
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
