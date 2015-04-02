<?php

namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class ShoppingController extends AbstractController
{
    public function index(Application $app, Request $request)
    {
        $cart = $app['eccube.service.cart'];
        
        // カートに変更がある場合はエラーにする
        if (!$cart->isLocked()) {
            $app->abort("カートが変更されました");
        }
        /** @var Eccube\Service\Order */
        $order = $app['eccube.service.order'];

        /** @var Eccube\Entity\OrderTmp */
        $preOrder = $order->findPreOrder($cart->getPreOrderId()); // 一時受注テーブルから復旧
        if (is_null($preOrder)) {
            // 一時受注テーブルが無いときは、カートから商品を取り出し、一時受注データを生成する(dtb_order_tmp)
            $preOrder = $order->createPreOrder($cart->getProducts());
            // カートへ一時受注IDをセット
            $cart->setPreOrderId($preOrder->getOrderId());
        }
        
        $title = "購入確認 | レジ";
        
        return $app['twig']->render(
                'shopping/index.twig',
                array(
                    'title' => $title,
                    'order' => $preOrder)
        );
    }
    
    // todo 初期化は外に出す
    private function initOrderService(Application $app) {
        $order = $app["eccube.service.order"];
        $customer = $app['session']->get('user');

        // 税金計算する人
        $taxRule = $app['orm.em']->getRepository("\\Eccube\Entity\TaxRule")
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
        $order->addPointCalculator($pointCalculator);
        $order->addPointCalculator($birthPointCalculator);
        
        // 配送料計算する人
        $order->addDeliveryFeeCalculator();
        $order->addPaymentFeeCalculator();
        $order->addSubTotalCalculator();
        $order->addChargeCalculator();
    }
}