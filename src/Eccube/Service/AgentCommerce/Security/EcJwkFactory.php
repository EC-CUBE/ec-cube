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

namespace Eccube\Service\AgentCommerce\Security;

use phpseclib3\Crypt\EC\PublicKey;

/**
 * EC 公開鍵を JWK (RFC 7517) と Key ID (RFC 7638 JWK Thumbprint) へ変換する.
 *
 * discovery の signing_keys[] を組み立てる UcpMessageSigner と,
 * eccube:keystore:show へ公開情報を渡す UcpSigningKeyPurpose の双方が使う.
 * 同じ鍵に対して同じ kid を返す必要があるため, 実装は 1 箇所に置く.
 */
final class EcJwkFactory
{
    /**
     * 公開鍵を EC JWK (連想配列) に変換する. 秘密鍵パラメータ d は含めない.
     *
     * @return array<string, mixed>
     */
    public static function toPublicJwk(PublicKey $publicKey): array
    {
        $coords = self::extractCoordinates($publicKey);

        return [
            'kty' => 'EC',
            'crv' => 'P-256',
            'x' => $coords['x'],
            'y' => $coords['y'],
            'use' => 'sig',
            'alg' => 'ES256',
            'kid' => self::thumbprintFromCoordinates($coords['x'], $coords['y']),
        ];
    }

    /**
     * RFC 7638 JWK Thumbprint を kid として算出する.
     */
    public static function thumbprint(PublicKey $publicKey): string
    {
        $coords = self::extractCoordinates($publicKey);

        return self::thumbprintFromCoordinates($coords['x'], $coords['y']);
    }

    /**
     * 公開鍵から JWK の座標 (x, y; base64url) を抽出する.
     *
     * phpseclib3 の toString('JWK') は ext-sodium が無い環境では padding 付き
     * (末尾 "=") の base64url を返す (Strings::base64url_encode のフォールバック先
     * paragonie Base64UrlSafe::encode が padding を付けるため). RFC 7515 §2 の
     * base64url は padding 省略が必須なので, 環境差に関わらず必ず正規化する.
     * kid (RFC 7638) も x/y の文字列から算出するため, 正規化しないと同じ鍵の kid が
     * sodium の有無で変わってしまう.
     *
     * @return array{x: string, y: string}
     */
    private static function extractCoordinates(PublicKey $publicKey): array
    {
        $x = null;
        $y = null;

        try {
            $jwkJson = $publicKey->toString('JWK');
            /** @var array<string, mixed>|null $decoded */
            $decoded = json_decode($jwkJson, true);
            if (is_array($decoded)) {
                // JWK は {keys:[{...}]} か単体 {x,y} のいずれもあり得るため両対応.
                if (isset($decoded['keys'][0]) && is_array($decoded['keys'][0])) {
                    $decoded = $decoded['keys'][0];
                }
                if (isset($decoded['x']) && is_string($decoded['x'])) {
                    $x = $decoded['x'];
                }
                if (isset($decoded['y']) && is_string($decoded['y'])) {
                    $y = $decoded['y'];
                }
            }
        } catch (\Throwable) {
            // 下の例外で扱う.
        }

        if ($x === null || $y === null) {
            // phpseclib3 の EC 公開鍵は toString('JWK') で必ず x/y を返すため通常到達しない.
            throw new \RuntimeException('EC 公開鍵から JWK 座標を取得できませんでした.');
        }

        return ['x' => self::normalizeBase64url($x), 'y' => self::normalizeBase64url($y)];
    }

    /**
     * base64 / base64url を RFC 7515 §2 の base64url (URL-safe alphabet, padding なし) に正規化する.
     */
    private static function normalizeBase64url(string $value): string
    {
        return rtrim(strtr($value, '+/', '-_'), '=');
    }

    /**
     * RFC 7638: 必須メンバ (crv, kty, x, y) を辞書順・余白なし JSON にして SHA-256 する.
     */
    private static function thumbprintFromCoordinates(string $x, string $y): string
    {
        // キー昇順 (crv, kty, x, y), 余白なし.
        $canonical = json_encode([
            'crv' => 'P-256',
            'kty' => 'EC',
            'x' => $x,
            'y' => $y,
        ], JSON_UNESCAPED_SLASHES);

        return rtrim(strtr(base64_encode(hash('sha256', (string) $canonical, true)), '+/', '-_'), '=');
    }
}
