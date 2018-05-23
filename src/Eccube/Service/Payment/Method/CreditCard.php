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

abstract class CreditCard implements PaymentMethod
{
    abstract public function checkout();

    abstract public function apply();

    abstract public function setFormType($form);
}
