<?php

namespace Eccube\Service\Payment\Method;

use Eccube\Service\Payment\PaymentMethod;

abstract class CreditCard implements PaymentMethod
{
    abstract public function checkout();

    abstract public function apply();

    abstract public function setFormType($form);
}
