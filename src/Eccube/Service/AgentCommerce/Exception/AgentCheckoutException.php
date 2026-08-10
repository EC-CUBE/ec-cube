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

namespace Eccube\Service\AgentCommerce\Exception;

/**
 * エージェントチェックアウトのプロトコル系エラー.
 *
 * 不正要求・処理不能を表す。HTTP ステータスや messages[] への変換はプロトコル層が行う。
 */
class AgentCheckoutException extends \RuntimeException
{
    public function __construct(
        private readonly AgentCheckoutErrorCode $errorCode,
        string $message = '',
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message !== '' ? $message : $errorCode->value, 0, $previous);
    }

    public function getErrorCode(): AgentCheckoutErrorCode
    {
        return $this->errorCode;
    }
}
