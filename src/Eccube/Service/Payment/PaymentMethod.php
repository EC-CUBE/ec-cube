<?php

namespace Eccube\Service\Payment;

interface PaymentMethod
{
    public function checkout();

    public function apply();

    public function setFormType($form);
}
