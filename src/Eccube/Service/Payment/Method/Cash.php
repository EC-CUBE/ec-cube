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

use Eccube\Entity\Order;
use Eccube\Service\Payment\PaymentMethod;
use Eccube\Service\Payment\PaymentResult;
use Symfony\Component\Form\FormInterface;

class Cash implements PaymentMethod
{
    /**
     * {@inheritdoc}
     */
    public function checkout()
    {
        return new PaymentResult();
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormType(FormInterface $form)
    {
        // quiet
    }

    /**
     * {@inheritdoc}
     */
    public function verify()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(Order $Order)
    {
        // quiet
    }
}
