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

namespace Eccube\Tests\Service\AgentCommerce\Security;

use Eccube\Service\AgentCommerce\Security\EcJwkFactory;
use phpseclib3\Crypt\EC;
use phpseclib3\Crypt\EC\PublicKey;
use PHPUnit\Framework\TestCase;

/**
 * Layer 5 (signing) tests for EcJwkFactory.
 *
 * EC 公開鍵 → JWK (RFC 7517) / kid (RFC 7638 JWK Thumbprint) の変換が,
 * 実行環境 (ext-sodium の有無) に依存せず RFC の base64url 表現になることを検証する.
 *
 * phpseclib3 の toString('JWK') は ext-sodium が無い環境では padding 付き ("=") の
 * base64url を返す. RFC 7515 §2 は padding 省略を要求し, kid も x/y の文字列から
 * 算出されるため, padding の有無で同じ鍵の kid が変わってはならない.
 *
 * @see https://www.rfc-editor.org/rfc/rfc7515#section-2 (Base64url Encoding: trailing '=' omitted)
 * @see https://www.rfc-editor.org/rfc/rfc7518#section-6.2.1.2 (x/y are base64url of the coordinate octets)
 * @see https://www.rfc-editor.org/rfc/rfc7638#section-3 (JWK Thumbprint computation)
 */
final class EcJwkFactoryTest extends TestCase
{
    /**
     * P-256 座標 (32 octets) の base64url は 43 文字で, padding が付くと 44 文字になる.
     */
    private const P256_COORDINATE_BASE64URL_LENGTH = 43;

    private const BASE64URL_UNPADDED_PATTERN = '/^[A-Za-z0-9_-]+$/';

    public function testCoordinatesAreUnpaddedBase64url(): void
    {
        $jwk = EcJwkFactory::toPublicJwk($this->createPublicKey());

        foreach (['x', 'y'] as $member) {
            $this->assertMatchesRegularExpression(
                self::BASE64URL_UNPADDED_PATTERN,
                $jwk[$member],
                sprintf('RFC 7515 §2: JWK "%s" MUST be base64url with all trailing "=" padding characters omitted', $member)
            );
            $this->assertSame(
                self::P256_COORDINATE_BASE64URL_LENGTH,
                strlen($jwk[$member]),
                sprintf('RFC 7518 §6.2.1.2: "%s" MUST encode exactly 32 octets for P-256 (43 base64url characters)', $member)
            );
            $this->assertSame(
                32,
                strlen($this->base64urlDecode($jwk[$member])),
                sprintf('"%s" MUST decode to the 32-octet P-256 coordinate', $member)
            );
        }
    }

    public function testKidMatchesRfc7638ThumbprintComputedIndependently(): void
    {
        $publicKey = $this->createPublicKey();
        $jwk = EcJwkFactory::toPublicJwk($publicKey);

        // EcJwkFactory を経由せず phpseclib の JWK 出力から座標 octets を取り出し,
        // RFC 7638 §3 の手順 (必須メンバを辞書順・余白なしで JSON 化 → SHA-256 → base64url) で
        // 独立に thumbprint を計算する. 座標を一旦 octets に戻して再エンコードするため,
        // phpseclib 側の padding の有無に影響されない参照値になる.
        $raw = json_decode($publicKey->toString('JWK'), true);
        $raw = $raw['keys'][0] ?? $raw;
        $canonical = json_encode([
            'crv' => 'P-256',
            'kty' => 'EC',
            'x' => $this->base64urlEncode($this->base64urlDecode($raw['x'])),
            'y' => $this->base64urlEncode($this->base64urlDecode($raw['y'])),
        ], JSON_UNESCAPED_SLASHES);
        $expectedKid = $this->base64urlEncode(hash('sha256', (string) $canonical, true));

        $this->assertSame($expectedKid, $jwk['kid'], 'RFC 7638: kid MUST equal the JWK Thumbprint over the unpadded base64url x/y members');
        $this->assertSame($expectedKid, EcJwkFactory::thumbprint($publicKey), 'thumbprint() MUST return the same kid as toPublicJwk()');
    }

    private function createPublicKey(): PublicKey
    {
        return EC::createKey('secp256r1')->getPublicKey();
    }

    private function base64urlEncode(string $binary): string
    {
        return rtrim(strtr(base64_encode($binary), '+/', '-_'), '=');
    }

    /**
     * padding の有無どちらも受け付ける (参照値の計算用).
     */
    private function base64urlDecode(string $value): string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        $this->assertNotFalse($decoded, 'value must be valid base64url');

        return $decoded;
    }
}
