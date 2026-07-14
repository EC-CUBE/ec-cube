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

/**
 * エージェントコマースのメッセージ署名・検証を抽象化するインターフェイス.
 *
 * 署名アルゴリズムはプロトコルごとに差し替え可能とする (UCP=RFC 9421 / EC P-256 など).
 * 公開鍵は discovery の signing_keys[] (JWK) として広告される.
 */
interface AgentCommerceMessageSignerInterface
{
    /**
     * 署名ベース文字列に署名し, base64url エンコードした署名値を返す.
     *
     * @param string $signatureBase 署名対象のバイト列 (RFC 9421 の signature base 等)
     *
     * @return string base64url エンコードされた署名
     */
    public function sign(string $signatureBase): string;

    /**
     * base64url エンコードされた署名を検証する.
     *
     * 鍵ローテーションの grace period 中は旧公開鍵でも検証を許可する.
     *
     * @param string $signatureBase 署名対象のバイト列
     * @param string $signature     base64url エンコードされた署名
     *
     * @return bool 検証に成功したとき true
     */
    public function verify(string $signatureBase, string $signature): bool;

    /**
     * 公開鍵 JWK (連想配列) の配列を返す. 現用鍵 + grace period の旧鍵を含む.
     *
     * 秘密鍵パラメータ (d) は絶対に含めない.
     *
     * @return array<int, array<string, mixed>> JWK の配列
     */
    public function getPublicJwks(): array;

    /**
     * 現用鍵の Key ID (kid) を返す.
     */
    public function getCurrentKid(): string;
}
