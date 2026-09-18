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

/**
 * ビジネスロジックの結果メッセージ (中立表現).
 */
final readonly class AgentCheckoutMessage
{
    public function __construct(
        public AgentCheckoutMessageLevel $level,
        public string $message,
    ) {
    }
}
