<?php

namespace Eccube\Service\Payment\Method;
use Eccube\Service\Payment\PaymentMethod;
use Eccube\Service\Payment\PaymentResult;

class EccubePaymentCreditCard extends CreditCard
{
   public function checkout()
    {
        // 支払処理をしたら PaymentResult を返す
        return new PaymentResult();
    }

    public function apply()
    {
        // forward
        // return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
        return false;
    }
    public function setFormType($form)
    {
        // nothing
    }
}
