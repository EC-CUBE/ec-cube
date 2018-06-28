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

namespace Eccube\Service;

use Eccube\Service\Payment\PaymentDispatcher;
use Eccube\Service\Payment\PaymentMethodInterface;
use Eccube\Service\Payment\PaymentResult;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * PaymentService
 *
 * 必要に応じて決済代行会社ごとに継承して実装すること
 */
interface PaymentServiceInterface
{
    /**
     * 他のコントローラに処理を移譲する.
     *
     * 注文確認画面→完了画面で呼ばれます.
     * このメソッドは, 内部で PaymentMethodInterface::apply() をコールし, 処理を移譲します.
     *
     * @return PaymentDispatcher
     */
    public function dispatch(PaymentMethodInterface $method);

    /**
     * 決済の妥当性を検証する.
     *
     * 注文入力画面→確認画面での入力チェックに利用します.
     * 主にクレジットカードの有効性チェックに利用します.
     * このメソッドは, 内部で PaymentMethodInterface::verify() をコールします.
     *
     * @return PaymentResult
     */
    public function doVerify(PaymentMethodInterface $method);

    /**
     * 決済処理を実行します.
     *
     * 注文確認画面→完了画面で呼ばれます.
     * このメソッドは, 内部で PeymentMethodInterface::checkout() をコールします.
     *
     * @return PaymentResult
     */
    public function doCheckout(PaymentMethodInterface $method);
}
