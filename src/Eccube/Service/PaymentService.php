<?php
namespace Eccube\Service;

use Eccube\Service\Payment\PaymentMethod;

class PaymentService
{

    public function dispatch(PaymentMethod $method)
    {
        // PaymentMethod->apply に処理を移譲する
        // 別のコントローラに forward など
        $request = null;
        return $method->apply($request);
    }

    public function doCheckout(PaymentMethod $method)
    {
        // ここに EventDispatcher を仕掛けておけば,いろいろできるけど,やりすぎないで.
        $PaymentResult = $method->checkout();
        return $PaymentResult;
    }
}
