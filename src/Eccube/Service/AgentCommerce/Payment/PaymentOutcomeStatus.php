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
 * 決済ハンドラの処理結果ステータス.
 *
 * エージェントチェックアウトの complete は「中断 → 再開」する状態機械であり、追加認証 (EMV-3DS) や
 * 外部サイトへの buyer ハンドオフ (escalation) は**エラーではなく正常な中間状態**である。
 * 決済ハンドラ ({@link AgentCheckoutPaymentHandlerInterface}) はこのステータスを返し、
 * オーケストレータ ({@link \Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutCompletionService})
 * が CheckoutSession の正規化ステータス遷移・在庫引当の保持/回収を決定する。
 */
enum PaymentOutcomeStatus: string
{
    /**
     * 与信 (オーソリ) のみ成功し、**売上確定 (capture) が必要**.
     *
     * オーケストレータはこの結果を受けてはじめて {@link AgentCheckoutPaymentHandlerInterface::capture()} を呼ぶ。
     * 与信と売上が 1 度の通信で完結する auto-capture 型 PSP は本ステータスでなく {@link self::COMPLETED}
     * を返すこと (本ステータスを返すと capture が二重発行される)。
     */
    case AUTHORIZED = 'authorized';

    /**
     * 売上まで確定済で、注文を確定 (commit) してよい.
     *
     * capture 済 (または capture 不要な auto-capture 型 PSP) を表す。
     * **オーケストレータは本ステータスに対して capture を呼ばない**。
     */
    case COMPLETED = 'completed';

    /** 追加対応が必要 (3DS challenge / escalation)。在庫は引当のまま保持し、再開 complete を待つ. */
    case REQUIRES_ACTION = 'requires_action';

    /** 非同期処理中 (PSP の確定通知待ち)。IPN/Webhook で完了させる. */
    case PENDING = 'pending';

    /** 拒否・エラー。引当をロールバックする. */
    case FAILED = 'failed';
}
