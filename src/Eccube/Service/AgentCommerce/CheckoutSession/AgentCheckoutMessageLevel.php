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
 * ビジネスロジックの結果メッセージのレベル.
 *
 * ACP/UCP 共通の「2 系統エラー」のうち、ビジネス系 (HTTP 200 + messages[]) に対応する。
 * プロトコル固有の表現 (ACP の `MessageError`/`MessageWarning`/`MessageInfo` 等) への変換は
 * プロトコル層 (#6776/#6574) で行う。
 */
enum AgentCheckoutMessageLevel: string
{
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';
}
