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
use Eccube\Service\Payment\PaymentMethod;
use Eccube\Service\Payment\PaymentResult;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * PaymentService
 *
 * 必要に応じて決済代行会社ごとに継承して実装する
 */
class PaymentService
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * PaymentService constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return PaymentDispatcher
     */
    public function dispatch(PaymentMethod $method)
    {
        // PaymentMethod->apply に処理を移譲する
        // 別のコントローラに forward など

        return $method->apply(); // Order 渡す
    }

    /**
     * @return PaymentResult
     */
    public function doVerify(PaymentMethod $method)
    {
        // 注文入力画面→確認画面での入力チェックに利用する
        // 主にカードの有効性チェック等を行なう
        $PaymentResult = $method->verify();

        return $PaymentResult;
    }

    /**
     * @return PaymentResult
     */
    public function doCheckout(PaymentMethod $method)
    {
        // ここに EventDispatcher を仕掛けておけば,いろいろできるけど,やりすぎないで.
        $PaymentResult = $method->checkout();

        return $PaymentResult;
    }
}
