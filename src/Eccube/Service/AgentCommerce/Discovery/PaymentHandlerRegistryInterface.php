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

namespace Eccube\Service\AgentCommerce\Discovery;

/**
 * UCP discovery profile の payment_handlers を寄与する口.
 *
 * UCP profile の payment_handlers は **reverse-domain キー** のレジストリ
 * (正規表現 ^[a-z][a-z0-9]*(?:\.[a-z][a-z0-9_]*)+$) であり、決済ハンドラプラグイン
 * (将来の Google Pay 等) が tagged service として寄与する設計とする.
 *
 * core 本体は payment_handlers をカラム化せず、本インターフェイスの実装からのみ収集する.
 * UCP profile schema 上 payment_handlers は必須のため、寄与が無い場合でも空オブジェクト {} を出す.
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/schemas/profile.json
 */
interface PaymentHandlerRegistryInterface
{
    /**
     * payment_handlers レジストリを連想配列で返す.
     *
     * キーは reverse-domain 形式 (例: "dev.ucp.payment.google_pay")、
     * 値は当該決済ハンドラの宣言オブジェクト.
     *
     * @return array<string, array<string, mixed>> reverse-domain キーのレジストリ (既定は空)
     */
    public function collect(): array;
}
