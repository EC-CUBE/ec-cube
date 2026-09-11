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

namespace Eccube\Service\AgentCommerce\Security;

use phpseclib3\Crypt\EC\PrivateKey;
use phpseclib3\Crypt\EC\PublicKey;
use phpseclib3\Crypt\PublicKeyLoader;

/**
 * UCP の RFC 9421 HTTP Message Signatures 向け署名実装.
 *
 * EC P-256 (secp256r1) / ES256 を使用し, 署名は raw R||S (IEEE P1363, 64byte) 形式で生成する.
 * 公開鍵は EC JWK (kty:"EC", crv:"P-256", x, y) として discovery の signing_keys[] に広告する.
 * 鍵ストア上に秘密鍵が無ければ生成して永続化する (KeyStoreInterface 経由).
 *
 * 鍵の生成方法は KeyPurposeInterface (UcpSigningKeyPurpose) が持つ. CLI
 * (eccube:keystore:generate) と同じ経路を通すことで, どちらから作っても同じ鍵になる.
 */
class UcpMessageSigner implements AgentCommerceMessageSignerInterface
{
    /**
     * @var array<int, string> grace period 中の旧公開鍵 PEM 群
     */
    private readonly array $gracePublicKeyPems;

    private ?PrivateKey $privateKey = null;

    /**
     * @param KeyStoreInterface  $keyStore           秘密鍵 PEM の読み書きストア
     * @param KeyPurposeInterface $keyPurpose        鍵の用途 (purpose の解決と生成を担う)
     * @param array<int, string> $gracePublicKeyPems 旧公開鍵 PEM 群 (verify/JWK に含める)
     */
    public function __construct(
        private readonly KeyStoreInterface $keyStore,
        private readonly KeyPurposeInterface $keyPurpose,
        array $gracePublicKeyPems = [],
    ) {
        $this->gracePublicKeyPems = array_values($gracePublicKeyPems);
    }

    /**
     * {@inheritdoc}
     */
    public function sign(string $signatureBase): string
    {
        $raw = $this->getPrivateKey()
            ->withSignatureFormat('IEEE')
            ->withHash('sha256')
            ->sign($signatureBase);

        return $this->base64urlEncode($raw);
    }

    /**
     * {@inheritdoc}
     */
    public function verify(string $signatureBase, string $signature): bool
    {
        $raw = $this->base64urlDecode($signature);
        if ($raw === '') {
            return false;
        }

        // 現用鍵 + grace period の旧公開鍵すべてに対して検証を試みる.
        foreach ($this->collectPublicKeys() as $publicKey) {
            try {
                $verified = $publicKey
                    ->withSignatureFormat('IEEE')
                    ->withHash('sha256')
                    ->verify($signatureBase, $raw);
            } catch (\Throwable) {
                // 不正な署名長などで例外が出る実装差異を吸収し, 次の鍵へ.
                continue;
            }

            if ($verified) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicJwks(): array
    {
        $jwks = [];
        foreach ($this->collectPublicKeys() as $publicKey) {
            $jwks[] = EcJwkFactory::toPublicJwk($publicKey);
        }

        return $jwks;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentKid(): string
    {
        return EcJwkFactory::thumbprint($this->getCurrentPublicKey());
    }

    /**
     * 現用秘密鍵を取得する. 鍵ストアに無ければ生成して永続化する.
     */
    private function getPrivateKey(): PrivateKey
    {
        if ($this->privateKey instanceof PrivateKey) {
            return $this->privateKey;
        }

        $purpose = $this->keyPurpose->getPurpose();

        $pem = $this->keyStore->read($purpose);
        if ($pem === null || trim($pem) === '') {
            // CLI を使えない構成 (共有レンタルサーバー等) のためのフォールバック.
            // 権限を分離した構成では eccube:keystore:generate で事前に配置しておく.
            $pem = $this->keyPurpose->generate();
            $this->keyStore->write($purpose, $pem);
        }

        $loaded = PublicKeyLoader::load($pem);
        if (!$loaded instanceof PrivateKey) {
            throw new \RuntimeException(sprintf('鍵ストアの "%s" は EC 秘密鍵ではありません.', $purpose));
        }

        $this->privateKey = $loaded;

        return $this->privateKey;
    }

    private function getCurrentPublicKey(): PublicKey
    {
        return $this->getPrivateKey()->getPublicKey();
    }

    /**
     * 現用 + grace の公開鍵を返す.
     *
     * @return array<int, PublicKey>
     */
    private function collectPublicKeys(): array
    {
        $keys = [$this->getCurrentPublicKey()];

        foreach ($this->gracePublicKeyPems as $pem) {
            if (trim($pem) === '') {
                continue;
            }
            $loaded = PublicKeyLoader::load($pem);
            if ($loaded instanceof PrivateKey) {
                $keys[] = $loaded->getPublicKey();
            } elseif ($loaded instanceof PublicKey) {
                $keys[] = $loaded;
            }
        }

        return $keys;
    }

    private function base64urlEncode(string $binary): string
    {
        return rtrim(strtr(base64_encode($binary), '+/', '-_'), '=');
    }

    private function base64urlDecode(string $value): string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        return $decoded === false ? '' : $decoded;
    }
}
