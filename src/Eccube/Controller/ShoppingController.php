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
use Eccube\Common\Constant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints as Assert;

class ShoppingController extends AbstractController
{
    /** @var \Eccube\Service\CartService */
    protected $cartService;
    /** @var \Eccube\Repository\OrderRepository */
    protected $orderRepository;
    /** @var \Eccube\Service\OrderService */
    protected $orderService;

    public function index(Application $app, Request $request)
    {
        $cartService = $app['eccube.service.cart'];
        $orderService = $app['eccube.service.order'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        $Cart = $cartService->getCart();

        // カートチェック
        if (count($Cart->getCartItems()) <= 0) {
            // カートが存在しない時はエラー
            return $app->redirect($app->url('cart'));
        }
        if ($cartService->hasError()) {
            // カート内の商品に異常があればエラー
            return $app->redirect($app->url('cart'));
        }

        // 受注データを取得
        $preOrderId = $cartService->getPreOrderId();
        $Order = $app['eccube.repository.order']->findOneBy(array(
            'pre_order_id' => $preOrderId,
            'OrderStatus' => $app['config']['order_processing']
        ));

        // 初回アクセス(受注データがない)の場合は, 受注データを作成
        if (is_null($Order)) {

            // 未ログインの場合は, ログイン画面へリダイレクト.
            if (!$app->isGranted('IS_AUTHENTICATED_FULLY')) {

                // 非会員でも一度会員登録されていればショッピング画面へ遷移
                $arr = $app['session']->get('eccube.front.shopping.nonmember');
                if (is_null($arr)) {
                    return $app->redirect($app->url('shopping_login'));
                }
                $Customer = $arr['customer'];
                $Customer->setPref($app['eccube.repository.master.pref']->find($arr['pref']));
            } else {
                $Customer = $app->user();
            }

            // ランダムなpre_order_idを作成
            $preOrderId = sha1(uniqid(mt_rand(), true));

            // 受注情報、受注明細情報、お届け先情報、配送商品情報を作成
            $Order = $orderService->registerPreOrderFromCartItems($Cart->getCartItems(), $Customer,
                $preOrderId);

            $cartService->setPreOrderId($preOrderId);
            $cartService->save();
        } else {
            // 計算処理
            $Order = $orderService->getAmount($Order, $Cart);
        }

        // 受注関連情報を最新状態に更新
        $app['orm.em']->refresh($Order);

        $form = $app['form.factory']->createBuilder('shopping')->getForm();

        $deliveries = $orderService->findDeliveriesFromOrderDetails($app, $Order->getOrderDetails());

        $shippings = $Order->getShippings();
        $delivery = $shippings[0]->getDelivery();

        // 配送業社の設定
        $orderService->setFormDelivery($form, $deliveries, $delivery);

        // お届け日の設定
        $orderService->setFormDeliveryDate($form, $Order, $app);

        // お届け時間の設定
        $orderService->setFormDeliveryTime($form, $delivery);

        // 支払い方法選択
        $orderService->setFormPayment($form, $delivery, $Order, $app);

        return $app->render('Shopping/index.twig', array(
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

        // カートチェック
        if ($cartService->hasError()) {
            // カート内の商品に異常があればエラー
            return $app->redirect($app->url('cart'));
        }

        $form = $app['form.factory']->createBuilder('shopping')->getForm();

        $Order = $orderRepository->findOneBy(array('pre_order_id' => $cartService->getPreOrderId()));

        $deliveries = $orderService->findDeliveriesFromOrderDetails($app, $Order->getOrderDetails());


        // 配送業社の設定
        $shippings = $Order->getShippings();
        $delivery = $shippings[0]->getDelivery();

        // 配送業社の設定
        $orderService->setFormDelivery($form, $deliveries, $delivery);

        // お届け日の設定
        $orderService->setFormDeliveryDate($form, $Order, $app);

        // お届け時間の設定
        $orderService->setFormDeliveryTime($form, $delivery);

        // 支払い方法選択
        $orderService->setFormPayment($form, $delivery, $Order, $app);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $formData = $form->getData();

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

                    if ($app->isGranted('ROLE_USER')) {
                        // 会員の場合、購入金額を更新
                        $orderService->setCustomerUpdate($em, $Order, $app->user());
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
            } else {
                return $app->render('Shopping/index.twig', array(
                    'form' => $form->createView(),
                    'Order' => $Order,
                ));
            }
        }

        return $app->redirect($app->url('cart'));
    }


    /**
     * 購入完了画面表示
     */
    public function complete(Application $app)
    {
        return $app->render('Shopping/complete.twig');
    }


    /**
     * 配送業者選択処理
     */
    public function delivery(Application $app, Request $request)
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

        $deliveries = $orderService->findDeliveriesFromOrderDetails($app, $Order->getOrderDetails());

        $shippings = $Order->getShippings();
        $delivery = $shippings[0]->getDelivery();

        // 配送業社の設定
        $orderService->setFormDelivery($form, $deliveries, $delivery);

        // お届け日の設定
        $orderService->setFormDeliveryDate($form, $Order, $app);

        // お届け時間の設定
        $orderService->setFormDeliveryTime($form, $delivery);

        // 支払い方法選択
        $orderService->setFormPayment($form, $delivery, $Order, $app);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $delivery = $data['delivery'];

                // 配送業者をセット
                $shippings = $Order->getShippings();
                $Shipping = $shippings[0];

                $deliveryFee = $app['eccube.repository.delivery_fee']->findOneBy(array(
                    'Delivery' => $delivery,
                    'Pref' => $Shipping->getPref()
                ));
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
            }
        }

        return $app->redirect($app->url('shopping'));
    }

    /**
     * 支払い方法選択処理
     */
    public function payment(Application $app, Request $request)
    {
        $cartService = $app['eccube.service.cart'];
        $orderService = $app['eccube.service.order'];
        $orderRepository = $app['eccube.repository.order'];

        $form = $app['form.factory']->createBuilder('shopping')->getForm();

        $Order = $orderRepository->findOneBy(array('pre_order_id' => $cartService->getPreOrderId()));

        $deliveries = $orderService->findDeliveriesFromOrderDetails($app, $Order->getOrderDetails());

        $shippings = $Order->getShippings();
        $delivery = $shippings[0]->getDelivery();

        // 配送業社の設定
        $orderService->setFormDelivery($form, $deliveries, $delivery);

        // お届け日の設定
        $orderService->setFormDeliveryDate($form, $Order, $app);

        // お届け時間の設定
        $orderService->setFormDeliveryTime($form, $delivery);

        // 支払い方法選択
        $orderService->setFormPayment($form, $delivery, $Order, $app);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $payment = $data['payment'];

                $Order->setPayment($payment);
                $Order->setPaymentMethod($payment->getMethod());
                $Order->setCharge($payment->getCharge());

                $total = $Order->getSubTotal() + $Order->getCharge() + $Order->getDeliveryFeeTotal();

                $Order->setTotal($total);
                $Order->setPaymentTotal($total);

                // 受注関連情報を最新状態に更新
                $app['orm.em']->flush();
            }
        }

        return $app->redirect($app->url('shopping'));
    }

    /**
     * お届け先の設定一覧からの選択
     */
    public function shipping(Application $app, Request $request)
    {
        $cartService = $app['eccube.service.cart'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        if ('POST' === $request->getMethod()) {
            $address = $request->get('address');

            if (is_null($address)) {
                // 選択されていなければエラー
                return $app->render(
                    'Shopping/shipping.twig',
                    array(
                        'Customer' => $app->user(),
                    )
                );
            }

            // 選択されたお届け先情報を取得
            $customerAddress = $app['eccube.repository.customer_address']->findOneBy(array(
                'Customer' => $app->user(),
                'id' => $address));

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

        return $app->render(
            'Shopping/shipping.twig',
            array(
                'Customer' => $app->user(),
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
        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            $customer = $app['user'];
            $delivery = $app['eccube.repository.customer_address']->findOrCreateByCustomerAndId($customer);
            $builder = $app['form.factory']->createBuilder('customer_address', $delivery);
        } else {
            // 非会員の場合、お届け先を追加
            $delivery = $shippings[0];
            $builder = $app['form.factory']->createBuilder('shopping_shipping', $delivery);
        }

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // 振る舞いが違うのでわかりにくい
                $app['orm.em']->persist($delivery);

                // 会員の場合、shippingsをアドレスで上書きする
                if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
                    $data = $form->getData();
                    $shippings[0]->copyProperties($data);
                    $app['orm.em']->persist($shippings[0]);
                }

                // 配送先を更新
                $app['orm.em']->flush();

                return $app->redirect($app->url('shopping'));
            }
        }

        return $app->render('Shopping/shipping_edit.twig', array(
            'form' => $form->createView(),
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
        $cartService = $app['eccube.service.cart'];

        if (!$cartService->isLocked()) {
            return $app->redirect($app['url_generator']->generate('cart'));
        }

        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $app->redirect($app->url('shopping'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $builder = $app['form.factory']
            ->createNamedBuilder('', 'customer_login');

        if ($app->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $Customer = $app->user();
            if ($Customer) {
                $builder->get('login_email')->setData($Customer->getEmail());
            }
        }

        $form = $builder->getForm();

        return $app->render('Shopping/login.twig', array(
            'error' => $app['security.last_error']($request),
            'form' => $form->createView(),
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
        if ($app->isGranted('ROLE_USER')) {
            return $app->redirect($app->url('shopping'));
        }

        $Cart = $cartService->getCart();

        // カートチェック
        if (count($Cart->getCartItems()) <= 0) {
            // カートが存在しない時はエラー
            return $app->redirect($app->url('cart'));
        }
        if ($cartService->hasError()) {
            // カート内の商品に異常があればエラー
            return $app->redirect($app->url('cart'));
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
                $Order = $app['eccube.repository.order']->findOneBy(array(
                    'pre_order_id' => $preOrderId,
                    'OrderStatus' => $app['config']['order_processing']
                ));

                // 初回アクセス(受注データがない)の場合は, 受注データを作成
                if (is_null($Order)) {
                    // ランダムなpre_order_idを作成
                    $preOrderId = sha1(uniqid(mt_rand(), true));

                    // 受注情報、受注明細情報、お届け先情報、配送商品情報を作成
                    $app['eccube.service.order']->registerPreOrderFromCartItems($Cart->getCartItems(),
                        $Customer, $preOrderId);

                    $cartService->setPreOrderId($preOrderId);
                    $cartService->save();
                }

                // 非会員用セッションを作成
                $arr = array();
                $arr['customer'] = $Customer;
                $arr['pref'] = $Customer->getPref()->getId();
                $app['session']->set('eccube.front.shopping.nonmember', $arr);

                return $app->redirect($app->url('shopping'));
            }
        }

        return $app->render('Shopping/nonmember.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * 購入エラー画面表示
     */
    public function shoppingError(Application $app)
    {
        return $app->render('Shopping/shopping_error.twig');
    }
}
