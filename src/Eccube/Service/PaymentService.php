<?php
namespace Eccube\Service;

use Eccube\Annotation\Inject;
use Eccube\Service\Payment\PaymentMethod;
use Symfony\Component\HttpFoundation\RequestStack;

class PaymentService
{
    /**
     * @Inject("request_stack")
     * @var RequestStack
     */
    protected $requestStack;

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
