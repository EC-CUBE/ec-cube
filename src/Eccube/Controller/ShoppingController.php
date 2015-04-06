<?php

namespace Eccube\Controller;

use Eccube\Application;

class ShoppingController extends AbstractController
{
    public function index(Application $app)
    {
        if (!$app['security']->isGranted('ROLE_USER')) {
            // TODO ログイン/非会員購入など実装
            $app->abort("ログインが必要です。");
        }

        /** @var Eccube\Entity\Customer */
        $customer = $app['user'];
        /** @var Eccube\Service\Cart */
        $cart = $app['eccube.service.cart'];
        
        // カートに変更がある場合はエラーにする
        if (!$cart->isLocked()) {
            // TODO エラーハンドリングする
            $app->abort("カートが変更されました");
        }
        
        /** @var Eccube\Service\Order */
        $orderService = $app['eccube.service.order'];
        $this->initOrderService($orderService);

        /** @var Eccube\Entity\Order */
        $order = $orderService->findPreOrder($cart->getPreOrderId());
        
        // 購入途中の受注がない場合は新規受注を作成
        if (is_null($order)) {
            $order = $orderService->createPreOrder($cart->getProducts());
            $cart->setPreOrderId($order->getOrderId());
        }
        // 受注の金額計算
        $orderService->calculate($order->getOrderId());
        
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
    public function deliv()
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
    // todo 初期化は外に出す
    private function initOrderService(\Eccube\Service\Order\Order $orderService) {
        $order = $app["eccube.service.order"];
        $customer = $app['user'];

        // 税金計算する人
        $taxRule = $app['orm.em']->getRepository("Eccube\Entity\TaxRule")
                ->findCurrentRule();
        $taxCalculator = new TaxCalculator();
        $taxCalculator->setTaxRule($taxRule);
        $taxCalculator->setPrefId($customer->getPrefId());
        $taxCalculator->setCountryId($customer->getCountryId());
        $order->addTaxCalculator($taxCalculator);
        
        // 加算ポイント計算
        $baseInfo = $app['orm.em']->getRepository("\\Eccube\Entity\BaseInfo")
                ->find(1);
        // 通常ポイント
        $pointCalculator = new PointCalculator();
        $pointCalculator->setBaseInfo($baseInfo);
        // 誕生日ポイント
        $birthPointCalculator = new BirthPointCalculator();
        $birthPointCalculator->setBaseInfo($baseInfo);
        $birthPointCalculator->setCustomer($customer);
        $order->addPointCalculator($pointCalculator);
        $order->addPointCalculator($birthPointCalculator);
        
        // 配送料計算する人
        $order->addDeliveryFeeCalculator();
        $order->addPaymentFeeCalculator();
        $order->addSubTotalCalculator();
        $order->addChargeCalculator();
    }
}