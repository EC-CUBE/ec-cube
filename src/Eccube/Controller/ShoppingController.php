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
}