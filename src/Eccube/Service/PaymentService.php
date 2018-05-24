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

use Eccube\Service\Payment\PaymentMethod;
use Symfony\Component\HttpFoundation\RequestStack;

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

    public function dispatch(PaymentMethod $method)
    {
        // PaymentMethod->apply に処理を移譲する
        // 別のコントローラに forward など
        $request = $this->requestStack->getCurrentRequest();

        return $method->apply($request);
    }

    public function doCheckout(PaymentMethod $method)
    {
        // ここに EventDispatcher を仕掛けておけば,いろいろできるけど,やりすぎないで.
        $PaymentResult = $method->checkout();

        return $PaymentResult;
    }
}
