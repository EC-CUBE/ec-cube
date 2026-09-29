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

use phpseclib4\Crypt\EC;
use phpseclib4\Crypt\EC\PrivateKey;
use phpseclib4\Crypt\PublicKeyLoader;

/**
 * UCP の HTTP Message Signatures (RFC 9421) に使う EC P-256 秘密鍵.
 *
 * 公開鍵は /.well-known/ucp の signing_keys[] として広告されるため,
 * describe() は公開鍵 JWK と kid を返す (秘密鍵パラメータ d は含めない).
 */
final class UcpSigningKeyPurpose implements KeyPurposeInterface
{
    public const PURPOSE = 'ucp_signing';

    #[\Override]
    public function getPurpose(): string
    {
        return self::PURPOSE;
    }

    #[\Override]
    public function getLabel(): string
    {
        return 'UCP の署名鍵 (EC P-256 / ES256). 公開鍵を /.well-known/ucp で広告する';
    }

    #[\Override]
    public function generate(): string
    {
        $key = EC::createKey('secp256r1');

        // phpseclib 4 の createKey() は OpenSSL 経由で loadPrivateKey() (password 既定 '') を通るため
        // 鍵の password が '' になり、toString() が isset('') で空パスワードの暗号化 PKCS8
        // (ENCRYPTED PRIVATE KEY) を書き出す。keystore は平文 PEM を前提としているので明示的に外す。
        return $key->withoutPassword()->toString('PKCS8');
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function describe(string $material): array
    {
        $publicKey = $this->load($material)->getPublicKey();

        return [
            'algorithm' => 'ES256',
            'curve' => 'P-256',
            'kid' => EcJwkFactory::thumbprint($publicKey),
            'public_jwk' => EcJwkFactory::toPublicJwk($publicKey),
        ];
    }

    /**
     * 保管されている PEM を EC 秘密鍵として読み込む.
     *
     * @throws \RuntimeException PEM として読めない, または EC 秘密鍵ではない場合
     */
    private function load(string $pem): PrivateKey
    {
        try {
            $loaded = PublicKeyLoader::load($pem);
        } catch (\Throwable $e) {
            throw new \RuntimeException(sprintf('鍵ストアの "%s" を鍵として読み込めません.', self::PURPOSE), 0, $e);
        }

        if (!$loaded instanceof PrivateKey) {
            throw new \RuntimeException(sprintf('鍵ストアの "%s" は EC 秘密鍵ではありません.', self::PURPOSE));
        }

        return $loaded;
    }
}
