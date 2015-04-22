<?php

namespace Plugin\SamplePayment\Service;

use Eccube\Application;

class PaymentService
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function sample()
    {
        return 'sample payment';
    }
}