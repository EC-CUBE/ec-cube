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

namespace Plugin\SamplePayment\Service\Method;

use Eccube\Entity\Order;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Service\Payment\PaymentDispatcher;
use Eccube\Service\Payment\PaymentMethod;
use Eccube\Service\Payment\PaymentResult;
use Symfony\Component\Form\FormTypeInterface;

class CreditCard implements PaymentMethod
{
    /**
     * @var Order
     */
    protected $Order;

    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @return PaymentResult
     */
    public function checkout()
    {
        // TODO トークンから仮売上あげる
        // エラーだったらどうするか？
        // とりあえずShoppingExceptionなげればエラーにはできる
    }

    /**
     * @return PaymentDispatcher
     */
    public function apply()
    {
        // TODO 決済処理中ステータスを定数化
        $Status = $this->orderStatusRepository->find(9);
        $this->Order->setOrderStatus($Status);

        // TODO 特になにもする必要はない
    }

    /**
     * @param FormTypeInterface
     *
     * TODO FormTypeInterface -> FormInterface
     */
    public function setFormType(FormTypeInterface $form)
    {
        $this->Order = $form->getData();

        // TODO Orderエンティティにトークンが保持されているのでフォームは不要
        // TODO フォームよりOrderがほしい
    }
}
