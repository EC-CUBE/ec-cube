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
use Eccube\Entity\MailHistory;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
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

    /**
     * @var string 非会員用セッションキー
     */
    private $sessionCustomerAddressKey = 'eccube.front.shopping.nonmember.customeraddress';

    /**
     * @var string 複数配送警告メッセージ
     */
    private $sessionMultipleKey = 'eccube.front.shopping.multiple';

    /**
     * @var string 受注IDキー
     */
    private $sessionOrderKey = 'eccube.front.shopping.order.id';

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

            try {
                // 受注情報を作成
                $Order = $app['eccube.service.shopping']->createOrder($Customer);
            } catch (CartException $e) {
                $app->addRequestError($e->getMessage());
                return $app->redirect($app->url('cart'));
            }

            // セッション情報を削除
            $app['session']->remove($this->sessionOrderKey);
            $app['session']->remove($this->sessionMultipleKey);
        } else {
            // 計算処理
            $Order = $app['eccube.service.shopping']->getAmount($Order);
        }

        // 受注関連情報を最新状態に更新
        $app['orm.em']->refresh($Order);

        // form作成
        $builder = $app['eccube.service.shopping']->getShippingFormBuilder($Order);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

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
        if (!$Order) {
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

        if ('POST' !== $request->getMethod()) {
            return $app->redirect($app->url('cart'));
        }

        // form作成
        $builder = $app['eccube.service.shopping']->getShippingFormBuilder($Order);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_CONFIRM_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // トランザクション制御
            $em = $app['orm.em'];
            $em->getConnection()->beginTransaction();
            try {
                // 商品公開ステータスチェック、商品制限数チェック、在庫チェック
                $check = $app['eccube.service.shopping']->isOrderProduct($em, $Order);
                if (!$check) {
                    $em->getConnection()->rollback();

                    $app->addError('front.shopping.stock.error');
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

                $em->flush();
                $em->getConnection()->commit();

            } catch (\Exception $e) {
                $em->getConnection()->rollback();

                $app->log($e);

                $app->addError('front.shopping.system.error');
                return $app->redirect($app->url('shopping_error'));
            }

            // カート削除
            $app['eccube.service.cart']->clear()->save();

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_CONFIRM_PROCESSING, $event);

            if ($event->getResponse() !== null) {
                return $event->getResponse();
            }

            // メール送信
            $app['eccube.service.mail']->sendOrderMail($Order);

            // 受注IDをセッションにセット
            $app['session']->set($this->sessionOrderKey, $Order->getId());

            // 送信履歴を保存.
            $MailTemplate = $app['eccube.repository.mail_template']->find(1);

            $body = $app->renderView($MailTemplate->getFileName(), array(
                'header' => $MailTemplate->getHeader(),
                'footer' => $MailTemplate->getFooter(),
                'Order' => $Order,
            ));

            $MailHistory = new MailHistory();
            $MailHistory
                ->setSubject('[' . $app['eccube.repository.base_info']->get()->getShopName() . '] ' . $MailTemplate->getSubject())
                ->setMailBody($body)
                ->setMailTemplate($MailTemplate)
                ->setSendDate(new \DateTime())
                ->setOrder($Order);
            $app['orm.em']->persist($MailHistory);
            $app['orm.em']->flush($MailHistory);

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                    'MailHistory' => $MailHistory,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_CONFIRM_COMPLETE, $event);

            if ($event->getResponse() !== null) {
                return $event->getResponse();
            }

            // 完了画面表示
            return $app->redirect($app->url('shopping_complete'));
        }

        return $app->render('Shopping/index.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
        ));
    }


    /**
     * 購入完了画面表示
     */
    public function complete(Application $app, Request $request)
    {
        // 受注IDを取得
        $orderId = $app['session']->get($this->sessionOrderKey);

        $event = new EventArgs(
            array(
                'orderId' => $orderId,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_COMPLETE_INITIALIZE, $event);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        // 受注IDセッションを削除
        $app['session']->remove($this->sessionOrderKey);

        return $app->render('Shopping/complete.twig', array(
            'orderId' => $orderId,
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
        if (!$Order) {
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

        if ('POST' !== $request->getMethod()) {
            return $app->redirect($app->url('shopping'));
        }

        $builder = $app['eccube.service.shopping']->getShippingFormBuilder($Order);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_DELIVERY_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $shippings = $data['shippings'];

            $productDeliveryFeeTotal = 0;
            $BaseInfo = $app['eccube.repository.base_info']->get();

            foreach ($shippings as $Shipping) {
                $Delivery = $Shipping->getDelivery();

                if ($Delivery) {
                    $deliveryFee = $app['eccube.repository.delivery_fee']->findOneBy(array(
                        'Delivery' => $Delivery,
                        'Pref' => $Shipping->getPref()
                    ));

                    // 商品ごとの配送料合計
                    if (!is_null($BaseInfo->getOptionProductDeliveryFee())) {
                        $productDeliveryFeeTotal += $app['eccube.service.shopping']->getProductDeliveryFee($Shipping);
                    }

                    $Shipping->setDeliveryFee($deliveryFee);
                    $Shipping->setShippingDeliveryFee($deliveryFee->getFee() + $productDeliveryFeeTotal);
                    $Shipping->setShippingDeliveryName($Delivery->getName());
                }
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

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_DELIVERY_COMPLETE, $event);

            return $app->redirect($app->url('shopping'));
        }

        return $app->render('Shopping/index.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
        ));
    }

    /**
     * 支払い方法選択処理
     */
    public function payment(Application $app, Request $request)
    {
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

        if ('POST' !== $request->getMethod()) {
            return $app->redirect($app->url('shopping'));
        }

        $builder = $app['eccube.service.shopping']->getShippingFormBuilder($Order);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_PAYMENT_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_PAYMENT_COMPLETE, $event);

            return $app->redirect($app->url('shopping'));
        }

        return $app->render('Shopping/index.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
        ));
    }

    /**
     * お届け先変更がクリックされた場合の処理
     */
    public function shippingChange(Application $app, Request $request, $id)
    {
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

        if ('POST' !== $request->getMethod()) {
            return $app->redirect($app->url('shopping'));
        }

        $builder = $app['eccube.service.shopping']->getShippingFormBuilder($Order);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = $data['message'];
            $Order->setMessage($message);
            // 受注情報を更新
            $app['orm.em']->flush();

            // お届け先設定一覧へリダイレクト
            return $app->redirect($app->url('shopping_shipping', array('id' => $id)));
        }

        return $app->render('Shopping/index.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
        ));
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
                        'error' => true,
                    )
                );
            }

            // 選択されたお届け先情報を取得
            $CustomerAddress = $app['eccube.repository.customer_address']->findOneBy(array(
                'Customer' => $app->user(),
                'id' => $address,
            ));
            if (is_null($CustomerAddress)) {
                throw new NotFoundHttpException();
            }

            $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
            if (!$Order) {
                $app->addError('front.shopping.order.error');

                return $app->redirect($app->url('shopping_error'));
            }

            $Shipping = $Order->findShipping($id);
            if (!$Shipping) {
                throw new NotFoundHttpException();
            }

            // お届け先情報を更新
            $Shipping
                ->setFromCustomerAddress($CustomerAddress);

            // 配送料金の設定
            $app['eccube.service.shopping']->setShippingDeliveryFee($Shipping);

            // 配送先を更新
            $app['orm.em']->flush();

            $event = new EventArgs(
                array(
                    'Order' => $Order,
                    'shippingId' => $id,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_COMPLETE, $event);

            return $app->redirect($app->url('shopping'));
        }

        return $app->render(
            'Shopping/shipping.twig',
            array(
                'Customer' => $app->user(),
                'shippingId' => $id,
                'error' => false,
            )
        );
    }

    /**
     * お届け先の設定（非会員）がクリックされた場合の処理
     */
    public function shippingEditChange(Application $app, Request $request, $id)
    {
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

        if ('POST' !== $request->getMethod()) {
            return $app->redirect($app->url('shopping'));
        }

        $builder = $app['eccube.service.shopping']->getShippingFormBuilder($Order);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = $data['message'];
            $Order->setMessage($message);
            // 受注情報を更新
            $app['orm.em']->flush();

            // お届け先設定一覧へリダイレクト
            return $app->redirect($app->url('shopping_shipping_edit', array('id' => $id)));
        }

        return $app->render('Shopping/index.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
        ));
    }

    /**
     * お届け先の設定(非会員でも使用する)
     */
    public function shippingEdit(Application $app, Request $request, $id)
    {
        // 配送先住所最大値判定
        $Customer = $app->user();
        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            $addressCurrNum = count($app->user()->getCustomerAddresses());
            $addressMax = $app['config']['deliv_addr_max'];
            if ($addressCurrNum >= $addressMax) {
                throw new NotFoundHttpException();
            }
        }

        // カートチェック
        if (!$app['eccube.service.cart']->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

        $Shipping = $Order->findShipping($id);
        if (!$Shipping) {
            throw new NotFoundHttpException();
        }
        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            $Shipping->clearCustomerAddress();
        }

        $CustomerAddress = new CustomerAddress();
        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            $CustomerAddress->setCustomer($Customer);
        } else {
            $CustomerAddress->setFromShipping($Shipping);
        }

        $builder = $app['form.factory']->createBuilder('shopping_shipping', $CustomerAddress);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
                'Shipping' => $Shipping,
                'CustomerAdderss' => $CustomerAddress,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 会員の場合、お届け先情報を新規登録
            $Shipping->setFromCustomerAddress($CustomerAddress);

            if ($Customer instanceof Customer) {
                $app['orm.em']->persist($CustomerAddress);
            }

            // 配送料金の設定
            $app['eccube.service.shopping']->setShippingDeliveryFee($Shipping);

            // 配送先を更新 
            $app['orm.em']->flush();

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Shipping' => $Shipping,
                    'CustomerAdderss' => $CustomerAddress,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_COMPLETE, $event);

            return $app->redirect($app->url('shopping'));
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

                // 入力チェック
                $errors = $this->customerValidation($app, $data);

                foreach ($errors as $error) {
                    if ($error->count() != 0) {
                        $response = new Response(json_encode('NG'), 400);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                }

                $pref = $app['eccube.repository.master.pref']->findOneBy(array('name' => $data['customer_pref']));
                if (!$pref) {
                    $response = new Response(json_encode('NG'), 400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

                $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
                if (!$Order) {
                    $app->addError('front.shopping.order.error');
                    return $app->redirect($app->url('shopping_error'));
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

                $event = new EventArgs(
                    array(
                        'Order' => $Order,
                        'data' => $data,
                    ),
                    $request
                );
                $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_CUSTOMER_INITIALIZE, $event);

                $response = new Response(json_encode('OK'));
                $response->headers->set('Content-Type', 'application/json');
            } catch (\Exception $e) {
                $app['monolog']->error($e);

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
            return $app->redirect($app->url('cart'));
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

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_LOGIN_INITIALIZE, $event);

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

        $builder = $app['form.factory']->createBuilder('nonmember');

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_NONMEMBER_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
                try {
                    // 受注情報を作成
                    $app['eccube.service.shopping']->createOrder($Customer);
                } catch (CartException $e) {
                    $app->addRequestError($e->getMessage());
                    return $app->redirect($app->url('cart'));
                }
            }

            // 非会員用セッションを作成
            $nonMember = array();
            $nonMember['customer'] = $Customer;
            $nonMember['pref'] = $Customer->getPref()->getId();
            $app['session']->set($this->sessionKey, $nonMember);

            $customerAddresses = array();
            $customerAddresses[] = $CustomerAddress;
            $app['session']->set($this->sessionCustomerAddressKey, serialize($customerAddresses));

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_NONMEMBER_COMPLETE, $event);

            if ($event->getResponse() !== null) {
                return $event->getResponse();
            }

            return $app->redirect($app->url('shopping'));
        }

        return $app->render('Shopping/nonmember.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * 複数配送処理がクリックされた場合の処理
     */
    public function shippingMultipleChange(Application $app, Request $request)
    {
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

        if ('POST' !== $request->getMethod()) {
            return $app->redirect($app->url('shopping'));
        }

        $builder = $app['eccube.service.shopping']->getShippingFormBuilder($Order);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = $data['message'];
            $Order->setMessage($message);
            // 受注情報を更新
            $app['orm.em']->flush();

            // 複数配送設定へリダイレクト
            return $app->redirect($app->url('shopping_shipping_multiple'));
        }

        return $app->render('Shopping/index.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
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
        if (!$Order) {
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

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

        $builder = $app->form();
        $builder
            ->add('shipping_multiple', 'collection', array(
                'type' => 'shipping_multiple',
                'data' => $shipmentItems,
                'allow_add' => true,
                'allow_delete' => true,
            ));

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        $errors = array();
        if ($form->isSubmitted() && $form->isValid()) {
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
                            // 会員の場合、CustomerAddressオブジェクトを取得
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
                            ->setFromCustomerAddress($CustomerAddress)
                            ->setDelivery($Delivery)
                            ->setDelFlg(Constant::DISABLED)
                            ->setOrder($Order);
                        $app['orm.em']->persist($Shipping);

                        $ProductClass = $multipleItem->getProductClass();
                        $Product = $multipleItem->getProduct();
                        $quantity = $item['quantity']->getData();

                        $ShipmentItem = new ShipmentItem();
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

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_COMPLETE, $event);

            return $app->redirect($app->url('shopping'));
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

        // 非会員用Customerを取得
        $Customer = $app['eccube.service.shopping']->getNonMember($this->sessionKey);
        $CustomerAddress = new CustomerAddress();
        $CustomerAddress->setCustomer($Customer);
        $Customer->addCustomerAddress($CustomerAddress);

        $builder = $app['form.factory']->createBuilder('shopping_shipping', $CustomerAddress);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Customer' => $Customer,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 非会員用のセッションに追加
            $customerAddresses = $app['session']->get($this->sessionCustomerAddressKey);
            $customerAddresses = unserialize($customerAddresses);
            $customerAddresses[] = $CustomerAddress;
            $app['session']->set($this->sessionCustomerAddressKey, serialize($customerAddresses));

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'CustomerAddresses' => $customerAddresses,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_COMPLETE, $event);

            return $app->redirect($app->url('shopping_shipping_multiple'));
        }

        return $app->render('Shopping/shipping_multiple_edit.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * 購入エラー画面表示
     */
    public function shoppingError(Application $app, Request $request)
    {

        $event = new EventArgs(
            array(),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_ERROR_COMPLETE, $event);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        return $app->render('Shopping/shopping_error.twig');
    }

    /**
     * 非会員でのお客様情報変更時の入力チェック
     * @param $data リクエストパラメータ
     */
    private function customerValidation($app, $data)
    {
        // 入力チェック
        $errors = array();

        $errors[] = $app['validator']->validateValue($data['customer_name01'], array(
            new Assert\NotBlank(),
            new Assert\Length(array('max' => $app['config']['name_len'],)),
            new Assert\Regex(array('pattern' => '/^[^\s ]+$/u', 'message' => 'form.type.name.firstname.nothasspace'))
        ));

        $errors[] = $app['validator']->validateValue($data['customer_name02'], array(
            new Assert\NotBlank(),
            new Assert\Length(array('max' => $app['config']['name_len'], )),
            new Assert\Regex(array('pattern' => '/^[^\s ]+$/u', 'message' => 'form.type.name.firstname.nothasspace'))
        ));

        $errors[] = $app['validator']->validateValue($data['customer_company_name'], array(
            new Assert\Length(array('max' => $app['config']['stext_len'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_tel01'], array(
            new Assert\NotBlank(),
            new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
            new Assert\Length(array('max' => $app['config']['tel_len'], 'min' => $app['config']['tel_len_min'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_tel02'], array(
            new Assert\NotBlank(),
            new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
            new Assert\Length(array('max' => $app['config']['tel_len'], 'min' => $app['config']['tel_len_min'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_tel03'], array(
            new Assert\NotBlank(),
            new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
            new Assert\Length(array('max' => $app['config']['tel_len'], 'min' => $app['config']['tel_len_min'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_zip01'], array(
            new Assert\NotBlank(),
            new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
            new Assert\Length(array('min' => $app['config']['zip01_len'], 'max' => $app['config']['zip01_len'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_zip02'], array(
            new Assert\NotBlank(),
            new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
            new Assert\Length(array('min' => $app['config']['zip02_len'], 'max' => $app['config']['zip02_len'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_addr01'], array(
            new Assert\NotBlank(),
            new Assert\Length(array('max' => $app['config']['address1_len'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_addr02'], array(
            new Assert\NotBlank(),
            new Assert\Length(array('max' => $app['config']['address2_len'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_email'], array(
            new Assert\NotBlank(),
            new Assert\Email(),
        ));

        return $errors;
    }
}
