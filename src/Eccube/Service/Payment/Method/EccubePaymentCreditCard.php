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

use Eccube\Service\Payment\PaymentDispatcher;
use Eccube\Service\Payment\PaymentResult;
use Symfony\Component\Form\FormInterface;

class EccubePaymentCreditCard extends CreditCard
{
    protected $request;

    /**
     * {@inheritdoc}
     */
    public function checkout()
    {
        // 支払処理をしたら PaymentResult を返す
        return new PaymentResult();
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        // 他のコントローラに移譲等の処理をする
        $dispatcher = new PaymentDispatcher();
        $dispatcher->setForward(true);
        $dispatcher->setRoute('shopping_***');

        return $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormType(FormInterface $form)
    {
        // nothing
    }

    /**
     * {@inheritdoc}
     */
    public function verify()
    {
        // 有効性チェック等の処理をする
        return new PaymentResult();
    }
}
