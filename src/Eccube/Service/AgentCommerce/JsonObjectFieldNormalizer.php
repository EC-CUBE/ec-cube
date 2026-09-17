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

namespace Eccube\Service\AgentCommerce;

/**
 * JSON 応答で「空でも object ({}) でなければならない」フィールドを stdClass へ正規化する.
 *
 * PHP の空配列は json_encode で `[]` になる。schema が object を要求するレジストリ
 * (UCP の `ucp.payment_handlers` / `ucp.capabilities`、ACP の `capabilities`) は空でも `{}` で
 * 配信しなければならない。mapper は純粋な連想配列を返し、HTTP 境界 (controller) で本クラスを通す。
 *
 * Idempotency-Key リプレイの本文は DB の json 列 (assoc decode) から戻るため、mapper が stdClass を
 * 入れていても `{}` → `[]` に退行する。配信直前に正規化することで、初回応答とリプレイの双方で
 * 同じ形を保証する。
 */
final class JsonObjectFieldNormalizer
{
    /**
     * 指定パスの値が空配列なら stdClass に置き換える (存在しないパスは無視).
     *
     * @param array<string, mixed> $body
     * @param list<string> $paths ドット区切りのパス (例: "ucp.payment_handlers")
     *
     * @return array<string, mixed>
     */
    public static function normalize(array $body, array $paths): array
    {
        foreach ($paths as $path) {
            $body = self::normalizePath($body, explode('.', $path));
        }

        return $body;
    }

    /**
     * @param array<string, mixed> $node
     * @param list<string> $segments
     *
     * @return array<string, mixed>
     */
    private static function normalizePath(array $node, array $segments): array
    {
        $key = array_shift($segments);
        if ($key === null || !array_key_exists($key, $node)) {
            return $node;
        }

        if ($segments === []) {
            if ($node[$key] === []) {
                $node[$key] = new \stdClass();
            }

            return $node;
        }

        if (is_array($node[$key])) {
            $node[$key] = self::normalizePath($node[$key], $segments);
        }

        return $node;
    }
}
