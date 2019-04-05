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

namespace Eccube\Service\Payment;

use Eccube\Entity\Order;
use Symfony\Component\Form\FormInterface;

/**
 * PaymentMethodInterface
 *
 * 必要に応じて決済手段ごとに実装する
 */
interface PaymentMethodInterface
{
    /**
     * 決済の妥当性を検証し, 検証結果を返します.
     *
     * 主にクレジットカードの有効性チェック等を実装します.
     *
     * @return PaymentResult
     */
    public function verify();

    /**
     * 決済を実行し, 実行結果を返します.
     *
     * 主に決済の確定処理を実装します.
     *
     * @return PaymentResult
     */
    public function checkout();

    /**
     * 注文に決済を適用します.
     *
     * PaymentDispatcher に遷移先の情報を設定することで, 他のコントローラに処理を移譲できます.
     *
     * @return PaymentDispatcher
     */
    public function apply();

    /**
     * PaymentMethod の処理に必要な FormInterface を設定します.
     *
     * @param FormInterface
     *
     * @return PaymentMethod
     */
    public function setFormType(FormInterface $form);

    /**
     * この決済を使用する Order を設定します.
     *
     * @param Order
     *
     * @return PaymentMethod
     */
    public function setOrder(Order $Order);
}
