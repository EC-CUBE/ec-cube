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
use Eccube\Service\Payment\PaymentDispatcher;
use Eccube\Service\Payment\PaymentMethodInterface;
use Eccube\Service\Payment\PaymentResult;
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
    #[\Override]
    abstract public function verify(): PaymentResult|bool;

    /**
     * {@inheritdoc}
     */
    #[\Override]
    abstract public function checkout(): PaymentResult;

    /**
     * {@inheritdoc}
     */
    #[\Override]
    abstract public function apply(): PaymentDispatcher|bool;

    /**
     * {@inheritdoc}
     */
    #[\Override]
    abstract public function setFormType(FormInterface $form): PaymentMethodInterface;

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function setOrder(Order $Order): PaymentMethodInterface
    {
        $this->Order = $Order;

        return $this;
    }
}
