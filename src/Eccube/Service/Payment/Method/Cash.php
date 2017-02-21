<?php

namespace Eccube\Service\Payment\Method;

use Eccube\Service\Payment\PaymentMethod;
use Eccube\Service\Payment\PaymentResult;

class Cash implements PaymentMethod
{
    protected $app;
    protected $request;

    public function checkout()
    {
        return new PaymentResult();
    }

    public function apply($request)
    {
        return false;
    }

    public function setFormType($form)
    {
        // quiet
    }

    public function setApplication($app)
    {
        $this->app = $app;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }
}
