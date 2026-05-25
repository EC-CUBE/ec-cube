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

namespace Eccube\Service\PurchaseFlow\Processor;

use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Service\PurchaseFlow\ItemHolderPostValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * 配送方法変更により支払方法が変更されたかどうかを検知するバリデータ.
 */
class PaymentChangePostValidator extends ItemHolderPostValidator
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * PaymentChangePostValidator constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     */
    public function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        /* @var Order $Order */
        $Order = $itemHolder;
        $request = $this->requestStack->getCurrentRequest();
        $requestData = $request->request->all();

        // 配送方法の変更によって選択していた支払方法が使用できなくなった場合、OrderTypeで支払方法が変更されている
        if (isset($requestData['_shopping_order']['Payment'])) {

            if (!is_null($Order->getPayment()) && $Order->getPayment()->getId() != $requestData['_shopping_order']['Payment']) {
                if ($Order->getPayment()) {
                    $this->throwInvalidItemException(trans('purchase_flow.payment_method_changed', ['%name%' => $Order->getPayment()->getMethod()]), null, true);
                }
            }
        }
    }
}
