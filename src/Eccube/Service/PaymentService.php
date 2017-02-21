<?php
namespace Eccube\Service;

use Eccube\Application;
use Eccube\Service\Payment\PaymentMethod;

class PaymentService
{
    /** @var \Eccube\Application */
    protected $app;

    public function dispatch(PaymentMethod $method)
    {
        // PaymentMethod->apply に処理を移譲する
        // 別のコントローラに forward など
        $request = $this->app['request_stack']->getCurrentRequest();
        return $method->apply($request);
    }

    public function doCheckout(PaymentMethod $method)
    {
        // ここに EventDispatcher を仕掛けておけば,いろいろできるけど,やりすぎないで.
        $PaymentResult = $method->checkout();
        return $PaymentResult;
    }

    public function setApplication(Application $app)
    {
        $this->app = $app;
        return $this;
    }

}
