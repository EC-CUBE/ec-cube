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

    public function apply()
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
