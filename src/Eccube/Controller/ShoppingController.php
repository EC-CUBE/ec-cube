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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\ForwardOnly;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Order;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Exception\ShoppingException;
use Eccube\Form\Type\Front\CustomerLoginType;
use Eccube\Form\Type\Front\ShoppingShippingType;
use Eccube\Form\Type\Shopping\OrderType;
use Eccube\Repository\CustomerAddressRepository;
use Eccube\Service\CartService;
use Eccube\Service\OrderHelper;
use Eccube\Service\ShoppingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route(service=ShoppingController::class)
 */
class ShoppingController extends AbstractShoppingController
{
    /**
     * @Inject(BaseInfo::class)
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @Inject(OrderHelper::class)
     * @var OrderHelper
     */
    protected $orderHelper;

    /**
     * @Inject(CartService::class)
     * @var CartService
     */
    protected $cartService;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(ShoppingService::class)
     * @var ShoppingService
     */
    protected $shoppingService;

    /**
     * @Inject(CustomerAddressRepository::class)
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepository;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("session")
     * @var Session
     */
    protected $session;

    /**
     * @Inject("request_scope")
     * @var ParameterBag
     */
    protected $parameterBag;

    /**
     * 購入画面表示
     *
     * @Route("/shopping", name="shopping")
     * @Template("Shopping/index.twig")
     */
    public function index(Application $app, Request $request)
    {
        // カートチェック
        $response = $app->forward($app->path("shopping_check_to_cart"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注情報を初期化
        $response = $app->forward($app->path("shopping_initialize_order"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        /** @var Order $Order */
        $Order = $this->parameterBag->get('Order');

        // 単価集計
        $flowResult = $this->executePurchaseFlow($app, $Order);

        // フォームを生成する
        $app->forward($app->path("shopping_create_form"));

        if ($flowResult->hasWarning() || $flowResult->hasError()) {
            return $app->redirect($app->url('cart'));
        }

        // 複数配送の場合、エラーメッセージを一度だけ表示
        $app->forward($app->path("shopping_handle_multiple_errors"));
        $form = $this->parameterBag->get(OrderType::class);

        return [
            'form' => $form->createView(),
            'Order' => $Order,
        ];
    }

    /**
     * 購入確認画面から, 他の画面へのリダイレクト.
     * 配送業者や支払方法、お問い合わせ情報をDBに保持してから遷移する.
     *
     * @Route("/shopping/redirect", name="shopping_redirect_to")
     * @Template("Shopping/index.twig")
     */
    public function redirectTo(Application $app, Request $request)
    {
        // カートチェック
        $response = $app->forward($app->path("shopping_check_to_cart"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注の存在チェック
        $response = $app->forward($app->path("shopping_exists_order"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // フォームの生成
        $app->forward($app->path("shopping_create_form"));
        $form = $this->parameterBag->get(OrderType::class);
        $form->handleRequest($request);

        // 各種変更ページへリダイレクトする
        $response = $app->forward($app->path("shopping_redirect_to_change"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }
        $form = $this->parameterBag->get(OrderType::class);
        $Order = $this->parameterBag->get('Order');

        return [
            'form' => $form->createView(),
            'Order' => $Order,
        ];
    }

    /**
     * 購入処理
     *
     * @Route("/shopping/confirm", name="shopping_confirm")
     * @Method("POST")
     * @Template("Shopping/confirm.twig")
     */
    public function confirm(Application $app, Request $request)
    {
        // カートチェック
        $response = $app->forward($app->path("shopping_check_to_cart"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注の存在チェック
        $response = $app->forward($app->path("shopping_exists_order"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // フォームの生成
        $app->forward($app->path("shopping_create_form"));
        $form = $this->parameterBag->get(OrderType::class);
        $form->handleRequest($request);

        $form = $this->parameterBag->get(OrderType::class);
        $Order = $this->parameterBag->get('Order');

        $flowResult = $this->executePurchaseFlow($app, $Order);
        if ($flowResult->hasWarning() || $flowResult->hasError()) {
            return $app->redirect($app->url('shopping_error'));
        }

        return [
            'form' => $form->createView(),
            'Order' => $Order,
        ];
    }

    /**
     * 購入処理
     *
     * @Route("/shopping/order", name="shopping_order")
     * @Method("POST")
     * @Template("Shopping/index.twig")
     */
    public function order(Application $app, Request $request)
    {
        // カートチェック
        $response = $app->forward($app->path("shopping_check_to_cart"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注の存在チェック
        $response = $app->forward($app->path("shopping_exists_order"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // form作成
        // FIXME イベントハンドラを外から渡したい
        $app->forward($app->path("shopping_create_form"));

        $form = $this->parameterBag->get(OrderType::class);
        $Order = $this->parameterBag->get('Order');

        $form->handleRequest($request);

        // 受注処理
        $response = $app->forward($app->path("shopping_complete_order"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        log_info('購入チェックエラー', array($Order->getId()));

        return [
            'form' => $form->createView(),
            'Order' => $Order,
        ];
    }


    /**
     * 購入完了画面表示
     *
     * @Route("/shopping/complete", name="shopping_complete")
     * @Template("Shopping/complete.twig")
     */
    public function complete(Application $app, Request $request)
    {
        // 受注IDを取得
        $orderId = $this->session->get($this->sessionOrderKey);

        $event = new EventArgs(
            array(
                'orderId' => $orderId,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_COMPLETE_INITIALIZE, $event);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        // 受注に関連するセッションを削除
        $this->session->remove($this->sessionOrderKey);
        $this->session->remove($this->sessionMultipleKey);

        // 非会員用セッション情報を空の配列で上書きする(プラグイン互換性保持のために削除はしない)
        $this->session->set($this->sessionKey, array());
        $this->session->set($this->sessionCustomerAddressKey, array());

        log_info('購入処理完了', array($orderId));

        $hasNextCart = !empty($this->cartService->getCarts());

        return [
            'orderId' => $orderId,
            'hasNextCart' => $hasNextCart,
        ];
    }

    /**
     * お届け先の設定一覧からの選択
     *
     * @Route("/shopping/shipping/{id}", name="shopping_shipping", requirements={"id" = "\d+"})
     * @Template("Shopping/shipping.twig")
     */
    public function shipping(Application $app, Request $request, $id)
    {
        // カートチェック
        $response = $app->forward($app->path("shopping_check_to_cart"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        if ('POST' === $request->getMethod()) {
            $address = $request->get('address');

            if (is_null($address)) {
                // 選択されていなければエラー
                log_info('お届け先入力チェックエラー');

                return [
                    'Customer' => $app->user(),
                    'shippingId' => $id,
                    'error' => true,
                ];
            }

            // 選択されたお届け先情報を取得
            $CustomerAddress = $this->customerAddressRepository->findOneBy(
                array(
                    'Customer' => $app->user(),
                    'id' => $address,
                )
            );
            if (is_null($CustomerAddress)) {
                throw new NotFoundHttpException('選択されたお届け先住所が存在しない');
            }

            /** @var Order $Order */
            $Order = $this->shoppingService->getOrder($this->appConfig['order_processing']);
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
            $Shipping->setFromCustomerAddress($CustomerAddress);

            // 配送料金の設定
            $this->shoppingService->setShippingDeliveryFee($Shipping);


            // 合計金額の再計算
            $flowResult = $this->executePurchaseFlow($app, $Order);
            if ($flowResult->hasWarning() || $flowResult->hasError()) {
                return $app->redirect($app->url('shopping_error'));
            }

            // 配送先を更新
            $this->entityManager->flush();

            $event = new EventArgs(
                array(
                    'Order' => $Order,
                    'shippingId' => $id,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_COMPLETE, $event);

            log_info('お届先情報更新完了', array($Shipping->getId()));

            return $app->redirect($app->url('shopping'));
        }

        return [
            'Customer' => $app->user(),
            'shippingId' => $id,
            'error' => false,
        ];
    }

    /**
     * お届け先の設定(非会員でも使用する)
     *
     * @Route("/shopping/shipping_edit/{id}", name="shopping_shipping_edit", requirements={"id" = "\d+"})
     * @Template("Shopping/shipping_edit.twig")
     */
    public function shippingEdit(Application $app, Request $request, $id)
    {
        // 配送先住所最大値判定
        $Customer = $app->user();
        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            $addressCurrNum = count($app->user()->getCustomerAddresses());
            $addressMax = $this->appConfig['deliv_addr_max'];
            if ($addressCurrNum >= $addressMax) {
                throw new NotFoundHttpException('配送先住所最大数エラー');
            }
        }

        // カートチェック
        $response = $app->forward($app->path("shopping_check_to_cart"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注の存在チェック
        $response = $app->forward($app->path("shopping_exists_order"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        /** @var Order $Order */
        $Order = $this->parameterBag->get('Order');

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

        $builder = $this->formFactory->createBuilder(ShoppingShippingType::class, $CustomerAddress);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
                'Shipping' => $Shipping,
                'CustomerAddress' => $CustomerAddress,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            log_info('お届け先追加処理開始', array('id' => $Order->getId(), 'shipping' => $id));

            // 会員の場合、お届け先情報を新規登録
            $Shipping->setFromCustomerAddress($CustomerAddress);

            if ($Customer instanceof Customer) {
                $this->entityManager->persist($CustomerAddress);
                log_info(
                    '新規お届け先登録',
                    array(
                        'id' => $Order->getId(),
                        'shipping' => $id,
                        'customer address' => $CustomerAddress->getId(),
                    )
                );
            }

            // 配送料金の設定
            $this->shoppingService->setShippingDeliveryFee($Shipping);

            // 合計金額の再計算
            $flowResult = $this->executePurchaseFlow($app, $Order);
            if ($flowResult->hasWarning() || $flowResult->hasError()) {
                return $app->redirect($app->url('shopping_error'));
            }

            // 配送先を更新
            $this->entityManager->flush();

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Shipping' => $Shipping,
                    'CustomerAddress' => $CustomerAddress,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_COMPLETE, $event);

            log_info('お届け先追加処理完了', array('id' => $Order->getId(), 'shipping' => $id));

            return $app->redirect($app->url('shopping'));
        }

        return [
            'form' => $form->createView(),
            'shippingId' => $id,
        ];
    }

    /**
     * ログイン
     *
     * @Route("/shopping/login", name="shopping_login")
     * @Template("Shopping/login.twig")
     */
    public function login(Application $app, Request $request)
    {
        if (!$this->cartService->isLocked()) {
            return $app->redirect($app->url('cart'));
        }

        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $app->redirect($app->url('shopping'));
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $builder = $this->formFactory->createNamedBuilder('', CustomerLoginType::class);

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
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_LOGIN_INITIALIZE, $event);

        $form = $builder->getForm();

        return [
            'error' => $app['security.last_error']($request),
            'form' => $form->createView(),
        ];
    }

    /**
     * 購入エラー画面表示
     *
     * @Route("/shopping/error", name="shopping_error")
     * @Template("Shopping/shopping_error.twig")
     */
    public function shoppingError(Application $app, Request $request)
    {
        $event = new EventArgs(
            array(),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_ERROR_COMPLETE, $event);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        return [];
    }

    /**
     * カート画面のチェック
     *
     * @ForwardOnly
     * @Route("/shopping/check_to_cart", name="shopping_check_to_cart")
     */
    public function checkToCart(Application $app, Request $request)
    {
        // カートチェック
        if (!$this->cartService->isLocked()) {
            log_info('カートが存在しません');

            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        // カートチェック
        if (count($this->cartService->getCart()->getCartItems()) <= 0) {
            log_info('カートに商品が入っていないためショッピングカート画面にリダイレクト');

            // カートが存在しない時はエラー
            return $app->redirect($app->url('cart'));
        }

        return new Response();
    }

    /**
     * 受注情報を初期化する.
     *
     * @ForwardOnly
     * @Route("/shopping/initialize_order", name="shopping_initialize_order")
     */
    public function initializeOrder(Application $app, Request $request)
    {
        // 購入処理中の受注情報を取得
        $Order = $this->shoppingService->getOrder($this->appConfig['order_processing']);

        // 初回アクセス(受注情報がない)の場合は, 受注情報を作成
        if (is_null($Order)) {
            // 未ログインの場合, ログイン画面へリダイレクト.
            if (!$app->isGranted('IS_AUTHENTICATED_FULLY')) {
                // 非会員でも一度会員登録されていればショッピング画面へ遷移
                $Customer = $this->shoppingService->getNonMember($this->sessionKey);

                if (is_null($Customer)) {
                    log_info('未ログインのためログイン画面にリダイレクト');

                    return $app->redirect($app->url('shopping_login'));
                }
            } else {
                $Customer = $app->user();
            }

            try {
                // 受注情報を作成
                //$Order = $app['eccube.service.shopping']->createOrder($Customer);
                $Order = $this->orderHelper->createProcessingOrder(
                    $Customer,
                    $Customer->getCustomerAddresses()->current(),
                    $this->cartService->getCart()->getCartItems()
                );
                $this->cartService->setPreOrderId($Order->getPreOrderId());
                $this->cartService->save();
            } catch (CartException $e) {
                log_error('初回受注情報作成エラー', array($e->getMessage()));
                $app->addRequestError($e->getMessage());

                return $app->redirect($app->url('cart'));
            }

            // セッション情報を削除
            $this->session->remove($this->sessionOrderKey);
            $this->session->remove($this->sessionMultipleKey);
        }

        // 受注関連情報を最新状態に更新
        $this->entityManager->refresh($Order);

        $this->parameterBag->set('Order', $Order);

        return new Response();
    }

    /**
     * フォームを作成し, イベントハンドラを設定する
     *
     * @ForwardOnly
     * @Route("/shopping/create_form", name="shopping_create_form")
     */
    public function createForm(Application $app, Request $request)
    {
        $Order = $this->parameterBag->get('Order');
        // フォームの生成
        $builder = $this->formFactory->createBuilder(OrderType::class, $Order);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $this->parameterBag->set(OrderType::class, $form);

        return new Response();
    }

    /**
     * mode に応じて各変更ページへリダイレクトする.
     *
     * @ForwardOnly
     * @Route("/shopping/redirect_to_change", name="shopping_redirect_to_change")
     */
    public function redirectToChange(Application $app, Request $request)
    {
        $form = $this->parameterBag->get(OrderType::class);

        // requestのバインド後、Calculatorに再集計させる
        //$app['eccube.service.calculate']($Order, $Order->getCustomer())->calculate();

        // 支払い方法の変更や配送業者の変更があった場合はDBに保持する.
        if ($form->isSubmitted() && $form->isValid()) {
            // POSTされたデータをDBに保持.
            $this->entityManager->flush();

            $mode = $form['mode']->getData();
            switch ($mode) {
                case 'shipping_change':
                    // お届け先設定一覧へリダイレクト
                    $param = $form['param']->getData();

                    return $app->redirect($app->url('shopping_shipping', array('id' => $param)));
                case 'shipping_edit_change':
                    // お届け先設定一覧へリダイレクト
                    $param = $form['param']->getData();

                    return $app->redirect($app->url('shopping_shipping_edit', array('id' => $param)));
                case 'shipping_multiple_change':
                    // 複数配送設定へリダイレクト
                    return $app->redirect($app->url('shopping_shipping_multiple'));
                case 'payment':
                case 'delivery':
                default:
                    return $app->redirect($app->url('shopping'));
            }
        }

        return new Response();
    }

    /**
     * 複数配送時のエラーを表示する
     *
     * @ForwardOnly
     * @Route("/shopping/handle_multiple_errors", name="shopping_handle_multiple_errors")
     */
    public function handleMultipleErrors(Application $app, Request $request)
    {
        $Order = $this->parameterBag->get('Order');

        // 複数配送の場合、エラーメッセージを一度だけ表示
        if (!$this->session->has($this->sessionMultipleKey)) {
            if (count($Order->getShippings()) > 1) {
                if (!$this->BaseInfo->isOptionMultipleShipping()) {
                    // 複数配送に設定されていないのに複数配送先ができればエラー
                    $app->addRequestError('cart.product.type.kind');

                    return $app->redirect($app->url('cart'));
                }

                $app->addError('shopping.multiple.delivery');
            }
            $this->session->set($this->sessionMultipleKey, 'multiple');
        }

        return new Response();
    }

    /**
     * 受注の存在チェック
     *
     * @ForwardOnly
     * @Route("/shopping/exists_order", name="shopping_exists_order")
     */
    public function existsOrder(Application $app, Request $request)
    {
        $Order = $this->shoppingService->getOrder($this->appConfig['order_processing']);
        if (!$Order) {
            log_info('購入処理中の受注情報がないため購入エラー');
            $app->addError('front.shopping.order.error');

            return $app->redirect($app->url('shopping_error'));
        }
        $this->parameterBag->set('Order', $Order);

        return new Response();
    }

    /**
     * 受注完了処理
     *
     * @ForwardOnly
     * @Route("/shopping/complete_order", name="shopping_complete_order")
     */
    public function completeOrder(Application $app, Request $request)
    {
        $form = $this->parameterBag->get(OrderType::class);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Order $Order */
            $Order = $form->getData();
            log_info('購入処理開始', array($Order->getId()));

            // トランザクション制御
            $em = $this->entityManager;
            $em->getConnection()->beginTransaction();
            try {

                // お問い合わせ、配送時間などのフォーム項目をセット
                // FormTypeで更新されるため不要
                //$app['eccube.service.shopping']->setFormData($Order, $data);

                $flowResult = $this->executePurchaseFlow($app, $Order);
                if ($flowResult->hasWarning() || $flowResult->hasError()) {
                    // TODO エラーメッセージ
                    throw new ShoppingException();
                }
                try {
                    $this->purchaseFlow->purchase($Order, $app['eccube.purchase.context']($Order, $Order->getCustomer())); // TODO 変更前の Order を渡す必要がある？
                } catch (PurchaseException $e) {
                    $app->addError($e->getMessage(), 'front');
                }

                // 購入処理
                $this->shoppingService->processPurchase($Order); // XXX フロント画面に依存してるので管理画面では使えない

                // Order も引数で渡すのがベスト??
                $paymentService = $app['eccube.service.payment']($Order->getPayment()->getServiceClass());

                $paymentMethod = $app['payment.method.request'](
                    $Order->getPayment()->getMethodClass(),
                    $form,
                    $request
                );
                // 必要に応じて別のコントローラへ forward or redirect(移譲)
                // forward の処理はプラグイン内で書けるようにしておく
                // dispatch をしたら, パスを返して forwardする
                // http://silex.sensiolabs.org/doc/cookbook/sub_requests.html
                // 確認画面も挟める
                // Request をセッションに入れるべし
                $dispatcher = $paymentService->dispatch($paymentMethod); // 決済処理中.
                // 一旦、決済処理中になった後は、購入処理中に戻せない。キャンセル or 購入完了の仕様とする
                // ステータス履歴も保持しておく？ 在庫引き当ての仕様もセットで。
                if ($dispatcher instanceof Response
                    && ($dispatcher->isRedirection() || $dispatcher->getContent())
                ) { // $paymentMethod->apply() が Response を返した場合は画面遷移
                    return $dispatcher;                // 画面遷移したいパターンが複数ある場合はどうする？ 引数で制御？
                }
                $PaymentResult = $paymentService->doCheckout($paymentMethod); // 決済実行
                if (!$PaymentResult->isSuccess()) {
                    $em->getConnection()->rollback();

                    return $app->redirect($app->url('shopping_error'));
                }

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

            return $app->forward($app->path('shopping_after_complete'));
        }

        return new Response();
    }

    /**
     * 受注完了の後処理
     *
     * @ForwardOnly
     * @Route("/shopping/after_complete", name="shopping_after_complete")
     */
    public function afterComplete(Application $app, Request $request)
    {
        $form = $this->parameterBag->get(OrderType::class);
        $Order = $this->parameterBag->get('Order');

        // カート削除
        $this->cartService->clear()->save();

        $event = new EventArgs(
            array(
                'form' => $form,
                'Order' => $Order,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_CONFIRM_PROCESSING, $event);

        if ($event->getResponse() !== null) {
            log_info('イベントレスポンス返却', array($Order->getId()));

            return $event->getResponse();
        }

        // 受注IDをセッションにセット
        $this->session->set($this->sessionOrderKey, $Order->getId());

        // メール送信
        $MailHistory = $this->shoppingService->sendOrderMail($Order);

        $event = new EventArgs(
            array(
                'form' => $form,
                'Order' => $Order,
                'MailHistory' => $MailHistory,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_CONFIRM_COMPLETE, $event);

        if ($event->getResponse() !== null) {
            log_info('イベントレスポンス返却', array($Order->getId()));

            return $event->getResponse();
        }

        // 完了画面表示
        return $app->redirect($app->url('shopping_complete'));
    }
}
