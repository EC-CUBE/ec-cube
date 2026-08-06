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

namespace Eccube\Service\AgentCommerce\Payment;

/**
 * UCP の Payment Token Exchange 向け決済ハンドラ.
 *
 * UCP checkout の complete 時に、エージェントから渡された支払クレデンシャル
 * (payment_handler 固有のトークン) を PSP のゲートウェイトークンへ交換 (exchange) する。
 * 具体的な交換ロジックは決済プラグイン側に実装し (core は型のみ保持)、本体は
 * {@link AgentCheckoutPaymentHandlerRegistry} 経由で handler_id により解決する。
 *
 * UCP discovery profile の `payment_handlers` (逆ドメイン名キーのレジストリ) は
 * {@link getHandlerId()} が返す識別子で広告される。
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6574
 */
interface UcpPaymentHandlerInterface extends AgentCheckoutPaymentHandlerInterface
{
    /**
     * このハンドラの識別子 (UCP の逆ドメイン名形式、例: "dev.ucp.payment.card").
     *
     * discovery profile の `payment_handlers` キー、および complete リクエストの
     * payment.instruments[].handler_id との突合に用いる。
     */
    public function getHandlerId(): string;

    /**
     * エージェントから渡された支払クレデンシャルを PSP のゲートウェイトークンへ交換する.
     *
     * @param array<string, mixed> $credential payment.instruments[].credential の中立表現 (type/token 等)
     *
     * @return array<string, mixed> 後続の authorize()/capture() へ渡す中立な支払データ
     */
    public function exchangePaymentToken(array $credential): array;
}
