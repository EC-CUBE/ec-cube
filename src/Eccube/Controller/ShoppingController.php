<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Controller;

use Eccube\Application;
use Eccube\Form\Type\ShippingMultiType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class ShoppingController extends AbstractController
{
    /** @var \Eccube\Service\CartService */
    protected $cartService;
    /** @var \Eccube\Repository\OrderRepository */
    protected $orderRepository;
    /** @var \Eccube\Service\OrderService */
    protected $orderService;

    public function test(Application $app)
    {
        // カートに商品追加(テスト用)
        $app['eccube.service.cart']->clear();
        $app['eccube.service.cart']->addProduct(9);
        $app['eccube.service.cart']->addProduct(9);
        $app['eccube.service.cart']->addProduct(10);
        $app['eccube.service.cart']->addProduct(10);
        $app['eccube.service.cart']->addProduct(2);
        $app['eccube.service.cart']->lock();
        $app['eccube.service.cart']->save();

        return $app->redirect($app->url('shopping'));

    }

    protected function init($app)
    {
        $this->cartService = $app['eccube.service.cart'];
        $this->orderRepository = $app['eccube.repository.order'];
        $this->orderService = $app['eccube.service.order'];
    }

    public function index(Application $app, Request $request)
    {
        $cartService = $app['eccube.service.cart'];
        $orderService = $app['eccube.service.order'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        // 受注データを取得
        $preOrderId = $cartService->getPreOrderId();
        $Order = $app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId, 'OrderStatus' => $app['config']['order_processing']));

        // 初回アクセス(受注データがない)の場合は, 受注データを作成
        if (is_null($Order)) {

            // 未ログインの場合は, ログイン画面へリダイレクト.
            if (!$this->isGranted($app)) {
                return $app->redirect($app->url('shopping_login'));
            }

            // ランダムなpre_order_idを作成
            $preOrderId = sha1(uniqid(mt_rand(), true));

            // 受注情報、受注明細情報、お届け先情報、配送商品情報を作成
            $Order = $orderService->registerPreOrderFromCartItems($cartService->getCart()->getCartItems(), $this->getUser($app), $preOrderId);

            $cartService->setPreOrderId($preOrderId);
            $cartService->save();
        } else {
            // 計算処理
            $Order = $orderService->getAmount($Order);
        }

        // 受注関連情報を最新状態に更新
        $app['orm.em']->refresh($Order);

        $form = $app['form.factory']->createBuilder('shopping')->getForm();

        $deliveries = $this->findDeliveriesFromOrderDetails($app, $Order->getOrderDetails());

        $shippings = $Order->getShippings();
        $delivery = $shippings[0]->getDelivery();

        // 配送業社の設定
        $this->setFormDelivery($form, $deliveries, $delivery);

        // お届け日の設定
        $this->setFormDeliveryDate($form, $Order, $app);

        // お届け時間の設定
        $this->setFormDeliveryTime($form, $delivery);

        // 支払い方法選択
        $this->setFormPayment($form, $delivery, $Order->getPayment());

        return $app['view']->render('Shopping/index.twig', array(
                'form' => $form->createView(),
                'Order' => $Order,
        ));
    }

    /**
     * 購入処理
     */
    public function confirm(Application $app, Request $request)
    {

        $cartService = $app['eccube.service.cart'];
        $orderService = $app['eccube.service.order'];
        $orderRepository = $app['eccube.repository.order'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }


        $form = $app['form.factory']->createBuilder('shopping')->getForm();

        $Order = $orderRepository->findOneBy(array('pre_order_id' => $cartService->getPreOrderId()));

        $deliveries = $this->findDeliveriesFromOrderDetails($app, $Order->getOrderDetails());


        // 配送業社の設定
        $shippings = $Order->getShippings();
        $delivery = $shippings[0]->getDelivery();

        // 配送業社の設定
        $this->setFormDelivery($form, $deliveries, $delivery);

        // お届け日の設定
        $this->setFormDeliveryDate($form, $Order, $app);

        // お届け時間の設定
        $this->setFormDeliveryTime($form, $delivery);

        // 支払い方法選択
        $this->setFormPayment($form, $delivery, $Order->getPayment());

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $formData = $form->getData();

                /** @var $Order \Eccube\Entity\Order */

                // トランザクション制御
                $em = $app['orm.em'];
                $em->getConnection()->beginTransaction();
                try {
                    // 商品公開ステータスチェック、商品制限数チェック、在庫チェック
                    $check = $orderService->isOrderProduct($em, $Order);
                    if (!$check) {
                        $em->getConnection()->rollback();
                        $em->close();
                        return $app->redirect($app->url('shopping_error'));
                    }

                    // 受注情報、配送情報を更新
                    $orderService->setOrderUpdate($em, $Order, $formData);
                    // 在庫情報を更新
                    $orderService->setStockUpdate($em, $Order);

                    if ($this->isGranted($app)) {
                        // 会員の場合、購入金額を更新
                        $orderService->setCustomerUpdate($em, $Order, $this->getUser($app));
                    }

                    $em->getConnection()->commit();
                    $em->flush();
                    $em->close();

                } catch (\Exception $e) {
                    $em->getConnection()->rollback();
                    $em->close();
                    return $app->redirect($app->url('shopping_error'));
                }

                // カート削除
                $cartService->clear()->save();

                // メール送信
                $app['eccube.service.mail']->sendOrderMail($Order);

                return $app->redirect($app->url('shopping_complete'));

            }
        }

        return $app->redirect($app->url('cart'));

    }


    /**
     * 購入完了画面表示
     */
    public function complete(Application $app)
    {
        return $app['view']->render('Shopping/complete.twig');
    }


    /**
     * 配送業者選択処理
     */
    public function delivery(Application $app, Request $request)
    {
        $this->init($app);

        $form = $app['form.factory']->createBuilder('shopping')->getForm();

        $Order = $this->orderRepository->findOneBy(array('pre_order_id' => $this->cartService->getPreOrderId()));

        $deliveries = $this->findDeliveriesFromOrderDetails($app, $Order->getOrderDetails());

        $shippings = $Order->getShippings();
        $delivery = $shippings[0]->getDelivery();

        // 配送業社の設定
        $this->setFormDelivery($form, $deliveries, $delivery);

        // お届け日の設定
        $this->setFormDeliveryDate($form, $Order, $app);

        // お届け時間の設定
        $this->setFormDeliveryTime($form, $delivery);

        // 支払い方法選択
        $this->setFormPayment($form, $delivery, $Order->getPayment());

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $data = $form->getData();
                $delivery = $data['delivery'];

                // 配送業者をセット
                $shippings = $Order->getShippings();
                $Shipping = $shippings[0];

                $deliveryFee = $app['eccube.repository.delivery_fee']->findOneBy(array('Delivery' => $delivery, 'Pref' => $Shipping->getPref()));
                $Shipping->setDelivery($delivery);
                $Shipping->setDeliveryFee($deliveryFee);

                // 支払い情報をセット
                $paymentOptions = $delivery->getPaymentOptions();
                $payment = $paymentOptions[0]->getPayment();

                $Order->setPayment($payment);
                $Order->setPaymentMethod($payment->getMethod());
                $Order->setCharge($payment->getCharge());
                $Order->setDeliveryFeeTotal($deliveryFee->getFee());

                $total = $Order->getSubTotal() + $Order->getCharge() + $Order->getDeliveryFeeTotal();

                $Order->setTotal($total);
                $Order->setPaymentTotal($total);

                // 受注関連情報を最新状態に更新
                $app['orm.em']->flush();

                $app['orm.em']->refresh($Order);


                return $app->redirect($app->url('shopping'));

            }
        } else {
            return $app->redirect($app->url('shopping'));
        }

    }

    /**
     * 支払い方法選択処理
     */
    public function payment(Application $app, Request $request)
    {

        $this->init($app);

        $form = $app['form.factory']->createBuilder('shopping')->getForm();

        $Order = $this->orderRepository->findOneBy(array('pre_order_id' => $this->cartService->getPreOrderId()));

        $deliveries = $this->findDeliveriesFromOrderDetails($app, $Order->getOrderDetails());

        $shippings = $Order->getShippings();
        $delivery = $shippings[0]->getDelivery();

        // 配送業社の設定
        $this->setFormDelivery($form, $deliveries, $delivery);

        // お届け日の設定
        $this->setFormDeliveryDate($form, $Order, $app);

        // お届け時間の設定
        $this->setFormDeliveryTime($form, $delivery);

        // 支払い方法選択
        $this->setFormPayment($form, $delivery, $Order->getPayment());

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $data = $form->getData();
                $payment = $data['payment'];

                $Order->setPayment($payment);
                $Order->setPaymentMethod($payment->getMethod());
                $Order->setCharge($payment->getCharge());

                $total = $Order->getSubTotal()  + $Order->getCharge() + $Order->getDeliveryFeeTotal();

                $Order->setTotal($total);
                $Order->setPaymentTotal($total);

                // 受注関連情報を最新状態に更新
                $app['orm.em']->flush();

                $app['orm.em']->refresh($Order);

                return $app->redirect($app->url('shopping'));

            }
        } else {
            return $app->redirect($app->url('shopping'));
        }


    }

    /**
     * お届け先の設定一覧からの選択
     */
    public function shipping(Application $app, Request $request)
    {
        $this->init($app);

        if ('POST' === $request->getMethod()) {
            $address = $request->get('address');

            if (is_null($address)) {
                // 選択されていなければエラー
                return $app['view']->render(
                    'Shopping/shipping.twig',
                    array(
                        'Customer' => $this->getUser($app),
                    )
                );
            }

            // 選択されたお届け先情報を取得
            $customerAddress = $app['eccube.repository.customer_address']->find($address);

            $Order = $app['eccube.repository.order']->findOneBy(array('pre_order_id' => $app['eccube.service.cart']->getPreOrderId()));
            // お届け先情報を更新
            $shippings = $Order->getShippings();
            foreach ($shippings as $shipping) {
                $shipping
                    ->setName01($customerAddress->getName01())
                    ->setName02($customerAddress->getName02())
                    ->setKana01($customerAddress->getKana01())
                    ->setKana02($customerAddress->getKana02())
                    ->setCompanyName($customerAddress->getCompanyName())
                    ->setTel01($customerAddress->getTel01())
                    ->setTel02($customerAddress->getTel02())
                    ->setTel03($customerAddress->getTel03())
                    ->setFax01($customerAddress->getFax01())
                    ->setFax02($customerAddress->getFax02())
                    ->setFax03($customerAddress->getFax03())
                    ->setZip01($customerAddress->getZip01())
                    ->setZip02($customerAddress->getZip02())
                    ->setZipCode($customerAddress->getZip01() . $customerAddress->getZip02())
                    ->setPref($customerAddress->getPref())
                    ->setAddr01($customerAddress->getAddr01())
                    ->setAddr02($customerAddress->getAddr02());
            }

            // 配送先を更新
            $app['orm.em']->flush();

            return $app->redirect($app->url('shopping'));

        }

        return $app['view']->render(
            'Shopping/shipping.twig',
            array(
                'Customer' => $this->getUser($app),
            )
        );
    }


    /**
     * お届け先の設定(非会員でも使用する)
     */
    public function shippingEdit(Application $app, Request $request)
    {

        $cartService = $app['eccube.service.cart'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }


        $Order = $app['eccube.repository.order']->findOneBy(array('pre_order_id' => $app['eccube.service.cart']->getPreOrderId()));
        $shippings = $Order->getShippings();

        // 会員の場合、お届け先情報を新規登録
        if ($this->isGranted($app)) {
            $builder = $app['form.factory']->createBuilder('shopping_shipping');
        } else {
            // 非会員の場合、お届け先を追加
            $builder = $app['form.factory']->createBuilder('shopping_shipping', $shippings[0]);
        }

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                // 会員の場合、お届け先情報を新規登録
                if ($this->isGranted($app)) {
                    $customerAddress = new \Eccube\Entity\CustomerAddress();
                    $customerAddress
                        ->setCustomer($this->getUser($app))
                        ->setName01($data['name01'])
                        ->setName02($data['name02'])
                        ->setKana01($data['kana01'])
                        ->setKana02($data['kana02'])
                        ->setCompanyName($data['company_name'])
                        ->setTel01($data['tel01'])
                        ->setTel02($data['tel02'])
                        ->setTel03($data['tel03'])
                        ->setZip01($data['zip01'])
                        ->setZip02($data['zip02'])
                        ->setZipCode($data['zip01'] . $data['zip02'])
                        ->setPref($data['pref'])
                        ->setAddr01($data['addr01'])
                        ->setAddr02($data['addr02'])
                        ->setDelFlg($app['config']['disabled']);

                    $app['orm.em']->persist($customerAddress);

                }
                foreach ($shippings as $shipping) {
                    $shipping
                        ->setName01($data['name01'])
                        ->setName02($data['name02'])
                        ->setKana01($data['kana01'])
                        ->setKana02($data['kana02'])
                        ->setCompanyName($data['company_name'])
                        ->setTel01($data['tel01'])
                        ->setTel02($data['tel02'])
                        ->setTel03($data['tel03'])
                        ->setZip01($data['zip01'])
                        ->setZip02($data['zip02'])
                        ->setZipCode($data['zip01'] . $data['zip02'])
                        ->setPref($data['pref'])
                        ->setAddr01($data['addr01'])
                        ->setAddr02($data['addr02']);
                }

                // 配送先を更新
                $app['orm.em']->flush();

                // 受注関連情報を最新状態に更新
                $app['orm.em']->refresh($Order);

                return $app->redirect($app->url('shopping'));

            }
        }

        return $app['view']->render('Shopping/shipping_edit.twig', array(
            'form'  => $form->createView(),
        ));

    }


    /**
     * お客様情報の変更(非会員)
     */
    public function customer(Application $app, Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            $data = $request->request->all();
            $Order = $app['eccube.repository.order']->findOneBy(array('pre_order_id' => $app['eccube.service.cart']->getPreOrderId()));

            $Order
                    ->setName01($data['customer_name01'])
                    ->setName02($data['customer_name02'])
                    ->setCompanyName($data['customer_company_name'])
                    ->setTel01($data['customer_tel01'])
                    ->setTel02($data['customer_tel02'])
                    ->setTel03($data['customer_tel03'])
                    ->setZip01($data['customer_zip01'])
                    ->setZip02($data['customer_zip02'])
                    ->setZipCode($data['customer_zip01'] . $data['customer_zip02'])
                    // ->setPref($data['customer_pref'])
                    ->setAddr01($data['customer_addr01'])
                    ->setAddr02($data['customer_addr02']);
            // 配送先を更新
            $app['orm.em']->flush();

            // 受注関連情報を最新状態に更新
            $app['orm.em']->refresh($Order);


            $response = new \Symfony\Component\HttpFoundation\Response(json_encode('OK'));
            $response->headers->set('Content-Type', 'application/json');

            return $response;

        }

    }


    /**
     * ログイン
     */
    public function login(Application $app, Request $request)
    {
        if (!$app['eccube.service.cart']->isLocked()) {
            return $app->redirect($app['url_generator']->generate('cart'));
        }

        if ($app['security']->isGranted('ROLE_USER')) {
            return $app->redirect($app->url('shopping'));
        }
        $session = $request->getSession();

        // ログインエラーがあれば、ここで取得
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        // Sessionにエラー情報があるか確認
        } else if ($session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            // Sessionからエラー情報を取得
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            // 一度表示したらSessionからは削除する
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }


        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $app['form.factory']
            ->createNamedBuilder('', 'customer_login')
            ->getForm();

        return $app['view']->render('Shopping/login.twig', array(
            'error' => $app['security.last_error']($app['request']),
            'form'  => $form->createView(),
        ));
    }

    /**
     * 非会員処理
     */
    public function nonmember(Application $app, Request $request)
    {

        $cartService = $app['eccube.service.cart'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }


        // ログイン済みの場合は, 購入画面へリダイレクト.
        if ($this->isGranted($app)) {
            return $app->redirect($app->url('shopping'));
        }


        $form = $app['form.factory']->createBuilder('nonmember')->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $Customer = new \Eccube\Entity\Customer();
                $Customer
                    ->setName01($data['name01'])
                    ->setName02($data['name02'])
                    ->setKana01($data['kana01'])
                    ->setKana02($data['kana02'])
                    ->setCompanyName($data['company_name'])
                    ->setEmail($data['email'])
                    ->setTel01($data['tel01'])
                    ->setTel02($data['tel02'])
                    ->setTel03($data['tel03'])
                    ->setZip01($data['zip01'])
                    ->setZip02($data['zip02'])
                    ->setZipCode($data['zip01'] . $data['zip02'])
                    ->setPref($data['pref'])
                    ->setAddr01($data['addr01'])
                    ->setAddr02($data['addr02']);

                // 受注関連情報を取得
                $preOrderId = $cartService->getPreOrderId();
                $Order = $app['eccube.repository.order']->findOneBy(array('pre_order_id' => $preOrderId, 'OrderStatus' => $app['config']['order_processing']));

                // 初回アクセス(受注データがない)の場合は, 受注データを作成
                if (is_null($Order)) {
                    // ランダムなpre_order_idを作成
                    $preOrderId = sha1(uniqid(mt_rand(), true));

                    // 受注情報、受注明細情報、お届け先情報、配送商品情報を作成
                    $Order = $app['eccube.service.order']->registerPreOrderFromCartItems($cartService->getCart()->getCartItems(), $Customer, $preOrderId);

                    $cartService->setPreOrderId($preOrderId);
                    $cartService->save();
                }

                // 受注関連情報を最新状態に更新
                $app['orm.em']->refresh($Order);

                $form = $app['form.factory']->createBuilder('shopping')->getForm();

                $deliveries = $this->findDeliveriesFromOrderDetails($app, $Order->getOrderDetails());

                $shippings = $Order->getShippings();
                $delivery = $shippings[0]->getDelivery();

                // 配送業社の設定
                $this->setFormDelivery($form, $deliveries, $delivery);

                // お届け日の設定
                $this->setFormDeliveryDate($form, $Order, $app);

                // お届け時間の設定
                $this->setFormDeliveryTime($form, $delivery);

                // 支払い方法選択
                $this->setFormPayment($form, $delivery, $Order->getPayment());


                return $app['view']->render('Shopping/index.twig', array(
                        'form' => $form->createView(),
                        'Order' => $Order,
                ));

            }
        }

        return $app['view']->render('Shopping/nonmember.twig', array(
            'form'  => $form->createView(),
        ));
    }


    /**
     * 購入エラー画面表示
     */
    public function shoppingError(Application $app)
    {
        return $app['view']->render('Shopping/shopping_error.twig');
    }


    /**
     * 配送業者を取得
     */
    private function findDeliveriesFromOrderDetails($app, $details)
    {

        $productTypes = array();
        foreach ($details as $detail) {
            $productTypes[] = $detail->getProductClass()->getProductType();
        }

        $qb = $app['orm.em']->createQueryBuilder();
        $deliveries = $qb->select("d")
            ->from("\Eccube\Entity\Delivery", "d")
            ->where($qb->expr()->in('d.ProductType', ':productTypes'))
            ->setParameter('productTypes', $productTypes)
            ->andWhere("d.del_flg = :delFlg")
            ->setParameter('delFlg', $app['config']['disabled'])
            ->orderBy("d.rank", "ASC")
            ->getQuery()
            ->getResult();

        return $deliveries;

    }


    /**
     * 配送業者のフォームを設定
     */
    private function setFormDelivery($form, $deliveries, $delivery = null)
    {

        // 配送業社の設定
        $form->add('delivery', 'entity', array(
            'class' => 'Eccube\Entity\Delivery',
            'property' => 'name',
            'choices' => $deliveries,
            'data' => $delivery,
        ));

    }


    /**
     * お届け日のフォームを設定
     */
    private function setFormDeliveryDate($form, $Order, $app)
    {

        // お届け日の設定
        $minDate = 0;
        $deliveryDateFlag = false;

        // 配送時に最大となる商品日数を取得
        foreach ($Order->getOrderDetails() as $detail) {
            $deliveryDate = $detail->getProductClass()->getDeliveryDate();
            if (!is_null($deliveryDate)) {
                if ($minDate < $deliveryDate->getValue()) {
                    $minDate = $deliveryDate->getValue();
                }
                // 配送日数が設定されている
                $deliveryDateFlag = true;
            }
        }

        // 配達最大日数期間を設定
        $deliveryDates = array();

        // 配送日数が設定されている
        if ($deliveryDateFlag) {
            $period = new \DatePeriod (
                new \DateTime($minDate . ' day'),
                new \DateInterval('P1D'),
                new \DateTime($minDate + $app['config']['deliv_date_end_max'] . ' day')
            );
    
            foreach ($period as $day) {
                $deliveryDates[$day->format('Y/m/d')] = $day->format('Y/m/d');
            }
        }
 

        $form->add('deliveryDate', 'choice', array(
            'choices' => $deliveryDates,
            'required' => false,
            'empty_value' => '指定なし',
        ));

    }

    /**
     * お届け時間のフォームを設定
     */
    private function setFormDeliveryTime($form, $delivery)
    {
        // お届け時間の設定
        $form->add('deliveryTime', 'entity', array(
            'class' => 'Eccube\Entity\DeliveryTime',
            'property' => 'deliveryTime',
            'choices' => $delivery->getDeliveryTimes(),
        ));
        
    }

    /**
     * 支払い方法のフォームを設定
     */
    private function setFormPayment($form, $delivery, $payment = null)
    {

        // 支払い方法選択
        $paymentOptions = $delivery->getPaymentOptions();
        $payments = array();
        // 初期値で設定されている配送業社を設定
        foreach ($paymentOptions as $paymentOption) {
            $payments[] = $paymentOption->getPayment();
        }
        $form->add('payment', 'entity', array(
            'class' => 'Eccube\Entity\Payment',
            'property' => 'method',
            'choices' => $payments,
            'data' => $payment,
            // 'expanded' => true,
        ));

    }


}
