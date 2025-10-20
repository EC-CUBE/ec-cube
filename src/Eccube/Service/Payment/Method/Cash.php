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
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseException;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Symfony\Component\Form\FormInterface;

/**
 * 銀行振込, 代金引き換えなど, 主に現金を扱う支払い方法を扱うクラス.
 */
class Cash implements PaymentMethodInterface
{
    /** @var Order */
    private $Order;

    /** @var PurchaseFlow */
    private $purchaseFlow;

    /** @var FormInterface */
    private $form;

    /**
     * Cash constructor.
     *
     * @param PurchaseFlow $shoppingPurchaseFlow
     */
    public function __construct(PurchaseFlow $shoppingPurchaseFlow)
    {
        $this->purchaseFlow = $shoppingPurchaseFlow;
    }

    /**
     * {@inheritdoc}
     *
     * @throws PurchaseException
     */
    #[\Override]
    public function checkout(): PaymentResult
    {
        $this->purchaseFlow->commit($this->Order, new PurchaseContext());

        $result = new PaymentResult();
        $result->setSuccess(true);

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @throws PurchaseException
     */
    #[\Override]
    public function apply(): PaymentDispatcher|bool
    {
        $this->purchaseFlow->prepare($this->Order, new PurchaseContext());

        return false;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function setFormType(FormInterface $form): PaymentMethodInterface
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return FormInterface
     */
    public function getFormType(): FormInterface
    {
        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function verify(): PaymentResult|bool
    {
        return false;
    }

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
