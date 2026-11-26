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

use Eccube\Entity\Order;

/**
 * 中断中の取引を PSP 側で取り消せる決済ハンドラ (任意実装).
 *
 * 追加認証 (REQUIRES_ACTION) や非同期確定待ち (IN_PROGRESS) のセッションが cancel / 期限切れに
 * なったとき、在庫の回収に加えて PSP 側の与信・取引も解放するために呼ばれる。
 * {@link AgentCheckoutPaymentHandlerInterface} へメソッドを足すと既存の決済プラグインが
 * fatal になるため、取消に対応するハンドラだけが本インターフェースを追加で実装する。
 */
interface CancellablePaymentHandlerInterface extends AgentCheckoutPaymentHandlerInterface
{
    /**
     * 中断中の取引を取り消す.
     *
     * 例外は投げず、失敗はハンドラ側でログに残すこと (投げても呼び出し側でログに残して処理を続ける)。
     * 在庫の回収とセッションの終端遷移は、取消の成否に関わらず行われる。
     *
     * @param array<string, mixed> $paymentReference 中断時に保持した PSP 参照 (`transaction_id` と metadata)
     */
    public function cancel(Order $order, array $paymentReference): void;
}
