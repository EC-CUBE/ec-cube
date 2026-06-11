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

namespace Eccube\Service\AgentCommerce\CheckoutSession;

use Eccube\Entity\Order;

/**
 * チェックアウト見積/確定の結果.
 *
 * 再計算後の `Order` (税・送料・手数料・合計が確定済) と、ビジネス系メッセージ
 * (在庫不足・販売停止・配送制限等) を保持する。
 */
final readonly class AgentCheckoutResult
{
    /**
     * @param array<int, AgentCheckoutMessage> $messages
     */
    public function __construct(
        public Order $order,
        public array $messages = [],
    ) {
    }

    public function hasError(): bool
    {
        foreach ($this->messages as $message) {
            if ($message->level === AgentCheckoutMessageLevel::ERROR) {
                return true;
            }
        }

        return false;
    }

    public function hasWarning(): bool
    {
        foreach ($this->messages as $message) {
            if ($message->level === AgentCheckoutMessageLevel::WARNING) {
                return true;
            }
        }

        return false;
    }
}
