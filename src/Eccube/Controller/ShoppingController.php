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
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Exception\ShoppingException;
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
            log_info('カートが存在しません');
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        // カートチェック
        if (count($cartService->getCart()->getCartItems()) <= 0) {
            log_info('カートに商品が入っていないためショッピングカート画面にリダイレクト');
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
                    log_info('未ログインのためログイン画面にリダイレクト');
                    return $app->redirect($app->url('shopping_login'));
                }
            } else {
                $Customer = $app->user();
            }

            try {
                // 受注情報を作成
                $Order = $app['eccube.service.shopping']->createOrder($Customer);
            } catch (CartException $e) {
                log_error('初回受注情報作成エラー', array($e->getMessage()));
                $app->addRequestError($e->getMessage());
                return $app->redirect($app->url('cart'));
            }

            // セッション情報を削除
            $app['session']->remove($this->sessionOrderKey);
            $app['session']->remove($this->sessionMultipleKey);
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

        if ($Order->getTotalPrice() < 0) {
            // 合計金額がマイナスの場合、エラー
            log_info('受注金額マイナスエラー', array($Order->getId()));
            $message = $app->trans('shopping.total.price', array('totalPrice' => number_format($Order->getTotalPrice())));
            $app->addError($message);

            return $app->redirect($app->url('shopping_error'));
        }

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
            log_info('カートが存在しません');
            return $app->redirect($app->url('cart'));
        }

        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            log_info('購入処理中の受注情報がないため購入エラー');
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

            log_info('購入処理開始', array($Order->getId()));

            // トランザクション制御
            $em = $app['orm.em'];
            $em->getConnection()->beginTransaction();
            try {

                // お問い合わせ、配送時間などのフォーム項目をセット
                $app['eccube.service.shopping']->setFormData($Order, $data);
                // 購入処理
                $app['eccube.service.shopping']->processPurchase($Order);

                $em->flush();
                $em->getConnection()->commit();

                log_info('購入処理完了', array($Order->getId()));

            } catch (ShoppingException $e) {

                log_error('購入エラー', array($e->getMessage()));

                $em->getConnection()->rollback();

                $app->log($e);
                $app->addError($e->getMessage());

                return $app->redirect($app->url('shopping_error'));
            } catch (\Exception $e) {

                log_error('予期しないエラー', array($e->getMessage()));

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
                log_info('イベントレスポンス返却', array($Order->getId()));
                return $event->getResponse();
            }

            // 受注IDをセッションにセット
            $app['session']->set($this->sessionOrderKey, $Order->getId());

            // メール送信
            $MailHistory = $app['eccube.service.shopping']->sendOrderMail($Order);

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
                log_info('イベントレスポンス返却', array($Order->getId()));
                return $event->getResponse();
            }

            // 完了画面表示
            return $app->redirect($app->url('shopping_complete'));
        }

        log_info('購入チェックエラー', array($Order->getId()));

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

        log_info('購入処理完了', array($orderId));

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
            log_info('カートが存在しません');
            return $app->redirect($app->url('cart'));
        }

        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            log_info('購入処理中の受注情報がないため購入エラー');
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
            log_info('配送業者変更処理開始', array($Order->getId()));

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

            // 合計金額の再計算
            $Order = $app['eccube.service.shopping']->getAmount($Order);

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

            log_info('配送業者変更処理完了', array($Order->getId()));
            return $app->redirect($app->url('shopping'));
        }

        log_info('配送業者変更入力チェックエラー', array($Order->getId()));
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
            log_info('購入処理中の受注情報がないため購入エラー');
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

            log_info('支払い方法変更処理開始', array("id" => $Order->getId()));

            $data = $form->getData();
            $payment = $data['payment'];
            $message = $data['message'];

            $Order->setPayment($payment);
            $Order->setPaymentMethod($payment->getMethod());
            $Order->setMessage($message);
            $Order->setCharge($payment->getCharge());

            // 合計金額の再計算
            $Order = $app['eccube.service.shopping']->getAmount($Order);

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

            log_info('支払い方法変更処理完了', array("id" => $Order->getId(), "payment" => $payment->getId()));

            return $app->redirect($app->url('shopping'));
        }

        log_info('支払い方法変更入力チェックエラー', array("id" => $Order->getId()));
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
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_CHANGE_INITIALIZE, $event);

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
            log_info('カートが存在しません');
            return $app->redirect($app->url('cart'));
        }

        if ('POST' === $request->getMethod()) {
            $address = $request->get('address');

            if (is_null($address)) {
                // 選択されていなければエラー
                log_info('お届け先入力チェックエラー');
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
                throw new NotFoundHttpException('選択されたお届け先住所が存在しない');
            }

            $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
            if (!$Order) {
                log_info('購入処理中の受注情報がないため購入エラー');
                $app->addError('front.shopping.order.error');

                return $app->redirect($app->url('shopping_error'));
            }

            $Shipping = $Order->findShipping($id);
            if (!$Shipping) {
                throw new NotFoundHttpException('お届け先情報が存在しない');
            }

            log_info('お届先情報更新開始', array($Shipping->getId()));

            // お届け先情報を更新
            $Shipping
                ->setFromCustomerAddress($CustomerAddress);

            // 配送料金の設定
            $app['eccube.service.shopping']->setShippingDeliveryFee($Shipping);

            // 合計金額の再計算
            $Order = $app['eccube.service.shopping']->getAmount($Order);

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

            log_info('お届先情報更新完了', array($Shipping->getId()));
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
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_CHANGE_INITIALIZE, $event);

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
                throw new NotFoundHttpException('配送先住所最大数エラー');
            }
        }

        // カートチェック
        if (!$app['eccube.service.cart']->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            log_info('カートが存在しません');
            return $app->redirect($app->url('cart'));
        }

        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            log_info('購入処理中の受注情報がないため購入エラー');
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

        $Shipping = $Order->findShipping($id);
        if (!$Shipping) {
            throw new NotFoundHttpException('設定されている配送先が存在しない');
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
                'CustomerAddress' => $CustomerAddress,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            log_info('お届け先追加処理開始', array('id' => $Order->getId(), 'shipping' => $id));

            // 会員の場合、お届け先情報を新規登録
            $Shipping->setFromCustomerAddress($CustomerAddress);

            if ($Customer instanceof Customer) {
                $app['orm.em']->persist($CustomerAddress);
                log_info('新規お届け先登録', array(
                    'id' => $Order->getId(),
                    'shipping' => $id,
                    'customer address' => $CustomerAddress->getId()));
            }

            // 配送料金の設定
            $app['eccube.service.shopping']->setShippingDeliveryFee($Shipping);

            // 合計金額の再計算
            $app['eccube.service.shopping']->getAmount($Order);

            // 配送先を更新 
            $app['orm.em']->flush();

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Shipping' => $Shipping,
                    'CustomerAddress' => $CustomerAddress,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_COMPLETE, $event);

            log_info('お届け先追加処理完了', array('id' => $Order->getId(), 'shipping' => $id));
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

                log_info('非会員お客様情報変更処理開始');

                $data = $request->request->all();

                // 入力チェック
                $errors = $this->customerValidation($app, $data);

                foreach ($errors as $error) {
                    if ($error->count() != 0) {
                        log_info('非会員お客様情報変更入力チェックエラー');
                        $response = new Response(json_encode('NG'), 400);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                }

                $pref = $app['eccube.repository.master.pref']->findOneBy(array('name' => $data['customer_pref']));
                if (!$pref) {
                    log_info('非会員お客様情報変更入力チェックエラー');
                    $response = new Response(json_encode('NG'), 400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

                $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
                if (!$Order) {
                    log_info('カートが存在しません');
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
                    ->setZipCode($data['customer_zip01'].$data['customer_zip02'])
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

                log_info('非会員お客様情報変更処理完了', array($Order->getId()));
                $response = new Response(json_encode('OK'));
                $response->headers->set('Content-Type', 'application/json');
            } catch (\Exception $e) {
                log_error('予期しないエラー', array($e->getMessage()));
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
            log_info('カートが存在しません');
            return $app->redirect($app->url('cart'));
        }

        // ログイン済みの場合は, 購入画面へリダイレクト.
        if ($app->isGranted('ROLE_USER')) {
            return $app->redirect($app->url('shopping'));
        }

        // カートチェック
        if (count($cartService->getCart()->getCartItems()) <= 0) {
            // カートが存在しない時はエラー
            log_info('カートに商品が入っていないためショッピングカート画面にリダイレクト');
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

            log_info('非会員お客様情報登録開始');

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
                ->setZipCode($data['zip01'].$data['zip02'])
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
                ->setZipCode($data['zip01'].$data['zip02'])
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
                    $Order = $app['eccube.service.shopping']->createOrder($Customer);
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

            log_info('非会員お客様情報登録完了', array($Order->getId()));

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
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_CHANGE_INITIALIZE, $event);

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
            log_info('カートが存在しません');
            return $app->redirect($app->url('cart'));
        }

        // カートチェック
        if (count($cartService->getCart()->getCartItems()) <= 0) {
            // カートが存在しない時はエラー
            log_info('カートに商品が入っていないためショッピングカート画面にリダイレクト');
            return $app->redirect($app->url('cart'));
        }

        /** @var \Eccube\Entity\Order $Order */
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            log_info('購入処理中の受注情報がないため購入エラー');
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

            log_info('複数配送設定処理開始', array($Order->getId()));
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
                        $errors[] = array('message' => $app->trans('shopping.multiple.quantity.diff'));

                        // 対象がなければエラー
                        log_info('複数配送設定入力チェックエラー', array($Order->getId()));
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

                        $Order->addShipping($Shipping);
                    }
                }
            }

            // 合計金額の再計算
            $Order = $app['eccube.service.shopping']->getAmount($Order);

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

            log_info('複数配送設定処理完了', array($Order->getId()));
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
            log_info('カートが存在しません');
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

            log_info('非会員お届け先追加処理開始');

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

            log_info('非会員お届け先追加処理完了');

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
     *
     * @param Application $app
     * @param array $data リクエストパラメータ
     * @return array
     */
    private function customerValidation(Application $app, array $data)
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
            new Assert\Length(array('max' => $app['config']['name_len'],)),
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
            new Assert\Email(array('strict' => true)),
        ));

        return $errors;
    }
}
