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

/**
 * `UCP-Agent` リクエストヘッダ (RFC 8941 Dictionary) のパーサ.
 *
 * 形式: `profile="https://platform.example/.well-known/ucp"` のように、
 * `profile` メンバにプラットフォームの HTTPS プロファイル URL を保持する。
 * 全 UCP リクエストで必須であり、署名検証時の公開鍵取得元となる。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP signatures.md (UCP-Agent header)
 */
final readonly class UcpAgentHeader
{
    private function __construct(
        public string $profileUrl,
    ) {
    }

    /**
     * ヘッダ値から profile URL を抽出する.
     *
     * @throws \InvalidArgumentException profile メンバが無い / HTTPS でない場合
     */
    public static function parse(string $headerValue): self
    {
        // RFC 8941 Dictionary の単純パース: profile="..." を取り出す。
        if (!preg_match('/profile\s*=\s*"([^"]+)"/', $headerValue, $matches)) {
            throw new \InvalidArgumentException('UCP-Agent header must contain a quoted "profile" member.');
        }

        $url = $matches[1];
        $scheme = parse_url($url, PHP_URL_SCHEME);
        // RFC 3986 では scheme は大文字小文字を区別しない (HTTPS == https) ため小文字化して比較する。
        if (!is_string($scheme) || strtolower($scheme) !== 'https') {
            throw new \InvalidArgumentException('UCP-Agent profile URL must be an absolute HTTPS URL.');
        }

        return new self($url);
    }

    /**
     * profile URL のホスト名を返す (ドメイン許可リスト照合に使用).
     */
    public function host(): string
    {
        $host = parse_url($this->profileUrl, PHP_URL_HOST);

        return is_string($host) ? strtolower($host) : '';
    }
}
