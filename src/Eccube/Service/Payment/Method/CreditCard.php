<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\Payment\Method;

use Eccube\Entity\Order;
use Eccube\Service\Payment\PaymentMethodInterface;
use Symfony\Component\Form\FormInterface;

/**
 * クレジットカード払いの基底クラス.
 *
 * クレジットカード決済を実装する場合は, このクラスを継承します.
 */
abstract class CreditCard implements PaymentMethodInterface
{
    /**
     * @var Order
     */
    protected $Order;

    /**
     * {@inheritdoc}
     */
    abstract public function verify();

    /**
     * {@inheritdoc}
     */
    abstract public function checkout();

    /**
     * {@inheritdoc}
     */
    abstract public function apply();

    /**
     * {@inheritdoc}
     */
    abstract public function setFormType(FormInterface $form);

    /**
     * {@inheritdoc}
     */
    public function setOrder(Order $Order)
    {
        $this->Order = $Order;

        return $this;
    }
}
