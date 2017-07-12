<?php

namespace Eccube\Service\PurchaseFlow;


use Eccube\Application;

class ProcessResult
{

    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const SUCCESS = 'SUCCESS';

    protected $type;
    protected $message;

    private function __construct($type, $message = null, $messageArgs)
    {
        $this->type = $type;
        $this->message = Application::getInstance()->trans($message, $messageArgs);
    }

    public static function warn($message, $messageArgs = [])
    {
        return new self(self::WARNING, $message, $messageArgs);
    }

    public static function error($message, $messageArgs = [])
    {
        return new self(self::ERROR, $message, $messageArgs);
    }

    public static function success()
    {
        return new self(self::SUCCESS, null, []);
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