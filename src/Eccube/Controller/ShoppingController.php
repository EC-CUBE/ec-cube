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
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Shipping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;

class ShoppingController extends AbstractController
{

    /**
     * @var string 非会員用セッションキー
     */
    private $sessionKey = 'eccube.front.shopping.nonmember';

    private $sessionCustomerAddressKey = 'eccube.front.shopping.nonmember.customeraddress';

    /**
     * @var string 複数配送警告メッセージ
     */
    private $sessionMultipleKey = 'eccube.front.shopping.multiple';

    /**
     * 購入画面表示
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function index(Application $app, Request $request)
    {
        $cartService = $app['eccube.service.cart'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        // カートチェック
        if (count($cartService->getCart()->getCartItems()) <= 0) {
            // カートが存在しない時はエラー
            return $app->redirect($app->url('cart'));
        }

        // 登録済みの受注情報を取得
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

        // 初回アクセス(受注情報がない)の場合は, 受注情報を作成
        if (is_null($Order)) {

            // 未ログインの場合, ログイン画面へリダイレクト.
            if (!$app->isGranted('IS_AUTHENTICATED_FULLY')) {

                // 非会員でも一度会員登録されていればショッピング画面へ遷移
                $Customer = $app['eccube.service.shopping']->getNonMember($this->sessionKey);

                if (is_null($Customer)) {
                    return $app->redirect($app->url('shopping_login'));
                }

            } else {
                $Customer = $app->user();
            }

            // 受注情報を作成
            $Order = $app['eccube.service.shopping']->createOrder($Customer);

            $app['session']->remove($this->sessionMultipleKey);

        } else {
            // 計算処理
            $Order = $app['eccube.service.shopping']->getAmount($Order);
        }

        // 受注関連情報を最新状態に更新
        $app['orm.em']->refresh($Order);

        // form作成
        $form = $app['eccube.service.shopping']->getShippingForm($Order);

        // 合計数量
        $totalQuantity = $app['eccube.service.order']->getTotalQuantity($Order);

        // 複数配送の場合、エラーメッセージを一度だけ表示
        if (!$app['session']->has($this->sessionMultipleKey)) {
            if (count($Order->getShippings()) > 1) {
                $app->addRequestError('shopping.multiple.delivery');
            }
            $app['session']->set($this->sessionMultipleKey, 'multiple');
        }


        return $app->render('Shopping/index.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
            'totalQuantity' => $totalQuantity,
        ));
    }

    /**
     * 購入処理
     */
    public function confirm(Application $app, Request $request)
    {

        $cartService = $app['eccube.service.cart'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

        // form作成
        $form = $app['eccube.service.shopping']->getShippingForm($Order);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                // トランザクション制御
                $em = $app['orm.em'];
                $em->getConnection()->beginTransaction();
                try {
                    // 商品公開ステータスチェック、商品制限数チェック、在庫チェック
                    $check = $app['eccube.service.shopping']->isOrderProduct($em, $Order);
                    if (!$check) {
                        $em->getConnection()->rollback();
                        $em->close();

                        return $app->redirect($app->url('shopping_error'));
                    }

                    // 受注情報、配送情報を更新
                    $app['eccube.service.shopping']->setOrderUpdate($Order, $data);
                    // 在庫情報を更新
                    $app['eccube.service.shopping']->setStockUpdate($em, $Order);

                    if ($app->isGranted('ROLE_USER')) {
                        // 会員の場合、購入金額を更新
                        $app['eccube.service.shopping']->setCustomerUpdate($Order, $app->user());
                    }

                    $em->getConnection()->commit();
                    $em->flush();
                    $em->close();

                } catch (\Exception $e) {
                    $em->getConnection()->rollback();
                    $em->close();

                    $app->log($e);

                    return $app->redirect($app->url('shopping_error'));
                }

                // メール送信
                $app['eccube.service.mail']->sendOrderMail($Order);

                return $app->redirect($app->url('shopping_complete', array(
                    'status' => $app['config']['order_new'],
                )));

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
    public function complete(Application $app, $status)
    {

        // 購入ステータスを指定しなければpre_order_idのみで検索
        $Order = $app['eccube.service.shopping']->getOrder($status);

        // カート削除
        $app['eccube.service.cart']->clear()->save();

        return $app->render('Shopping/complete.twig', array(
            'Order' => $Order,
        ));
    }


    /**
     * 配送業者選択処理
     */
    public function delivery(Application $app, Request $request)
    {

        // カートチェック
        if (!$app['eccube.service.cart']->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

        $form = $app['eccube.service.shopping']->getShippingForm($Order);

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $data = $form->getData();

                $shippings = $data['shippings'];

                foreach ($shippings as $Shipping) {

                    $Delivery = $Shipping->getDelivery();

                    $deliveryFee = $app['eccube.repository.delivery_fee']->findOneBy(array(
                        'Delivery' => $Delivery,
                        'Pref' => $Shipping->getPref()
                    ));

                    $Shipping->setDeliveryFee($deliveryFee);
                    $Shipping->setShippingDeliveryFee($deliveryFee->getFee());
                    $Shipping->setShippingDeliveryName($Delivery->getName());
                }

                // 支払い情報をセット
                $payment = $data['payment'];
                $message = $data['message'];

                $Order->setPayment($payment);
                $Order->setPaymentMethod($payment->getMethod());
                $Order->setMessage($message);
                $Order->setCharge($payment->getCharge());

                $Order->setDeliveryFeeTotal($app['eccube.service.shopping']->getShippingDeliveryFeeTotal($shippings));

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

        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

        $form = $app['eccube.service.shopping']->getShippingForm($Order);

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {

				$data = $form->getData();
                $payment = $data['payment'];
                $message = $data['message'];

                $Order->setPayment($payment);
                $Order->setPaymentMethod($payment->getMethod());
                $Order->setMessage($message);
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
    public function shipping(Application $app, Request $request, $id)
    {

        // カートチェック
        if (!$app['eccube.service.cart']->isLocked()) {
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
                        'shippingId' => $id,
                    )
                );
            }

            // 選択されたお届け先情報を取得
            $CustomerAddress = $app['eccube.repository.customer_address']->findOneBy(array(
                'Customer' => $app->user(),
                'id' => $address));

            $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

            $Shipping = $Order->findShipping($id);
            if (!$Shipping) {
                throw new NotFoundHttpException();
            }

            // お届け先情報を更新
            $Shipping
                ->setName01($CustomerAddress->getName01())
                ->setName02($CustomerAddress->getName02())
                ->setKana01($CustomerAddress->getKana01())
                ->setKana02($CustomerAddress->getKana02())
                ->setCompanyName($CustomerAddress->getCompanyName())
                ->setTel01($CustomerAddress->getTel01())
                ->setTel02($CustomerAddress->getTel02())
                ->setTel03($CustomerAddress->getTel03())
                ->setFax01($CustomerAddress->getFax01())
                ->setFax02($CustomerAddress->getFax02())
                ->setFax03($CustomerAddress->getFax03())
                ->setZip01($CustomerAddress->getZip01())
                ->setZip02($CustomerAddress->getZip02())
                ->setZipCode($CustomerAddress->getZip01() . $CustomerAddress->getZip02())
                ->setPref($CustomerAddress->getPref())
                ->setAddr01($CustomerAddress->getAddr01())
                ->setAddr02($CustomerAddress->getAddr02());

            // 配送料金の設定
            $app['eccube.service.shopping']->setShippingDeliveryFee($Shipping);

            // 配送先を更新
            $app['orm.em']->flush();

            return $app->redirect($app->url('shopping'));

        }

        return $app->render(
            'Shopping/shipping.twig',
            array(
                'Customer' => $app->user(),
                'shippingId' => $id,
            )
        );
    }


    /**
     * お届け先の設定(非会員でも使用する)
     */
    public function shippingEdit(Application $app, Request $request, $id)
    {

        // カートチェック
        if (!$app['eccube.service.cart']->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }


        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

        $Shipping = $Order->findShipping($id);
        if (!$Shipping) {
            throw new NotFoundHttpException();
        }

        // 会員の場合、お届け先情報を新規登録
        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            $builder = $app['form.factory']->createBuilder('shopping_shipping');
        } else {
            // 非会員の場合、お届け先を追加
            $builder = $app['form.factory']->createBuilder('shopping_shipping', $Shipping);
        }

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                // 会員の場合、お届け先情報を新規登録
                if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
                    $CustomerAddress = new CustomerAddress();
                    $CustomerAddress
                        ->setCustomer($app->user())
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
                        ->setDelFlg(Constant::DISABLED);

                    $app['orm.em']->persist($CustomerAddress);

                }

                $Shipping
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

                // 配送料金の設定
                $app['eccube.service.shopping']->setShippingDeliveryFee($Shipping);

                // 配送先を更新
                $app['orm.em']->flush();

                return $app->redirect($app->url('shopping'));

            }
        }

        return $app->render('Shopping/shipping_edit.twig', array(
            'form' => $form->createView(),
            'shippingId' => $id,
        ));

    }

    /**
     * お客様情報の変更(非会員)
     */
    public function customer(Application $app, Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            try {
                $data = $request->request->all();
                $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

                $pref = $app['eccube.repository.master.pref']->findOneBy(array('name' => $data['customer_pref']));
                if (!$pref) {
                    $response = new Response(json_encode('NG'), 500);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

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
                    ->setPref($pref)
                    ->setAddr01($data['customer_addr01'])
                    ->setAddr02($data['customer_addr02'])
                    ->setEmail($data['customer_email']);

                // 配送先を更新
                $app['orm.em']->flush();

                // 受注関連情報を最新状態に更新
                $app['orm.em']->refresh($Order);

                $response = new Response(json_encode('OK'));
                $response->headers->set('Content-Type', 'application/json');

            } catch (\Exception $e) {
                $app->log($e);

                $response = new Response(json_encode('NG'), 500);
                $response->headers->set('Content-Type', 'application/json');

            }

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

        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $app->redirect($app->url('shopping'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $builder = $app['form.factory']->createNamedBuilder('', 'customer_login');

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

        // カートチェック
        if (count($cartService->getCart()->getCartItems()) <= 0) {
            // カートが存在しない時はエラー
            return $app->redirect($app->url('cart'));
        }

        $form = $app['form.factory']->createBuilder('nonmember')->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $Customer = new Customer();
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

                // 非会員複数配送用
                $CustomerAddress = new CustomerAddress();
                $CustomerAddress
                    ->setCustomer($Customer)
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
                    ->setDelFlg(Constant::DISABLED);
                $Customer->addCustomerAddress($CustomerAddress);

                // 受注情報を取得
                $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

                // 初回アクセス(受注データがない)の場合は, 受注情報を作成
                if (is_null($Order)) {
                    // 受注情報を作成
                    $app['eccube.service.shopping']->createOrder($Customer);
                }

                // 非会員用セッションを作成
                $nonMember = array();
                $nonMember['customer'] = $Customer;
                $nonMember['pref'] = $Customer->getPref()->getId();
                $app['session']->set($this->sessionKey, $nonMember);

                $customerAddresses = array();
                $customerAddresses[] = $CustomerAddress;
                $app['session']->set($this->sessionCustomerAddressKey, serialize($customerAddresses));

                return $app->redirect($app->url('shopping'));

            }
        }

        return $app->render('Shopping/nonmember.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * 複数配送処理
     */
    public function shippingMultiple(Application $app, Request $request)
    {

        $cartService = $app['eccube.service.cart'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        // カートチェック
        if (count($cartService->getCart()->getCartItems()) <= 0) {
            // カートが存在しない時はエラー
            return $app->redirect($app->url('cart'));
        }
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

        // 複数配送時は商品毎でお届け先を設定する為、商品をまとめた数量を設定
        $compItemQuantities = array();
        foreach ($Order->getShippings() as $Shipping) {
            foreach ($Shipping->getShipmentItems() as $ShipmentItem) {
                $itemId = $ShipmentItem->getProductClass()->getId();
                $quantity = $ShipmentItem->getQuantity();
                if (array_key_exists($itemId, $compItemQuantities)) {
                    $compItemQuantities[$itemId] = $compItemQuantities[$itemId] + $quantity;
                } else {
                    $compItemQuantities[$itemId] = $quantity;
                }
            }
        }

        // 商品に紐づく商品情報を取得
        $shipmentItems = array();
        $productClassIds = array();
        foreach ($Order->getShippings() as $Shipping) {
            foreach ($Shipping->getShipmentItems() as $ShipmentItem) {
                if (!in_array($ShipmentItem->getProductClass()->getId(), $productClassIds)) {
                    $shipmentItems[] = $ShipmentItem;
                }
                $productClassIds[] = $ShipmentItem->getProductClass()->getId();
            }
        }

        $form = $app->form()->getForm();
        $form
            ->add('shipping_multiple', 'collection', array(
                'type' => 'shipping_multiple',
                'data' => $shipmentItems,
                'allow_add' => true,
                'allow_delete' => true,
            ));

        $errors = array();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form['shipping_multiple'];

                // 数量が超えていないか、同一でないとエラー
                $itemQuantities = array();
                foreach ($data as $mulitples) {
                    /** @var \Eccube\Entity\ShipmentItem $multipleItem */
                    $multipleItem = $mulitples->getData();
                    foreach ($mulitples as $items) {
                        foreach ($items as $item) {
                            $quantity = $item['quantity']->getData();
                            $itemId = $multipleItem->getProductClass()->getId();
                            if (array_key_exists($itemId, $itemQuantities)) {
                                $itemQuantities[$itemId] = $itemQuantities[$itemId] + $quantity;
                            } else {
                                $itemQuantities[$itemId] = $quantity;
                            }
                        }
                    }
                }

                foreach ($compItemQuantities as $key => $value) {
                    if (array_key_exists($key, $itemQuantities)) {
                        if ($itemQuantities[$key] != $value) {

                            $errors[] = array('message' => '数量の数が異なっています。');

                            // 対象がなければエラー
                            return $app->render('Shopping/shipping_multiple.twig', array(
                                'form' => $form->createView(),
                                'shipmentItems' => $shipmentItems,
                                'compItemQuantities' => $compItemQuantities,
                                'errors' => $errors,
                            ));

                        }
                    }
                }

                // お届け先情報をdelete/insert

                $shippings = $Order->getShippings();
                foreach ($shippings as $Shipping) {
                    $Order->removeShipping($Shipping);
                    $app['orm.em']->remove($Shipping);
                }

                foreach ($data as $mulitples) {

                    /** @var \Eccube\Entity\ShipmentItem $multipleItem */
                    $multipleItem = $mulitples->getData();

                    foreach ($mulitples as $items) {
                        foreach ($items as $item) {

                            // 追加された配送先情報を作成
                            $Delivery = $multipleItem->getShipping()->getDelivery();

                            // 選択された情報を取得
                            $data = $item['customer_address']->getData();
                            if ($data instanceof CustomerAddress) {
                                // 会員の場合、CustomerAddressオブジェクトを取得される
                                $CustomerAddress = $data;
                            } else {
                                // 非会員の場合、選択されたindexが取得される
                                $customerAddresses = $app['session']->get($this->sessionCustomerAddressKey);
                                $customerAddresses = unserialize($customerAddresses);
                                $CustomerAddress = $customerAddresses[$data];
                                $pref = $app['eccube.repository.master.pref']->find($CustomerAddress->getPref()->getId());
                                $CustomerAddress->setPref($pref);
                            }

                            $Shipping = new Shipping();

                            $Shipping
                                ->setName01($CustomerAddress->getName01())
                                ->setName02($CustomerAddress->getName02())
                                ->setKana01($CustomerAddress->getKana01())
                                ->setKana02($CustomerAddress->getKana02())
                                ->setCompanyName($CustomerAddress->getCompanyName())
                                ->setTel01($CustomerAddress->getTel01())
                                ->setTel02($CustomerAddress->getTel02())
                                ->setTel03($CustomerAddress->getTel03())
                                ->setFax01($CustomerAddress->getFax01())
                                ->setFax02($CustomerAddress->getFax02())
                                ->setFax03($CustomerAddress->getFax03())
                                ->setZip01($CustomerAddress->getZip01())
                                ->setZip02($CustomerAddress->getZip02())
                                ->setZipCode($CustomerAddress->getZip01() . $CustomerAddress->getZip02())
                                ->setPref($CustomerAddress->getPref())
                                ->setAddr01($CustomerAddress->getAddr01())
                                ->setAddr02($CustomerAddress->getAddr02())
                                ->setDelivery($Delivery)
                                ->setDelFlg(Constant::DISABLED)
                                ->setOrder($Order);

                            $app['orm.em']->persist($Shipping);


                            $ShipmentItem = new ShipmentItem();

                            $ProductClass = $multipleItem->getProductClass();
                            $Product = $multipleItem->getProduct();


                            $quantity = $item['quantity']->getData();

                            $ShipmentItem->setShipping($Shipping)
                                ->setOrder($Order)
                                ->setProductClass($ProductClass)
                                ->setProduct($Product)
                                ->setProductName($Product->getName())
                                ->setProductCode($ProductClass->getCode())
                                ->setPrice($ProductClass->getPrice02())
                                ->setQuantity($quantity);

                            $ClassCategory1 = $ProductClass->getClassCategory1();
                            if (!is_null($ClassCategory1)) {
                                $ShipmentItem->setClasscategoryName1($ClassCategory1->getName());
                                $ShipmentItem->setClassName1($ClassCategory1->getClassName()->getName());
                            }
                            $ClassCategory2 = $ProductClass->getClassCategory2();
                            if (!is_null($ClassCategory2)) {
                                $ShipmentItem->setClasscategoryName2($ClassCategory2->getName());
                                $ShipmentItem->setClassName2($ClassCategory2->getClassName()->getName());
                            }
                            $Shipping->addShipmentItem($ShipmentItem);
                            $app['orm.em']->persist($ShipmentItem);

                            // 配送料金の設定
                            $app['eccube.service.shopping']->setShippingDeliveryFee($Shipping);

                        }
                    }
                }
                // 配送先を更新
                $app['orm.em']->flush();
                return $app->redirect($app->url('shopping'));
            }
        }

        return $app->render('Shopping/shipping_multiple.twig', array(
            'form' => $form->createView(),
            'shipmentItems' => $shipmentItems,
            'compItemQuantities' => $compItemQuantities,
            'errors' => $errors,
        ));
    }

    /**
     * 非会員用複数配送設定時の新規お届け先の設定
     */
    public function shippingMultipleEdit(Application $app, Request $request)
    {

        // カートチェック
        if (!$app['eccube.service.cart']->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        $form = $app['form.factory']->createBuilder('shopping_shipping')->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                // 非会員用Customerを取得
                $Customer = $app['eccube.service.shopping']->getNonMember($this->sessionKey);

                $CustomerAddress = new CustomerAddress();
                $CustomerAddress
                    ->setCustomer($Customer)
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
                    ->setDelFlg(Constant::DISABLED);
                $Customer->addCustomerAddress($CustomerAddress);


                // 非会員用のセッションに追加
                $customerAddresses = $app['session']->get($this->sessionCustomerAddressKey);
                $customerAddresses = unserialize($customerAddresses);
                $customerAddresses[] = $CustomerAddress;
                $app['session']->set($this->sessionCustomerAddressKey, serialize($customerAddresses));

                return $app->redirect($app->url('shopping_shipping_multiple'));

            }
        }

        return $app->render('Shopping/shipping_multiple_edit.twig', array(
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
    
    /**
     * お届け先変更がクリックされた場合の処理
     */
    public function shippingChange(Application $app, Request $request, $id)
    {
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

        $form = $app['eccube.service.shopping']->getShippingForm($Order);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            // バリデート処理
            if ($form->isValid()) {
                $data = $form->getData();
                $message = $data['message'];
                $Order->setMessage($message);
                // 受注情報を更新
                $app['orm.em']->flush();
                // お届け先設定一覧へリダイレクト
			    return $app->redirect($app->url('shopping_shipping', array('id' => $id)));
            }
        }

	    return $app->redirect($app->url('shopping'));
    }

    /**
     * お届け先の設定（非会員）がクリックされた場合の処理
     */
    public function shippingEditChange(Application $app, Request $request, $id)
    {
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

        $form = $app['eccube.service.shopping']->getShippingForm($Order);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            // バリデート処理
            if ($form->isValid()) {
                $data = $form->getData();
                $message = $data['message'];
                $Order->setMessage($message);
                // 受注情報を更新
                $app['orm.em']->flush();
                // お届け先設定一覧へリダイレクト
			    return $app->redirect($app->url('shopping_shipping_edit', array('id' => $id)));
            }
        }

	    return $app->redirect($app->url('shopping'));
    }

    /**
     * 複数配送処理がクリックされた場合の処理
     */
    public function shippingMultipleChange(Application $app, Request $request)
    {
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

        $form = $app['eccube.service.shopping']->getShippingForm($Order);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            // バリデート処理
            if ($form->isValid()) {
                $data = $form->getData();
                $message = $data['message'];
                $Order->setMessage($message);
                // 受注情報を更新
                $app['orm.em']->flush();
                // 複数配送設定へリダイレクト
			    return $app->redirect($app->url('shopping_shipping_multiple'));
            }
        }

	    return $app->redirect($app->url('shopping'));
    }

    
}
