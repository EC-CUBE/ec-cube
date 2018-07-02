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
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Symfony\Component\Form\FormInterface;

class Cash implements PaymentMethod
{
    /** @var Order */
    private $Order;

    /** @var FormInterface */
    private $form;

    /** @var $purchaseFlow */
    private $purchaseFlow;

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
     * @throws \Eccube\Service\PurchaseFlow\PurchaseException
     */
    public function checkout()
    {
        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->commit($this->Order, new PurchaseContext());

        return new PaymentResult();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Eccube\Service\PurchaseFlow\PurchaseException
     */
    public function apply()
    {
        $purchaseFlow = new PurchaseFlow();
        $purchaseFlow->prepare($this->Order, new PurchaseContext());

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormType(FormInterface $form)
    {
        $this->form = $form;
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
        $this->Order = $Order;
    }
}
