<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\PurchaseFlow;

class ProcessResult
{
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const SUCCESS = 'SUCCESS';

    protected $type;

    protected $message;

    private function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    public static function warn($message)
    {
        return new self(self::WARNING, $message);
    }

    public static function error($message)
    {
        return new self(self::ERROR, $message);
    }

    public static function success()
    {
        return new self(self::SUCCESS, null);
    }

    public function isError()
    {
        return $this->type === self::ERROR;
    }

    public function isWarning()
    {
        return $this->type === self::WARNING;
    }

    public function isSuccess()
    {
        return $this->type === self::SUCCESS;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
