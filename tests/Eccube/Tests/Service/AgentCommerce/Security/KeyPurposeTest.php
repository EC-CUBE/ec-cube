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

use Eccube\Service\AgentCommerce\Security\AcpWebhookKeyPurpose;
use Eccube\Service\AgentCommerce\Security\UcpSigningKeyPurpose;
use phpseclib3\Crypt\EC\PrivateKey;
use phpseclib3\Crypt\PublicKeyLoader;
use PHPUnit\Framework\TestCase;

/**
 * 鍵の用途 (生成と describe) のテスト.
 *
 * describe() は eccube:keystore:show の出力源になるため, 鍵素材が漏れないことを固定する.
 */
final class KeyPurposeTest extends TestCase
{
    public function testUcpSigningGeneratesEcP256PrivateKey(): void
    {
        $pem = (new UcpSigningKeyPurpose())->generate();

        $key = PublicKeyLoader::load($pem);
        $this->assertInstanceOf(PrivateKey::class, $key, 'EC 秘密鍵として読み込める');
        $this->assertSame('secp256r1', $key->getCurve());
    }

    public function testUcpSigningGeneratesADifferentKeyEachTime(): void
    {
        $purpose = new UcpSigningKeyPurpose();

        $this->assertNotSame($purpose->generate(), $purpose->generate());
    }

    public function testUcpSigningDescribeExposesPublicMaterialOnly(): void
    {
        $purpose = new UcpSigningKeyPurpose();
        $pem = $purpose->generate();

        $described = $purpose->describe($pem);

        $this->assertSame('ES256', $described['algorithm']);
        $this->assertNotSame('', $described['kid']);
        $this->assertIsArray($described['public_jwk']);
        $this->assertArrayNotHasKey('d', $described['public_jwk'], '公開鍵 JWK に秘密鍵パラメータ d を含めない');
        $this->assertSame($described['kid'], $described['public_jwk']['kid']);

        $this->assertMaterialIsNotExposed($pem, $described);
    }

    public function testUcpSigningDescribeRejectsMaterialThatIsNotAnEcPrivateKey(): void
    {
        $this->expectException(\RuntimeException::class);
        (new UcpSigningKeyPurpose())->describe('not a pem');
    }

    public function testAcpWebhookGeneratesHexSecret(): void
    {
        $purpose = new AcpWebhookKeyPurpose();

        $secret = $purpose->generate();

        $this->assertMatchesRegularExpression('/\A[0-9a-f]{64}\z/', $secret);
        $this->assertNotSame($secret, $purpose->generate());
    }

    public function testAcpWebhookDescribeDoesNotExposeTheSecret(): void
    {
        $purpose = new AcpWebhookKeyPurpose();
        $secret = $purpose->generate();

        $described = $purpose->describe($secret);

        $this->assertSame(['algorithm' => 'HMAC-SHA256', 'length' => 64], $described);
        $this->assertMaterialIsNotExposed($secret, $described);
    }

    /**
     * describe() の出力に鍵素材が現れないことを確かめる.
     *
     * PEM は base64 部分を連結したものと, 十分な長さを持つ各行の双方で照合する
     * (全体だけを見ると先頭部分だけ漏らす実装を見逃すため).
     *
     * @param array<string, mixed> $described
     */
    private function assertMaterialIsNotExposed(string $material, array $described): void
    {
        $encoded = (string) json_encode($described, JSON_UNESCAPED_SLASHES);

        $body = (string) preg_replace('/-----[^-]+-----|\s+/', '', $material);
        $this->assertNotSame('', $body);
        $this->assertStringNotContainsString($body, $encoded, '鍵素材が出力に含まれている');

        foreach (explode("\n", $material) as $line) {
            $line = trim($line);
            if (strlen($line) < 32 || str_starts_with($line, '-----')) {
                continue;
            }
            $this->assertStringNotContainsString($line, $encoded, '鍵素材の一部が出力に含まれている');
        }
    }
}
