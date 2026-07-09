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
use Eccube\Entity\Payment;

/**
 * エージェント注文に割り当てる支払方法 (Payment) を解決する seam.
 *
 * 通常購入では買い手が購入画面で支払方法を選ぶが、エージェントは画面を持たず
 * discovery で広告された決済ハンドラを `handler_id` で指定する。本リゾルバは
 * 「エージェントが選んだ決済ハンドラ」を起点に、対応する EC-CUBE の `Payment` を
 * 決定する (= ハンドラ解決軸と Payment 割当軸を一致させる) ことで、
 * {@link AgentCheckoutPaymentHandlerRegistry::resolveForOrder()} が
 * 常に同じハンドラへ解決されることを保証する。
 *
 * **sort_no に依存しない**: 利用可能な支払方法の先頭 (sort_no 最小) を機械的に採用すると、
 * エージェント決済不可能な支払方法 (銀行振込等) が割り当たり、ハンドラ未通過のまま確定する
 * 「沈黙のすり抜け」を招く。本リゾルバは登録済みハンドラが扱える Payment のみを候補とし、
 * sort_no は「エージェント決済可能な候補内のタイブレーク」としてのみ機能する。
 *
 * **拡張点**: 店舗ごとに選択ロジック (PSP の優先順位・通貨別ルーティング等) を変えたい場合は
 * 本インターフェイスを `app/Customize/Service/...` で実装し、サービスエイリアスを差し替える。
 */
interface AgentPaymentMethodResolverInterface
{
    /**
     * エージェント注文に割り当てる Payment を解決し、$order へ設定する.
     *
     * - $handlerId 指定時: その `handler_id` を宣言する決済ハンドラ
     *   ({@link AgentCheckoutPaymentHandlerInterface::getHandlerId()}) が扱える Payment を
     *   選ぶ (エージェントの明示選択を尊重)。
     * - $handlerId 省略時: その注文のプロトコルでエージェント決済可能な Payment の既定を選ぶ
     *   (create 時の見積算出向け)。
     *
     * 該当する Payment が無い場合は **沈黙フォールバックせず** null を返す。
     * 呼び出し側 (create=見積) は既定維持を、(complete=確定) はプロトコルエラー化を選択する。
     *
     * @return Payment|null 解決して $order に設定した Payment。該当無しは null
     */
    public function resolve(Order $order, ?string $handlerId = null): ?Payment;
}
