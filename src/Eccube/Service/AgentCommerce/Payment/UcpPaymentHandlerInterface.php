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
     * UCP は ACP と異なり、再開 complete の入力もこのクレデンシャル経由で届く。追加認証の結果など
     * **再開に必要な項目を戻り値から落とさない**こと (落とすと REQUIRES_ACTION から復帰できなくなる)。
     *
     * 本メソッドは complete の状態機械の**外側** (controller のペイロード解決時) で呼ばれるため、
     * {@link \Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutCompletionService} の
     * authorize/capture 用の砦が効かない。**例外を投げてはならない**。トークンを解決できない入力も
     * そのまま返し、{@link AgentCheckoutPaymentHandlerInterface::authorize()} 側で fail-closed に
     * 失敗させること (状態機械へ入れば在庫の回収とセッション遷移が伴う)。
     *
     * 契約に反して投げた場合も HTTP 500 にはならない。controller が捕捉してビジネス系エラー
     * (HTTP 200 + messages[]) へ写像し、セッションを据え置いて再試行を許す。ただしその経路では
     * 失敗理由をエージェントへ伝えられないため、依存してはならない。
     *
     * @param array<string, mixed> $credential payment.instruments[].credential の中立表現 (type/token 等)
     *
     * @return array<string, mixed> authorize() が PSP へ渡す中立な支払データ
     */
    public function exchangePaymentToken(array $credential): array;
}
