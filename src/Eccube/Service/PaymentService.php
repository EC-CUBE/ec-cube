<?php

namespace Eccube\Service;

use Eccube\Service\Payment\PaymentMethod;
use Symfony\Component\HttpFoundation\RequestStack;

class PaymentService
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

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
