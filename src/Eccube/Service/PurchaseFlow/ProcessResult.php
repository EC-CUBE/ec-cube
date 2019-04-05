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

namespace Eccube\Service\PurchaseFlow;

class ProcessResult
{
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const SUCCESS = 'SUCCESS';

    protected $type;

    protected $message;

    protected $class;

    /**
     * @param string $type
     * @param string|null $message
     * @param string|null $class 呼び出し元クラス
     */
    private function __construct($type, string $message = null, $class = null)
    {
        $this->type = $type;
        $this->message = $message;
        $this->class = $class;
    }

    /**
     * @param string|null $message
     * @param string|null $class
     *
     * @return ProcessResult
     */
    public static function warn($message = null, $class = null)
    {
        return new self(self::WARNING, $message, $class);
    }

    /**
     * @param string|null $message
     * @param string|null $class
     *
     * @return ProcessResult
     */
    public static function error($message = null, $class = null)
    {
        return new self(self::ERROR, $message, $class);
    }

    /**
     * @param string|null $message
     * @param string|null $class
     *
     * @return ProcessResult
     */
    public static function success($message = null, $class = null)
    {
        return new self(self::SUCCESS, $message, $class);
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
