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

namespace Eccube\Service\AgentCommerce\Acp;

use Eccube\Service\AgentCommerce\Security\KeyStoreInterface;

/**
 * ACP Webhook の `Merchant-Signature` (HMAC-SHA256) 署名生成・検証基盤.
 *
 * ACP の Webhook (加盟店 → エージェントへの order_create / order_update 通知) は
 * `Merchant-Signature: t=<unix>,v1=<hex>` ヘッダで署名する。署名対象は
 * `<timestamp> + "." + <raw_body>`、アルゴリズムは共有シークレットによる HMAC-SHA256。
 *
 * 本クラスは署名生成・検証のプリミティブを提供する (#6776 の範囲)。実際の outbound 送出
 * (HTTP クライアントでの通知配送・イベント組み立て) は後続 PR で本クラスを利用して実装する。
 *
 * 共有シークレットは keystore (#6797) から解決する (purpose: `acp_webhook`)。未設定時は
 * ランダム生成して永続化する (UCP 署名鍵と同じ解決方式)。エージェントへの共有 (provisioning) は運用手順。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP openapi.agentic_checkout_webhook.yaml (Merchant-Signature)
 */
class AcpMessageSigner
{
    private ?string $secret = null;

    public function __construct(
        private readonly KeyStoreInterface $keyStore,
        private readonly string $purpose = 'acp_webhook',
    ) {
    }

    /**
     * raw body と timestamp から `Merchant-Signature` ヘッダ値 (`t=<unix>,v1=<hex>`) を生成する.
     */
    public function sign(string $rawBody, int $timestamp): string
    {
        return sprintf('t=%d,v1=%s', $timestamp, $this->computeHex($rawBody, $timestamp));
    }

    /**
     * `Merchant-Signature` ヘッダを検証する.
     *
     * timestamp が許容範囲内 (既定 300 秒) かつ HMAC が一致する場合のみ true。署名欠落・不正フォーマット・
     * タイムスタンプ超過・不一致はいずれも false (呼び出し側で 401 に変換する)。
     *
     * @param int|null $now 現在時刻 (テスト用に注入可。null なら time())
     */
    public function verify(string $rawBody, string $header, int $toleranceSeconds = 300, ?int $now = null): bool
    {
        $parsed = $this->parseHeader($header);
        if ($parsed === null) {
            return false;
        }
        [$timestamp, $signature] = $parsed;

        $now ??= time();
        if (abs($now - $timestamp) > $toleranceSeconds) {
            return false;
        }

        return hash_equals($this->computeHex($rawBody, $timestamp), $signature);
    }

    /**
     * `t=<unix>,v1=<hex>` 形式のヘッダを (timestamp, signature) に分解する. 不正なら null.
     *
     * @return array{0: int, 1: string}|null
     */
    private function parseHeader(string $header): ?array
    {
        $timestamp = null;
        $signature = null;
        foreach (explode(',', $header) as $part) {
            $pair = explode('=', trim($part), 2);
            if (count($pair) !== 2) {
                continue;
            }
            [$name, $value] = $pair;
            if ($name === 't' && ctype_digit($value)) {
                $timestamp = (int) $value;
            } elseif ($name === 'v1' && ctype_xdigit($value)) {
                $signature = $value;
            }
        }

        if ($timestamp === null || $signature === null) {
            return null;
        }

        return [$timestamp, $signature];
    }

    /**
     * HMAC-SHA256(`<timestamp>.<raw_body>`) の 16 進数表現を返す.
     */
    private function computeHex(string $rawBody, int $timestamp): string
    {
        return hash_hmac('sha256', $timestamp.'.'.$rawBody, $this->getSecret());
    }

    /**
     * 共有シークレットを取得する. keystore に無ければランダム生成して永続化する.
     */
    private function getSecret(): string
    {
        if ($this->secret !== null) {
            return $this->secret;
        }

        $stored = $this->keyStore->read($this->purpose);
        if ($stored !== null && trim($stored) !== '') {
            $this->secret = trim($stored);

            return $this->secret;
        }

        $generated = bin2hex(random_bytes(32));
        $this->keyStore->write($this->purpose, $generated);
        $this->secret = $generated;

        return $this->secret;
    }
}
