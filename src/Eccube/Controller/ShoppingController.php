<?php

namespace Eccube\Controller;

use Eccube\Application;

class ShoppingController extends AbstractController
{
    public function index(Application $app)
    {
        if (!$app['security']->isGranted('ROLE_USER')) {
            // TODO ログイン/非会員購入など実装
            //$app->abort("ログインが必要です。");
        }

        /** @var $customer \Eccube\Entity\Customer */
        $customer = $app['user'];
        /** @var $cart \Eccube\Service\Cart */
        $cart = $app['eccube.service.cart'];

        // カートに変更がある場合はエラーにする
        if (!$cart->isLocked()) {
            // TODO エラーハンドリングする
            $app->abort("カートが変更されました");
        }
        
        /** @var $orderService \Eccube\Service\Order\Order */
        $orderService = $app['eccube.service.order'];
        $this->initOrderService($orderService);

        /** @var $order \Eccube\Entity\Order */
        $order = $orderService->findPreOrderByOrderId($cart->getPreOrderId());
        
        // 購入途中の受注がない場合は新規受注を作成
        if (is_null($order)) {
            $order = $orderService->createPreOrder($cart->getProducts());
            $cart->setPreOrderId($order->getOrderId());
        }
        // 受注の金額計算
        $orderService->setOrder($order);
        $orderService->setOrderDetails($orderService->findOrderDetailsByOrderId($order->getOrderId()));
        $orderService->calc();
        $orderService->registerOrder($orderService->getOrder(), $orderService->getOrderDetais());
        
        $title = "購入確認 | レジ";
        
        return $app['twig']->render(
                'shopping/index.twig',
                array(
                    'title' => $title,
                    'order' => $order)
        );
    }
    
    // 購入処理
    public function confirm(Application $app)
    {
        
    }
    // 購入完了
    public function complete(Application $app)
    {
        
    }
    // 配送業者設定
    public function delivery()
    {
        
    }
    // ポイント設定
    public function point()
    {
        
    }
    // 配送先設定
    public function shipping()
    {
        
    }

    private function initOrderService(\Eccube\Service\Order\Order $orderService) {
        // 税率
        $taxCalculator = new \Eccube\Service\Order\TaxCalculator($this->app);
        $taxCalculator->setTaxRule($this->app['eccube.repository.baseinfo']->findCurrentRule());
        $orderService->addCalcurator($taxCalculator);

        // ポイント
        $pointCalculator = new \Eccube\Service\Order\PointCalculator($this->app);
        $pointCalculator->setBaseInfo($this->app['eccube.repository.baseinfo']->find(1));
        $pointCalculator->setCustomer($this->app['user']);
        $orderService->addCalcurator($taxCalculator);
    }
}