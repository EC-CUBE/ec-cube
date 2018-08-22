<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller;

use Eccube\Annotation\ForwardOnly;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Exception\ShoppingException;
use Eccube\Form\Type\Front\CustomerLoginType;
use Eccube\Form\Type\Front\ShoppingShippingType;
use Eccube\Form\Type\Shopping\CustomerAddressType;
use Eccube\Form\Type\Shopping\OrderType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerAddressRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\CartService;
use Eccube\Service\OrderHelper;
use Eccube\Service\Payment\PaymentDispatcher;
use Eccube\Service\ShoppingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ShoppingController extends AbstractShoppingController
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var OrderHelper
     */
    protected $orderHelper;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var ShoppingService
     */
    protected $shoppingService;

    /**
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepository;

    /**
     * @var ParameterBag
     */
    protected $parameterBag;

    /**
     * ShoppingController constructor.
     *
     * @param BaseInfoRepository $baseInfoRepository
     * @param OrderHelper $orderHelper
     * @param CartService $cartService
     * @param ShoppingService $shoppingService
     * @param CustomerAddressRepository $customerAddressRepository
     * @param ParameterBag $parameterBag
     */
    public function __construct(
        BaseInfoRepository $baseInfoRepository,
        OrderHelper $orderHelper,
        CartService $cartService,
        ShoppingService $shoppingService,
        CustomerAddressRepository $customerAddressRepository,
        OrderRepository $orderRepository,
        ParameterBag $parameterBag
    ) {
        $this->BaseInfo = $baseInfoRepository->get();
        $this->orderHelper = $orderHelper;
        $this->cartService = $cartService;
        $this->shoppingService = $shoppingService;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->orderRepository = $orderRepository;
        $this->parameterBag = $parameterBag;
    }

    /**
     * 購入画面表示
     *
     * @Route("/shopping", name="shopping")
     * @Template("Shopping/index.twig")
     */
    public function index(Request $request)
    {
        // カートチェック
        $response = $this->forwardToRoute('shopping_check_to_cart');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注情報を初期化
        $response = $this->forwardToRoute('shopping_initialize_order');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        /** @var Order $Order */
        $Order = $this->parameterBag->get('Order');

        // 単価集計
        $flowResult = $this->validatePurchaseFlow($Order);

        // 明細が丸められる場合に, カートから注文画面へ遷移できなくなるため, 集計の結果を保存する
        $this->entityManager->flush();

        // フォームを生成する
        $this->forwardToRoute('shopping_create_form');

        if ($flowResult->hasWarning() || $flowResult->hasError()) {
            return $this->redirectToRoute('cart');
        }

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
    public function redirectTo(Request $request)
    {
        // カートチェック
        $response = $this->forwardToRoute('shopping_check_to_cart');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注の存在チェック
        $response = $this->forwardToRoute('shopping_exists_order');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // フォームの生成
        $this->forwardToRoute('shopping_create_form');
        $form = $this->parameterBag->get(OrderType::class);
        $form->handleRequest($request);

        // 各種変更ページへリダイレクトする
        $response = $this->forwardToRoute('shopping_redirect_to_change');
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
     * @Route("/shopping/confirm", name="shopping_confirm", methods={"POST"})
     * @Template("Shopping/confirm.twig")
     */
    public function confirm(Request $request)
    {
        // カートチェック
        $response = $this->forwardToRoute('shopping_check_to_cart');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注の存在チェック
        $response = $this->forwardToRoute('shopping_exists_order');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // フォームの生成
        $this->forwardToRoute('shopping_create_form');
        $form = $this->parameterBag->get(OrderType::class);
        $form->handleRequest($request);

        $form = $this->parameterBag->get(OrderType::class);
        $Order = $this->parameterBag->get('Order');

        // フォームエラーチェック
        if (!$form->isValid()) {
            $response = $this->forwardToRoute('shopping_redirect_to');

            return $response;
        }

        $flowResult = $this->validatePurchaseFlow($Order);
        if ($flowResult->hasWarning() || $flowResult->hasError()) {
            return $this->redirectToRoute('shopping_error');
        }

        $paymentMethod = $this->createPaymentMethod($Order, $form);

        $PaymentResult = $paymentMethod->verify();
        // エラーの場合は注文入力画面に戻す？
        if ($PaymentResult instanceof PaymentResult) {
            if (!$PaymentResult->isSuccess()) {
                $this->entityManager->getConnection()->rollback();

                $this->addError($PaymentResult->getErrors());
            }

            $response = $PaymentResult->getResponse();
            if ($response && ($response->isRedirection() || $response->getContent())) {
                $this->entityManager->flush();

                return $response;
            }
        }
        $this->entityManager->flush();

        return [
            'form' => $form->createView(),
            'Order' => $Order,
        ];
    }

    /**
     * 購入処理
     *
     * @Route("/shopping/order", name="shopping_order", methods={"POST"})
     * @Template("Shopping/index.twig")
     */
    public function order(Request $request)
    {
        // カートチェック
        $response = $this->forwardToRoute('shopping_check_to_cart');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注の存在チェック
        $response = $this->forwardToRoute('shopping_exists_order');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // form作成
        // FIXME イベントハンドラを外から渡したい
        $this->forwardToRoute('shopping_create_form');

        $form = $this->parameterBag->get(OrderType::class);
        $Order = $this->parameterBag->get('Order');
        $usePoint = $Order->getUsePoint();

        $form->handleRequest($request);
        $Order->setUsePoint($usePoint);

        // 受注処理
        $response = $this->forwardToRoute('shopping_complete_order');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        log_info('購入チェックエラー', [$Order->getId()]);

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
    public function complete(Request $request)
    {
        // 受注IDを取得
        $orderId = $this->session->get($this->sessionOrderKey);

        if (empty($orderId)) {
            return $this->redirectToRoute('homepage');
        }

        $Order = $this->orderRepository->find($orderId);

        $event = new EventArgs(
            [
                'Order' => $Order,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_COMPLETE_INITIALIZE, $event);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        // 受注に関連するセッションを削除
        $this->session->remove($this->sessionOrderKey);
        $this->session->remove($this->sessionKey);
        $this->session->remove($this->sessionCustomerAddressKey);

        log_info('購入処理完了', [$Order->getId()]);

        $hasNextCart = !empty($this->cartService->getCarts());

        return [
            'Order' => $Order,
            'hasNextCart' => $hasNextCart,
        ];
    }

    /**
     * お届け先の設定一覧からの選択
     *
     * @Route("/shopping/shipping/{id}", name="shopping_shipping", requirements={"id" = "\d+"})
     * @Template("Shopping/shipping.twig")
     */
    public function shipping(Request $request, Shipping $Shipping)
    {
        // カートチェック
        $response = $this->forwardToRoute('shopping_check_to_cart');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注の存在チェック
        $response = $this->forwardToRoute('shopping_exists_order');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注に紐づくShippingかどうかのチェック.
        /** @var Order $Order */
        $Order = $this->parameterBag->get('Order');
        if (!$Order->findShipping($Shipping->getId())) {
            throw new NotFoundHttpException();
        }

        $builder = $this->formFactory->createBuilder(CustomerAddressType::class, null, [
            'customer' => $this->getUser(),
            'shipping' => $Shipping,
        ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('お届先情報更新開始', [$Shipping->getId()]);

            /** @var CustomerAddress $CustomerAddress */
            $CustomerAddress = $form['addresses']->getData();

            // お届け先情報を更新
            $Shipping->setFromCustomerAddress($CustomerAddress);

            // 合計金額の再計算
            $flowResult = $this->validatePurchaseFlow($Order);
            if ($flowResult->hasWarning() || $flowResult->hasError()) {
                return $this->redirectToRoute('shopping_error');
            }

            // 配送先を更新
            $this->entityManager->flush();

            $event = new EventArgs(
                [
                    'Order' => $Order,
                    'Shipping' => $Shipping,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_COMPLETE, $event);

            log_info('お届先情報更新完了', [$Shipping->getId()]);

            return $this->redirectToRoute('shopping');
        }

        return [
            'form' => $form->createView(),
            'Customer' => $this->getUser(),
            'shippingId' => $Shipping->getId(),
        ];
    }

    /**
     * お届け先の設定(非会員でも使用する)
     *
     * @Route("/shopping/shipping_edit/{id}", name="shopping_shipping_edit", requirements={"id" = "\d+"})
     * @Template("Shopping/shipping_edit.twig")
     */
    public function shippingEdit(Request $request, $id)
    {
        // 配送先住所最大値判定
        $Customer = $this->getUser();
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $addressCurrNum = count($this->getUser()->getCustomerAddresses());
            $addressMax = $this->eccubeConfig['eccube_deliv_addr_max'];
            if ($addressCurrNum >= $addressMax) {
                throw new NotFoundHttpException(trans('shoppingcontroller.text.error.number_of_address'));
            }
        }

        // カートチェック
        $response = $this->forwardToRoute('shopping_check_to_cart');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        // 受注の存在チェック
        $response = $this->forwardToRoute('shopping_exists_order');
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        /** @var Order $Order */
        $Order = $this->parameterBag->get('Order');

        $Shipping = $Order->findShipping($id);
        if (!$Shipping) {
            throw new NotFoundHttpException(trans('shoppingcontroller.text.error.set_address'));
        }
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $Shipping->clearCustomerAddress();
        }

        $CustomerAddress = new CustomerAddress();
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $CustomerAddress->setCustomer($Customer);
        } else {
            $CustomerAddress->setFromShipping($Shipping);
        }

        $builder = $this->formFactory->createBuilder(ShoppingShippingType::class, $CustomerAddress);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Order' => $Order,
                'Shipping' => $Shipping,
                'CustomerAddress' => $CustomerAddress,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('お届け先追加処理開始', ['id' => $Order->getId(), 'shipping' => $id]);

            // 会員の場合、お届け先情報を新規登録
            $Shipping->setFromCustomerAddress($CustomerAddress);

            if ($Customer instanceof Customer) {
                $this->entityManager->persist($CustomerAddress);
                log_info(
                    '新規お届け先登録',
                    [
                        'id' => $Order->getId(),
                        'shipping' => $id,
                        'customer address' => $CustomerAddress->getId(),
                    ]
                );
            }

            // 配送料金の設定
            $this->shoppingService->setShippingDeliveryFee($Shipping);

            // 合計金額の再計算
            $flowResult = $this->validatePurchaseFlow($Order);
            if ($flowResult->hasWarning() || $flowResult->hasError()) {
                return $this->redirectToRoute('shopping_error');
            }

            // 配送先を更新
            $this->entityManager->flush();

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Shipping' => $Shipping,
                    'CustomerAddress' => $CustomerAddress,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_COMPLETE, $event);

            log_info('お届け先追加処理完了', ['id' => $Order->getId(), 'shipping' => $id]);

            return $this->redirectToRoute('shopping');
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
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('shopping');
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $builder = $this->formFactory->createNamedBuilder('', CustomerLoginType::class);

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $Customer = $this->getUser();
            if ($Customer) {
                $builder->get('login_email')->setData($Customer->getEmail());
            }
        }

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_LOGIN_INITIALIZE, $event);

        $form = $builder->getForm();

        return [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'form' => $form->createView(),
        ];
    }

    /**
     * 購入エラー画面表示
     *
     * @Route("/shopping/error", name="shopping_error")
     * @Template("Shopping/shopping_error.twig")
     */
    public function shoppingError(Request $request)
    {
        $event = new EventArgs(
            [],
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
    public function checkToCart(Request $request)
    {
        $Cart = $this->cartService->getCart();
        if ($Cart && count($Cart->getCartItems()) > 0) {
            $divide = $request->getSession()->get('cart.divide');
            if ($divide) {
                log_info('種別が異なる商品がカートと結合されたためカート画面にリダイレクト');

                return $this->redirectToRoute('cart');
            }

            return new Response();
        }
        log_info('カートに商品が入っていないためショッピングカート画面にリダイレクト');

        // カートが存在しない時はエラー
        return $this->redirectToRoute('cart');
    }

    /**
     * 受注情報を初期化する.
     *
     * @ForwardOnly
     * @Route("/shopping/initialize_order", name="shopping_initialize_order")
     */
    public function initializeOrder(Request $request)
    {
        // 購入処理中の受注情報を取得
        $Order = $this->shoppingService->getOrder(OrderStatus::PROCESSING);

        // 初回アクセス(受注情報がない)の場合は, 受注情報を作成
        if (is_null($Order)) {
            // 未ログインの場合, ログイン画面へリダイレクト.
            if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
                // 非会員でも一度会員登録されていればショッピング画面へ遷移
                $Customer = $this->shoppingService->getNonMember($this->sessionKey);

                if (is_null($Customer)) {
                    log_info('未ログインのためログイン画面にリダイレクト');

                    return $this->redirectToRoute('shopping_login');
                }
            } else {
                $Customer = $this->getUser();
            }

            try {
                // 受注情報を作成
                $Order = $this->orderHelper->createProcessingOrder(
                    $Customer,
                    $this->cartService->getCart()->getCartItems()
                );
                $this->cartService->setPreOrderId($Order->getPreOrderId());
                $this->cartService->save();
            } catch (CartException $e) {
                log_error('初回受注情報作成エラー', [$e->getMessage()]);
                $this->addRequestError($e->getMessage());

                return $this->redirectToRoute('cart');
            }

            // セッション情報を削除
            $this->session->remove($this->sessionOrderKey);
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
    public function createShoppingForm(Request $request)
    {
        $Order = $this->parameterBag->get('Order');
        // フォームの生成
        $builder = $this->formFactory->createBuilder(OrderType::class, $Order);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Order' => $Order,
            ],
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
    public function redirectToChange(Request $request)
    {
        $form = $this->parameterBag->get(OrderType::class);

        // 支払い方法の変更や配送業者の変更があった場合はDBに保持する.
        if ($form->isSubmitted() && $form->isValid()) {
            // POSTされたデータをDBに保持.
            $this->entityManager->flush();

            $mode = $form['mode']->getData();
            switch ($mode) {
                case 'shipping_change':
                    // お届け先設定一覧へリダイレクト
                    $param = $form['param']->getData();

                    return $this->redirectToRoute('shopping_shipping', ['id' => $param]);
                case 'shipping_edit_change':
                    // お届け先設定一覧へリダイレクト
                    $param = $form['param']->getData();

                    return $this->redirectToRoute('shopping_shipping_edit', ['id' => $param]);
                case 'shipping_multiple_change':
                    // 複数配送設定へリダイレクト
                    return $this->redirectToRoute('shopping_shipping_multiple');
                case 'payment':
                case 'delivery':
                default:
                    return $this->redirectToRoute('shopping');
            }
        }

        return new Response();
    }

    /**
     * 受注の存在チェック
     *
     * @ForwardOnly
     * @Route("/shopping/exists_order", name="shopping_exists_order")
     */
    public function existsOrder(Request $request)
    {
        $Order = $this->shoppingService->getOrder(OrderStatus::PROCESSING);
        if (!$Order) {
            log_info('購入処理中の受注情報がないため購入エラー');
            $this->addError('front.shopping.order.error');

            return $this->redirectToRoute('shopping_error');
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
    public function completeOrder(Request $request)
    {
        $form = $this->parameterBag->get(OrderType::class);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Order $Order */
            $Order = $form->getData();
            log_info('購入処理開始', [$Order->getId()]);

            // トランザクション制御
            $em = $this->entityManager;
            $em->getConnection()->beginTransaction();
            try {
                $flowResult = $this->validatePurchaseFlow($Order);
                if ($flowResult->hasWarning() || $flowResult->hasError()) {
                    // TODO エラーメッセージ
                    throw new ShoppingException();
                }

                $paymentMethod = $this->createPaymentMethod($Order, $form);

                // 必要に応じて別のコントローラへ forward or redirect(移譲)
                $dispatcher = $paymentMethod->apply(); // 決済処理中.
                // 一旦、決済処理中になった後は、購入処理中に戻せない。キャンセル or 購入完了の仕様とする
                // ステータス履歴も保持しておく？ 在庫引き当ての仕様もセットで。
                if ($dispatcher instanceof PaymentDispatcher) {
                    $response = $dispatcher->getResponse();
                    $this->entityManager->flush();
                    $this->entityManager->commit();

                    if ($response && ($response->isRedirection() || $response->getContent())) {
                        return $response;
                    }

                    if ($dispatcher->isForward()) {
                        return $this->forwardToRoute($dispatcher->getRoute(), $dispatcher->getPathParameters(), $dispatcher->getQueryParameters());
                    } else {
                        return $this->redirectToRoute($dispatcher->getRoute(), array_merge($dispatcher->getPathParameters(), $dispatcher->getQueryParameters()));
                    }
                }

                // 決済実行
                $response = $this->forwardToRoute('shopping_do_checkout_order');
                $this->entityManager->flush();
                $this->entityManager->commit();

                if ($response->isRedirection() || $response->getContent()) {
                    return $response;
                }

                log_info('購入処理完了', [$Order->getId()]);
            } catch (ShoppingException $e) {
                log_error('購入エラー', [$e->getMessage()]);

                $this->entityManager->getConnection()->rollback();

                $this->addError($e->getMessage());

                return $this->redirectToRoute('shopping_error');
            } catch (\Exception $e) {
                log_error('予期しないエラー', [$e->getMessage()]);

                $this->entityManager->getConnection()->rollback();

                $this->addError('front.shopping.system.error');

                return $this->redirectToRoute('shopping_error');
            }

            return $this->forwardToRoute('shopping_after_complete');
        }

        return new Response();
    }

    /**
     * 決済完了処理
     *
     * @ForwardOnly
     * @Route("/shopping/do_checkout_order", name="shopping_do_checkout_order")
     */
    public function doCheckoutOrder(Request $request)
    {
        $form = $this->parameterBag->get(OrderType::class);
        $Order = $this->parameterBag->get('Order');

        $paymentMethod = $this->createPaymentMethod($Order, $form);

        // 決済実行
        $PaymentResult = $paymentMethod->checkout();
        $response = $PaymentResult->getResponse();
        if ($response && ($response->isRedirection() || $response->getContent())) {
            return $response;
        }

        if (!$PaymentResult->isSuccess()) {
            $this->entityManager->getConnection()->rollback();

            $this->addError($PaymentResult->getErrors());
        }

        return new Response();
    }

    /**
     * 受注完了の後処理
     *
     * @ForwardOnly
     * @Route("/shopping/after_complete", name="shopping_after_complete")
     */
    public function afterComplete(Request $request)
    {
        $form = $this->parameterBag->get(OrderType::class);
        $Order = $this->parameterBag->get('Order');

        // カート削除
        $this->cartService->clear();

        $event = new EventArgs(
            [
                'form' => $form,
                'Order' => $Order,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_CONFIRM_PROCESSING, $event);

        if ($event->getResponse() !== null) {
            log_info('イベントレスポンス返却', [$Order->getId()]);

            return $event->getResponse();
        }

        // 受注IDをセッションにセット
        $this->session->set($this->sessionOrderKey, $Order->getId());

        // メール送信
        $MailHistory = $this->shoppingService->sendOrderMail($Order);

        $event = new EventArgs(
            [
                'form' => $form,
                'Order' => $Order,
                'MailHistory' => $MailHistory,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_CONFIRM_COMPLETE, $event);

        if ($event->getResponse() !== null) {
            log_info('イベントレスポンス返却', [$Order->getId()]);

            return $event->getResponse();
        }

        // 完了画面表示
        return $this->redirectToRoute('shopping_complete');
    }

    private function createPaymentMethod(Order $Order, FormInterface $form)
    {
        $PaymentMethod = $this->container->get($Order->getPayment()->getMethodClass());
        $PaymentMethod->setOrder($Order);
        $PaymentMethod->setFormType($form);

        return $PaymentMethod;
    }
}
