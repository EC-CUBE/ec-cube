<?php

namespace Eccube\Service;


class ProcessResult
{
    protected $error = false;

    protected $message;

    private function __construct($error, $message = null)
    {
        $this->error = $error;
        $this->message = $message;
    }

    public static function fail($message)
    {
        return new self(true, $message);
    }

    public static function success()
    {
        return new self(false);
    }

    public function isError()
    {
        return $this->error;
    }

    public function getErrorMessage()
    {
        return $this->message;
    }
}