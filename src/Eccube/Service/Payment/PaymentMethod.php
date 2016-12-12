<?php

namespace Eccube\Service\Payment;

interface PaymentMethod
{
    public function checkout();

    public function apply($request);

    public function setFormType($form);

    public function setApplication($app);
}
