<?php

namespace Eccube\Controller;

use Doctrine\ORM\EntityManager;
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
    /** @var \Eccube\Service\OrderService */
    protected $orderService;
    /** @var \Symfony\Component\Form\Form */
    protected $form;

    public function test(Application $app)
    {
        /** @var $cartService \Eccube\Service\CartService */
        $cartService = $app['eccube.service.cart'];
        // カートに商品追加(テスト用)
        $cartService->clear();
        $cartService->addProduct(9);
        $cartService->addProduct(9);
        $cartService->addProduct(10);
        $cartService->addProduct(10);
        $cartService->addProduct(2);
        $cartService->lock();
        return $app->redirect($app['url_generator']->generate('shopping'));
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

    // todo カート変更チェック
    protected function verifyCartAndAbort()
    {
        if (!$this->cartService->isLocked()) {
            // エラー処理
        }
    }

    // todo ログインチェック
    protected function verifyCustomerAndAbort()
    {
        if (!$this->app['security']->isGranted('ROLE_USER')) {
            // 非会員購入対応
        }
    }

    public function index(Application $app)
    {
        $this->init($app);

        // ログインチェック
        $this->verifyCustomerAndAbort();
        // カート変更チェック
        $this->verifyCartAndAbort();

        // 受注関連情報を取得
        $preOrderId = $this->cartService->getPreOrderId();
        $order = null;
        if (!is_null($preOrderId)) {
            $order = $this->orderRepository->find($preOrderId);
        }
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

        // todo 複数配送設定の対応
        $shipping = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Shipping')
            ->findOneBy(array("order_id" => $order->getId()));
        $shipmentItems = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\ShipmentItem')
            ->findBy(array("order_id" => $order->getId()));

        // todo 受注情報の金額計算
        //$this->orderService->calcurate($order);
        //$deliveries = $this->orderService->findDeliveriesByOrder($order);
        //$payments = $this->orderService->findPaymentByOrder($order);

        // 配送業者選択
        $deliveries = $this->findDeliveriesFromOrderDetails($order->getOrderDetails());
        $this->form->add('delivery', 'entity', array(
                    'class' => 'Eccube\Entity\Deliv',
                    'property' => 'name',
                    'choices' => $deliveries,
                    'data' => $order->getDeliv()));

        // 支払い方法選択
        $paymentOptions = $order->getDeliv()->getPaymentOptions();
        $payments = array();
        foreach ($paymentOptions as $paymentOption) {
            $payments[] = $paymentOption->getPayment();
        }
        $this->form->add('payment', 'entity', array(
                'class' => 'Eccube\Entity\Payment',
                'property' => 'method',
                'choices' => $payments,
                'data' => $order->getPayment()));

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

        if ('POST' === $app['request']->getMethod()) {
            $this->form->handleRequest($app['request']);
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                /** @var $order \Eccube\Entity\Order */
                $order = $this->orderRepository->find($this->cartService->getPreOrderId());
                $order->setMessage($data['message']);
                $this->orderService->commit($order);
                $this->cartService->clear();
                return $app->redirect($app['url_generator']->generate('shopping_complete'));
            }
        }

        // todo エラーハンドリング
        return $app->redirect($app['url_generator']->generate('cart'));
    }

    // 購入完了画面表示
    public function complete(Application $app)
    {
        $title = "ご購入完了";
        $baseInfo = $app['eccube.repository.base_info']->find(1);
        return $app['twig']->render(
            'shopping/complete.twig', array(
                'title' => $title,
                'baseInfo' => $baseInfo
            )
        );
    }

    // 配送業者設定
    public function delivery(Application $app)
    {
        $this->init($app);
        $this->verifyCartAndAbort();

        if ('POST' === $app['request']->getMethod()) {
            $this->form->handleRequest($app['request']);
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                /** @var $order \Eccube\Entity\Order */
                $order = $this->orderRepository->find($this->cartService->getPreOrderId());
                // 配送業者をセット
                $delivery = $data['delivery'];
                $deliveryFees = $delivery->getDelivFees();
                $order->setDeliv($delivery);
                $order->setDelivFee($deliveryFees[0]->getFee());
                // 支払い情報をセット
                $paymentOptions = $delivery->getPaymentOptions();
                $payment = $paymentOptions[0]->getPayment();;
                $order->setPayment($payment);
                $order->setPaymentMethod($payment->getMethod());
                $order->setCharge($payment->getCharge());
                $app['orm.em']->persist($order);
                $app['orm.em']->flush();
            }
        }
        return $app->redirect($app['url_generator']->generate('shopping'));
    }

    // 支払い方法設定
    public function payment(Application $app)
    {
        $this->init($app);
        $this->verifyCartAndAbort();

        if ('POST' === $app['request']->getMethod()) {
            $this->form->handleRequest($app['request']);
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                /** @var $order \Eccube\Entity\Order */
                $order = $this->orderRepository->find($this->cartService->getPreOrderId());
                // 支払い情報をセット
                $payment = $data['payment'];
                $order->setPayment($payment);
                $order->setPaymentMethod($payment->getMethod());
                $order->setCharge($payment->getCharge());
                $app['orm.em']->persist($order);
                $app['orm.em']->flush();
            }
        }
        return $app->redirect($app['url_generator']->generate('shopping'));
    }

    // ポイント設定
    public function point(Application $app)
    {
        $this->init($app);
        $this->verifyCartAndAbort();

        /** @var $order \Eccube\Entity\Order */
        $order = $this->orderRepository->find($this->cartService->getPreOrderId());
        $point = $order->getUsePoint();
        $pointFlg = $point > 0 ? 1 : 0;

        $form = $app['form.factory']->createBuilder()
            ->add('point_flg', 'choice', array(
                    'required' => true,
                    'choices'  => array(0 => '使用しない', 1 => '使用する'),
                    'expanded' => true,
                    'data' => $pointFlg))
            ->add('point', 'integer', array(
                    'required' => true,
                    'data' => $point))
            ->getForm();

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                $pointFlg = $data['point_flg'];
                $point = $data['point'];
                if ($pointFlg == 0) {
                    $point = 0;
                }
                $order->setUsePoint($point);
                $app['orm.em']->persist($order);
                $app['orm.em']->flush();
                return $app->redirect($app['url_generator']->generate('shopping'));
            }
        }
        return $app['twig']->render(
            'shopping/point.twig', array(
                'title' => 'ポイント設定',
                'order' => $order,
                'form' => $form->createView()
            )
        );
    }

    // お届け先設定
    public function shipping(Application $app)
    {
        $this->init($app);
        $this->verifyCartAndAbort();

        $customer = $app['user'];
        $addresses = array();
        $addresses[0] = $customer;

        $qb = $this->app['orm.em']->createQueryBuilder();
        $otherAddrs = $qb->select("od")
            ->from("\\Eccube\\Entity\\OtherDeliv", "od")
            ->where('od.Customer = :customer')
            ->orderBy("od.id", "ASC")
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getResult();
        foreach ($otherAddrs as $otherAddr) {
            $addresses[$otherAddr->getId()] = $otherAddr;
        }

        $form = $app['form.factory']->createBuilder()
            ->add('addresses', 'choice', array(
                'choices'  => $addresses,
                'expanded' => true,
                'data' => 0))
            ->getForm();

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                /** @var $order \Eccube\Entity\Order */
                $order = $this->orderRepository->find($this->cartService->getPreOrderId());
                /** @var $shipping \Eccube\Entity\Shipping */
                $shipping = $this->app['orm.em']
                    ->getRepository('\Eccube\Entity\Shipping')
                    ->findOneBy(array("order_id" => $order->getId()));
                $addressId = $data['addresses'];
                $address = null;
                if ($addressId == 0) {
                    $address = $customer;
                } else {
                    $qb = $this->app['orm.em']->createQueryBuilder();
                    $address = $qb->select("od")
                        ->from("\\Eccube\\Entity\\OtherDeliv", "od")
                        ->where('od.id = :id')
                        ->andWhere('od.Customer = :customer')
                        ->setParameter('id', $addressId)
                        ->setParameter('customer', $customer)
                        ->getQuery()
                        ->getSingleResult();
                }
                $shipping
                    ->setName01($address->getName01())
                    ->setName02($address->getName02())
                    ->setKana01($address->getKana02())
                    ->setKana02($address->getKana02())
                    ->setCompanyName($address->getCompanyName())
                    ->setTel01($address->getTel01())
                    ->setTel02($address->getTel02())
                    ->setTel03($address->getTel03())
                    ->setFax01($address->getFax01())
                    ->setFax02($address->getFax02())
                    ->setFax03($address->getFax03())
                    ->setZip01($address->getZip01())
                    ->setZip02($address->getZip02())
                    ->setPref($address->getPref())
                    ->setAddr01($address->getAddr01())
                    ->setAddr02($address->getAddr02());
                // 配送先を更新
                $app['orm.em']->persist($shipping);
                $app['orm.em']->flush();
                return $app->redirect($app['url_generator']->generate('shopping'));
            }
        }

        return $app['twig']->render(
            'shopping/shipping.twig', array(
                'form'  => $form->createView(),
                'title' => 'お届け先設定',
            )
        );
    }

    public function shipping_multiple(Application $app)
    {

        $this->init($app);
        $this->verifyCartAndAbort();
        $order = $this->orderRepository->find($this->cartService->getPreOrderId());
        $orderDetails = $order->getOrderDetails();

        $form = $app['form.factory']->createBuilder()
            ->add('orderDetails', 'collection', array(
                    'required' => true,
                    'choices'  => array(0 => '使用しない', 1 => '使用する'),
                    'expanded' => true,
                    'data' => $pointFlg))
            ->getForm();


        $form = $this->form;
        return $app['twig']->render(
            'shopping/shipping_multiple.twig', array(
                'form'  => $form->createView(),
                'order' => $order,
                'title' => 'お届け先設定(複数配送)',
            )
        );
    }

    // todo サービスに移動
    public function findDeliveriesFromOrderDetails($details)
    {
        $productTypeIds = array();
        foreach ($details as $detail) {
            $productTypeIds[] = $detail->getProductClass()->getProductTypeId();
        }
        $productTypeIds = array_unique($productTypeIds);
        $qb = $this->app['orm.em']->createQueryBuilder();
        $deliveries = $qb->select("d")
            ->from("\\Eccube\\Entity\\Deliv", "d")
            ->where($qb->expr()->in('d.product_type_id', $productTypeIds))
            ->andWhere("d.del_flg = 0")
            ->orderBy("d.rank", "ASC")
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        return $deliveries;
    }
}