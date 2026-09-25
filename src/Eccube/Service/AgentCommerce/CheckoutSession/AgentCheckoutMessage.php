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
 *
 * `code` は ACP / UCP とも error・warning で必須の理由コード。生成元が理由を特定できる場合は
 * {@link AgentCheckoutMessageCode} を渡す。null のときは各プロトコルの message mapper が
 * レベルに応じた既定値 (error → invalid / warning → limited_availability) を補う。
 */
final readonly class AgentCheckoutMessage
{
    public function __construct(
        public AgentCheckoutMessageLevel $level,
        public string $message,
        public ?AgentCheckoutMessageCode $code = null,
    ) {
    }
}
