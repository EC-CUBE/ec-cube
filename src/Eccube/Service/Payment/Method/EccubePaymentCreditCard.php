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

use Eccube\Service\Payment\PaymentResult;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class EccubePaymentCreditCard extends CreditCard
{
    protected $app;
    protected $request;

    public function checkout()
    {
        // 支払処理をしたら PaymentResult を返す
        return new PaymentResult();
    }

    public function apply()
    {
        // 他のコントローラに移譲等の処理をする
        // $subRequest = Request::create(
        //     $this->app->path('shopping/examplePayment'),
        //     $this->app['request_stack']->getCurrentRequest()->getMethod(),
        //     [],
        //     $this->request->cookies->all(),
        //     [],
        //     $this->request->server->all()
        // );
        // if ($this->request->getSession()) {
        //     $subRequest->setSession($this->request->getSession());
        // }
        // return $this->app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
        return false;
    }

    public function setFormType($form)
    {
        // nothing
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
