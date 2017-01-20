<?php

namespace Eccube\Service\Payment\Method;

class Cash implements PaymentMethod
{
    public function checkout()
    {
        return true;
    }

    public function apply($request)
    {
        return false;
    }

    public function setFormType($form)
    {
        // quiet
    }
}
