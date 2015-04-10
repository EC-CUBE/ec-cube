<?php

namespace Eccube\Controller;

use Eccube\Application;
use \Doctrine\Common\Util\Debug;

class ShoppingController extends AbstractController
{
    /** @var \Eccube\Application */
    protected $app;
    /** @var \Eccube\Service\CartService */
    protected $cartService;
    /** @var \Eccube\Repository\OrderRepository */
    protected $orderRepository;
    /** @var \Eccube\Service\Order\Order */
    protected $orderService;
    /** @var \Symfony\Component\Form\Form */
    protected $form;

    protected function test()
    {
        /** @var $cart \Eccube\Service\CartService */
        $cartService = $app['eccube.service.cart'];
        // カートに商品追加(テスト用)
        $cartService->clear();
        $cartService->addProduct(9);
        $cartService->addProduct(9);
        $cartService->addProduct(10);
        $cartService->addProduct(10);
        $cartService->addProduct(2);
        $cartService->lock();
        //$cartService->setPreOrderId(10078);
    }

    protected function init($app)
    {
        $this->app = $app;
        $this->cartService = $app['eccube.service.cart'];
        $this->orderRepository = $app['eccube.repository.order'];
        $this->orderService = $app['eccube.service.order'];
        $this->form = $app['form.factory']
            ->createBuilder('shopping')
            ->getForm();
    }

    public function index(Application $app)
    {
        $this->init($app);

        // todo ログイン/非会員購入
        if (!$app['security']->isGranted('ROLE_USER')) {
            //$app->abort("ログインが必要です。");
        }

        // カートに変更がある場合はエラーにする
        if (!$this->cartService->isLocked()) {
            // todo エラー表示
            //$app->abort("カートが変更されました");
        }

        // 受注関連情報を取得
        $preOrderId = $this->cartService->getPreOrderId();
        $order = $this->orderRepository
                      ->findOneBy(array("id" => $preOrderId)); // todo nullでfindするとorm exception

        // 初回アクセスの場合は受注データを作成
        if (is_null($order)) {
            $order = $this->orderService
                          ->registerPreOrderFromCart(
                              $this->cartService->getProducts(),
                              $this->app['user']);
            $this->cartService->setPreOrderId($order->getId());
        }

        // 受注関連情報を最新状態に更新
        $this->app['orm.em']->refresh($order);

        // todo いったん複数配送なしで実装
        $shipping = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Shipping')
            ->findOneBy(array("order_id" => $order->getId()));
        $shipmentItems = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\ShipmentItem')
            ->findBy(array("order_id" => $order->getId()));

        // todo 受注情報の金額計算
        // todo ポイント設定
        // todo 配送業者設定
        // todo 支払方法設定
        // todo 配送先設定

        $title = "ご注文内容の確認";
        
        return $app['twig']->render(
                'shopping/index.twig',
                array(
                    'form' => $this->form->createView(),
                    'title' => $title,
                    'order' => $order,
                    'shipping' => $shipping,
                    'shipmentItems' => $shipmentItems)
        );
    }
    
    // 購入処理
    public function confirm(Application $app)
    {
        $this->init($app);

        if ($app['request']->getMethod() === HTTP_REQUEST_METHOD_POST) {
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                $preOrderId = $this->cartService->getPreOrderId();
                $this->orderService->commit($preOrderId);
                return $app->redirect($app['url_generator']->generate('shopping_complete'));
            }
        }
    }

    // 購入完了画面表示
    public function complete(Application $app)
    {
        return $app['twig']->render(
            'shopping/complete.twig', array()
        );
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
}