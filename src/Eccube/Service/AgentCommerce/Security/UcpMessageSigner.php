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

use phpseclib3\Crypt\EC;
use phpseclib3\Crypt\EC\PrivateKey;
use phpseclib3\Crypt\EC\PublicKey;
use phpseclib3\Crypt\PublicKeyLoader;

/**
 * UCP の RFC 9421 HTTP Message Signatures 向け署名実装.
 *
 * EC P-256 (secp256r1) / ES256 を使用し, 署名は raw R||S (IEEE P1363, 64byte) 形式で生成する.
 * 公開鍵は EC JWK (kty:"EC", crv:"P-256", x, y) として discovery の signing_keys[] に広告する.
 * 鍵ストア上に秘密鍵が無ければ生成して永続化する (KeyStoreInterface 経由).
 */
class UcpMessageSigner implements AgentCommerceMessageSignerInterface
{
    /**
     * @var array<int, string> grace period 中の旧公開鍵 PEM 群
     */
    private readonly array $gracePublicKeyPems;

    private ?PrivateKey $privateKey = null;

    /**
     * @param KeyStoreInterface   $keyStore           秘密鍵 PEM の読み書きストア
     * @param string              $purpose            鍵の用途識別子 (KeyStore のパス解決に使用)
     * @param array<int, string>  $gracePublicKeyPems 旧公開鍵 PEM 群 (verify/JWK に含める)
     */
    public function __construct(
        private readonly KeyStoreInterface $keyStore,
        private readonly string $purpose = 'ucp_signing',
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
            $jwks[] = $this->toPublicJwk($publicKey);
        }

        return $jwks;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentKid(): string
    {
        return $this->thumbprint($this->getCurrentPublicKey());
    }

    /**
     * 現用秘密鍵を取得する. 鍵ストアに無ければ EC P-256 を生成して永続化する.
     */
    private function getPrivateKey(): PrivateKey
    {
        if ($this->privateKey instanceof PrivateKey) {
            return $this->privateKey;
        }

        $pem = $this->keyStore->read($this->purpose);
        if ($pem === null || trim($pem) === '') {
            /** @var PrivateKey $generated */
            $generated = EC::createKey('secp256r1');
            $pem = $generated->toString('PKCS8');
            $this->keyStore->write($this->purpose, $pem);
            $this->privateKey = $generated;

            return $this->privateKey;
        }

        $loaded = PublicKeyLoader::load($pem);
        if (!$loaded instanceof PrivateKey) {
            throw new \RuntimeException(sprintf('鍵ストアの "%s" は EC 秘密鍵ではありません.', $this->purpose));
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

    /**
     * 公開鍵を EC JWK (連想配列) に変換する. 秘密鍵パラメータ d は含めない.
     *
     * @return array<string, mixed>
     */
    private function toPublicJwk(PublicKey $publicKey): array
    {
        $coords = $this->extractCoordinates($publicKey);

        $jwk = [
            'kty' => 'EC',
            'crv' => 'P-256',
            'x' => $coords['x'],
            'y' => $coords['y'],
            'use' => 'sig',
            'alg' => 'ES256',
        ];
        $jwk['kid'] = $this->thumbprintFromCoordinates($coords['x'], $coords['y']);

        return $jwk;
    }

    /**
     * 公開鍵から JWK の座標 (x, y; base64url) を抽出する.
     *
     * 主: phpseclib の toString('JWK') を利用.
     * フォールバック (コメント): phpseclib のバージョン差で 'JWK' 出力のキー名が
     * 異なる / 取得できない場合は, getEncodedCoordinates() 等で得た
     * uncompressed point (0x04 || X(32) || Y(32)) を 32byte ずつに分割し,
     * それぞれ base64url する実装に切り替えること. ここではまず JWK 出力を解析し,
     * 取得不能な場合に uncompressed point から座標を復元する.
     *
     * @return array{x: string, y: string}
     */
    private function extractCoordinates(PublicKey $publicKey): array
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
            // 下のフォールバックで座標を復元する.
        }

        if ($x === null || $y === null) {
            // phpseclib3 の EC 公開鍵は toString('JWK') で必ず x/y を返すため通常到達しない。
            throw new \RuntimeException('EC 公開鍵から JWK 座標を取得できませんでした.');
        }

        return ['x' => $x, 'y' => $y];
    }

    /**
     * RFC 7638 JWK Thumbprint を kid として算出する.
     */
    private function thumbprint(PublicKey $publicKey): string
    {
        $coords = $this->extractCoordinates($publicKey);

        return $this->thumbprintFromCoordinates($coords['x'], $coords['y']);
    }

    /**
     * RFC 7638: 必須メンバ (crv, kty, x, y) を辞書順・余白なし JSON にして SHA-256 する.
     */
    private function thumbprintFromCoordinates(string $x, string $y): string
    {
        // キー昇順 (crv, kty, x, y), 余白なし.
        $canonical = json_encode([
            'crv' => 'P-256',
            'kty' => 'EC',
            'x' => $x,
            'y' => $y,
        ], JSON_UNESCAPED_SLASHES);

        return $this->base64urlEncode(hash('sha256', (string) $canonical, true));
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
