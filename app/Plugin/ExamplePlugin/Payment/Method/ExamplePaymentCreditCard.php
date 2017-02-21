<?php

namespace Plugin\ExamplePlugin\Payment\Method;

use Eccube\Service\Payment\PaymentMethod;
use Eccube\Service\Payment\PaymentResult;
use Eccube\Service\Payment\Method\CreditCard;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ExamplePaymentCreditCard extends CreditCard
{
    protected $app;
    protected $request;

    public function checkout()
    {
        // 支払処理の実行. 決済サーバーと通信したり
        // 支払処理をしたら PaymentResult を返す
        return new PaymentResult();
    }

    public function apply()
    {
        // shopping/examplePayment に移譲する
        $subRequest = Request::create(
            $this->app->path('shopping/examplePayment'),
            $this->request->getMethod(),
            [],
            $this->request->cookies->all(),
            [],
            $this->request->server->all()
        );
        if ($this->request->getSession()) {
            $subRequest->setSession($this->request->getSession());
        }
        return $this->app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
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
