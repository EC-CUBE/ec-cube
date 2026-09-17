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

use Symfony\Component\HttpFoundation\Request;

/**
 * RFC 9421 HTTP Message Signatures の signature base 文字列を構築する.
 *
 * Signature-Input が宣言する covered component を順に
 * `"<component>": <value>` 行へ展開し、最後に `"@signature-params": <params>` 行を付す。
 * UCP checkout が署名対象とするコンポーネント (@method/@authority/@path、および
 * ucp-agent/idempotency-key/content-digest/content-type ヘッダ) を解決できる。
 *
 * @see https://www.rfc-editor.org/rfc/rfc9421 RFC 9421 Section 2.5 (Creating the Signature Base)
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP signatures.md
 */
class Rfc9421SignatureBaseBuilder
{
    /**
     * signature base を構築する.
     *
     * @param Request      $request           署名対象のリクエスト
     * @param list<string> $coveredComponents covered component 識別子 (小文字・派生は先頭 @)
     * @param string       $signatureParams   `@signature-params` の値 (例: `("@method" "@path");created=...;keyid="..."`)
     *
     * @throws \InvalidArgumentException 解決できない component が含まれる場合
     */
    public function build(Request $request, array $coveredComponents, string $signatureParams): string
    {
        $lines = [];
        foreach ($coveredComponents as $component) {
            $value = $this->resolveComponent($request, $component);
            $lines[] = sprintf('"%s": %s', $component, $value);
        }

        $lines[] = sprintf('"@signature-params": %s', $signatureParams);

        return implode("\n", $lines);
    }

    /**
     * covered component の値を解決する.
     */
    private function resolveComponent(Request $request, string $component): string
    {
        return match ($component) {
            '@method' => strtoupper($request->getMethod()),
            '@authority' => strtolower($request->getHttpHost()),
            '@path' => $this->path($request),
            '@query' => '?'.($request->getQueryString() ?? ''),
            '@scheme' => strtolower($request->getScheme()),
            '@target-uri' => $request->getUri(),
            default => $this->resolveHeader($request, $component),
        };
    }

    /**
     * 派生コンポーネント以外はヘッダ値として解決する (RFC 9421 では小文字のフィールド名).
     */
    private function resolveHeader(Request $request, string $component): string
    {
        if (str_starts_with($component, '@')) {
            throw new \InvalidArgumentException(sprintf('Unsupported derived component "%s".', $component));
        }

        $value = $request->headers->get($component);
        if ($value === null) {
            throw new \InvalidArgumentException(sprintf('Signed header "%s" is missing from the request.', $component));
        }

        // RFC 9421 Section 2.1: 先頭末尾の OWS を除去する (obs-fold は HTTP/1.1 で既に除去済み想定)。
        return trim($value);
    }

    /**
     * リクエストターゲットの絶対パス部分 (@path) を返す.
     */
    private function path(Request $request): string
    {
        $requestUri = $request->getRequestUri();
        $queryPos = strpos($requestUri, '?');

        return $queryPos === false ? $requestUri : substr($requestUri, 0, $queryPos);
    }
}
