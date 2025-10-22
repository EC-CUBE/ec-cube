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
    public const ERROR = 'ERROR';
    public const WARNING = 'WARNING';
    public const SUCCESS = 'SUCCESS';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $message;

    /**
     * @var string|null
     */
    protected $class;

    /**
     * @param string|null $class 呼び出し元クラス
     */
    private function __construct(string $type, ?string $message = null, ?string $class = null)
    {
        $this->type = $type;
        $this->message = $message;
        $this->class = $class;
    }

    /**
     * @return ProcessResult
     */
    public static function warn(?string $message = null, ?string $class = null): ProcessResult
    {
        return new self(self::WARNING, $message, $class);
    }

    /**
     * @return ProcessResult
     */
    public static function error(?string $message = null, ?string $class = null): ProcessResult
    {
        return new self(self::ERROR, $message, $class);
    }

    /**
     * @return ProcessResult
     */
    public static function success(?string $message = null, ?string $class = null): ProcessResult
    {
        return new self(self::SUCCESS, $message, $class);
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->type === self::ERROR;
    }

    /**
     * @return bool
     */
    public function isWarning(): bool
    {
        return $this->type === self::WARNING;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->type === self::SUCCESS;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}
