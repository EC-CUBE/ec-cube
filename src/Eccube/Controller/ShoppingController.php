<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller;

use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\ShoppingException;
use Eccube\Form\Type\Front\CustomerLoginType;
use Eccube\Form\Type\Front\ShoppingShippingType;
use Eccube\Form\Type\Shopping\CustomerAddressType;
use Eccube\Form\Type\Shopping\OrderType;
use Eccube\Repository\OrderRepository;
use Eccube\Service\CartService;
use Eccube\Service\MailService;
use Eccube\Service\OrderHelper;
use Eccube\Service\Payment\PaymentDispatcher;
use Eccube\Service\Payment\PaymentMethodInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ShoppingController extends AbstractShoppingController
{
    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var OrderHelper
     */
    protected $orderHelper;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    public function __construct(
        CartService $cartService,
        MailService $mailService,
        OrderRepository $orderRepository,
        OrderHelper $orderHelper
    ) {
        $this->cartService = $cartService;
        $this->mailService = $mailService;
        $this->orderRepository = $orderRepository;
        $this->orderHelper = $orderHelper;
    }

    /**
     * 注文手続き画面を表示する
     *
     * 未ログインまたはRememberMeログインの場合はログイン画面に遷移させる.
     * ただし、非会員でお客様情報を入力済の場合は遷移させない.
     *
     * カート情報から受注データを生成し, `pre_order_id`でカートと受注の紐付けを行う.
     * 既に受注が生成されている場合(pre_order_idで取得できる場合)は, 受注の生成を行わずに画面を表示する.
     *
     * purchaseFlowの集計処理実行後, warningがある場合はカートど同期をとるため, カートのPurchaseFlowを実行する.
     *
     * @Route("/shopping", name="shopping", methods={"GET"})
     * @Template("Shopping/index.twig")
     */
    public function index(PurchaseFlow $cartPurchaseFlow)
    {
        // ログイン状態のチェック.
        if ($this->orderHelper->isLoginRequired()) {
            log_info('[注文手続] 未ログインもしくはRememberMeログインのため, ログイン画面に遷移します.');

            return $this->redirectToRoute('shopping_login');
        }

        // カートチェック.
        $Cart = $this->cartService->getCart();
        if (!($Cart && $this->orderHelper->verifyCart($Cart))) {
            log_info('[注文手続] カートが購入フローへ遷移できない状態のため, カート画面に遷移します.');

            return $this->redirectToRoute('cart');
        }

        // 受注の初期化.
        log_info('[注文手続] 受注の初期化処理を開始します.');
        $Customer = $this->getUser() ? $this->getUser() : $this->orderHelper->getNonMember();
        $Order = $this->orderHelper->initializeOrder($Cart, $Customer);

        // 集計処理.
        log_info('[注文手続] 集計処理を開始します.', [$Order->getId()]);
        $flowResult = $this->executePurchaseFlow($Order, false);
        $this->entityManager->flush();

        if ($flowResult->hasError()) {
            log_info('[注文手続] Errorが発生したため購入エラー画面へ遷移します.', [$flowResult->getErrors()]);

            return $this->redirectToRoute('shopping_error');
        }

        if ($flowResult->hasWarning()) {
            log_info('[注文手続] Warningが発生しました.', [$flowResult->getWarning()]);

            // 受注明細と同期をとるため, CartPurchaseFlowを実行する
            $cartPurchaseFlow->validate($Cart, new PurchaseContext($Cart, $this->getUser()));

            // 注文フローで取得されるカートの入れ替わりを防止する
            // @see https://github.com/EC-CUBE/ec-cube/issues/4293
            $this->cartService->setPrimary($Cart->getCartKey());
        }

        // マイページで会員情報が更新されていれば, Orderの注文者情報も更新する.
        if ($Customer->getId()) {
            $this->orderHelper->updateCustomerInfo($Order, $Customer);
            $this->entityManager->flush();
        }

        $form = $this->createForm(OrderType::class, $Order);

        return [
            'form' => $form->createView(),
            'Order' => $Order,
        ];
    }

    /**
     * 他画面への遷移を行う.
     *
     * お届け先編集画面など, 他画面へ遷移する際に, フォームの値をDBに保存してからリダイレクトさせる.
     * フォームの`redirect_to`パラメータの値にリダイレクトを行う.
     * `redirect_to`パラメータはpath('遷移先のルーティング')が渡される必要がある.
     *
     * 外部のURLやPathを渡された場合($router->matchで展開出来ない場合)は, 購入エラーとする.
     *
     * プラグインやカスタマイズでこの機能を使う場合は, twig側で以下のように記述してください.
     *
     * <button data-trigger="click" data-path="path('ルーティング')">更新する</button>
     *
     * data-triggerは, click/change/blur等のイベント名を指定してください。
     * data-pathは任意のパラメータです. 指定しない場合, 注文手続き画面へリダイレクトします.
     *
     * @Route("/shopping/redirect_to", name="shopping_redirect_to", methods={"POST"})
     * @Template("Shopping/index.twig")
     */
    public function redirectTo(Request $request, RouterInterface $router)
    {
        // ログイン状態のチェック.
        if ($this->orderHelper->isLoginRequired()) {
            log_info('[リダイレクト] 未ログインもしくはRememberMeログインのため, ログイン画面に遷移します.');

            return $this->redirectToRoute('shopping_login');
        }

        // 受注の存在チェック.
        $preOrderId = $this->cartService->getPreOrderId();
        $Order = $this->orderHelper->getPurchaseProcessingOrder($preOrderId);
        if (!$Order) {
            log_info('[リダイレクト] 購入処理中の受注が存在しません.');

            return $this->redirectToRoute('shopping_error');
        }

        $form = $this->createForm(OrderType::class, $Order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('[リダイレクト] 集計処理を開始します.', [$Order->getId()]);
            $response = $this->executePurchaseFlow($Order);
            $this->entityManager->flush();

            if ($response) {
                return $response;
            }

            $redirectTo = $form['redirect_to']->getData();
            if (empty($redirectTo)) {
                log_info('[リダイレクト] リダイレクト先未指定のため注文手続き画面へ遷移します.');

                return $this->redirectToRoute('shopping');
            }

            try {
                // リダイレクト先のチェック.
                $pattern = '/^'.preg_quote($request->getBasePath(), '/').'/';
                $redirectTo = preg_replace($pattern, '', $redirectTo);
                $result = $router->match($redirectTo);
                // パラメータのみ抽出
                $params = array_filter($result, function ($key) {
                    return 0 !== \strpos($key, '_');
                }, ARRAY_FILTER_USE_KEY);

                log_info('[リダイレクト] リダイレクトを実行します.', [$result['_route'], $params]);

                // pathからurlを再構築してリダイレクト.
                return $this->redirectToRoute($result['_route'], $params);
            } catch (\Exception $e) {
                log_info('[リダイレクト] URLの形式が不正です', [$redirectTo, $e->getMessage()]);

                return $this->redirectToRoute('shopping_error');
            }
        }

        log_info('[リダイレクト] フォームエラーのため, 注文手続き画面を表示します.', [$Order->getId()]);

        return [
            'form' => $form->createView(),
            'Order' => $Order,
        ];
    }

    /**
     * 注文確認画面を表示する.
     *
     * ここではPaymentMethod::verifyがコールされます.
     * PaymentMethod::verifyではクレジットカードの有効性チェック等, 注文手続きを進められるかどうかのチェック処理を行う事を想定しています.
     * PaymentMethod::verifyでエラーが発生した場合は, 注文手続き画面へリダイレクトします.
     *
     * @Route("/shopping/confirm", name="shopping_confirm", methods={"POST"})
     * @Template("Shopping/confirm.twig")
     */
    public function confirm(Request $request)
    {
        // ログイン状態のチェック.
        if ($this->orderHelper->isLoginRequired()) {
            log_info('[注文確認] 未ログインもしくはRememberMeログインのため, ログイン画面に遷移します.');

            return $this->redirectToRoute('shopping_login');
        }

        // 受注の存在チェック
        $preOrderId = $this->cartService->getPreOrderId();
        $Order = $this->orderHelper->getPurchaseProcessingOrder($preOrderId);
        if (!$Order) {
            log_info('[注文確認] 購入処理中の受注が存在しません.', [$preOrderId]);

            return $this->redirectToRoute('shopping_error');
        }

        $form = $this->createForm(OrderType::class, $Order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('[注文確認] 集計処理を開始します.', [$Order->getId()]);
            $response = $this->executePurchaseFlow($Order);
            $this->entityManager->flush();

            if ($response) {
                return $response;
            }

            log_info('[注文確認] PaymentMethod::verifyを実行します.', [$Order->getPayment()->getMethodClass()]);
            $paymentMethod = $this->createPaymentMethod($Order, $form);
            $PaymentResult = $paymentMethod->verify();

            if ($PaymentResult) {
                if (!$PaymentResult->isSuccess()) {
                    $this->entityManager->rollback();
                    foreach ($PaymentResult->getErrors() as $error) {
                        $this->addError($error);
                    }

                    log_info('[注文確認] PaymentMethod::verifyのエラーのため, 注文手続き画面へ遷移します.', [$PaymentResult->getErrors()]);

                    return $this->redirectToRoute('shopping');
                }

                $response = $PaymentResult->getResponse();
                if ($response instanceof Response && ($response->isRedirection() || $response->isSuccessful())) {
                    $this->entityManager->flush();

                    log_info('[注文確認] PaymentMethod::verifyが指定したレスポンスを表示します.');

                    return $response;
                }
            }

            $this->entityManager->flush();

            log_info('[注文確認] 注文確認画面を表示します.');

            return [
                'form' => $form->createView(),
                'Order' => $Order,
            ];
        }

        log_info('[注文確認] フォームエラーのため, 注文手続画面を表示します.', [$Order->getId()]);

        $template = new Template([
            'owner' => [$this, 'confirm'],
            'template' => 'Shopping/index.twig'
        ]);
        $request->attributes->set('_template', $template);

        return [
            'form' => $form->createView(),
            'Order' => $Order,
        ];
    }

    /**
     * 注文処理を行う.
     *
     * 決済プラグインによる決済処理および注文の確定処理を行います.
     *
     * @Route("/shopping/checkout", name="shopping_checkout", methods={"POST"})
     * @Template("Shopping/confirm.twig")
     */
    public function checkout(Request $request)
    {
        // ログイン状態のチェック.
        if ($this->orderHelper->isLoginRequired()) {
            log_info('[注文処理] 未ログインもしくはRememberMeログインのため, ログイン画面に遷移します.');

            return $this->redirectToRoute('shopping_login');
        }

        // 受注の存在チェック
        $preOrderId = $this->cartService->getPreOrderId();
        $Order = $this->orderHelper->getPurchaseProcessingOrder($preOrderId);
        if (!$Order) {
            log_info('[注文処理] 購入処理中の受注が存在しません.', [$preOrderId]);

            return $this->redirectToRoute('shopping_error');
        }

        // フォームの生成.
        $form = $this->createForm(OrderType::class, $Order, [
            // 確認画面から注文処理へ遷移する場合は, Orderエンティティで値を引き回すためフォーム項目の定義をスキップする.
            'skip_add_form' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('[注文処理] 注文処理を開始します.', [$Order->getId()]);

            try {
                /*
                 * 集計処理
                 */
                log_info('[注文処理] 集計処理を開始します.', [$Order->getId()]);
                $response = $this->executePurchaseFlow($Order);
                $this->entityManager->flush();

                if ($response) {
                    return $response;
                }

                log_info('[注文処理] PaymentMethodを取得します.', [$Order->getPayment()->getMethodClass()]);
                $paymentMethod = $this->createPaymentMethod($Order, $form);

                /*
                 * 決済実行(前処理)
                 */
                log_info('[注文処理] PaymentMethod::applyを実行します.');
                if ($response = $this->executeApply($paymentMethod)) {
                    return $response;
                }

                /*
                 * 決済実行
                 *
                 * PaymentMethod::checkoutでは決済処理が行われ, 正常に処理出来た場合はPurchaseFlow::commitがコールされます.
                 */
                log_info('[注文処理] PaymentMethod::checkoutを実行します.');
                if ($response = $this->executeCheckout($paymentMethod)) {
                    return $response;
                }

                $this->entityManager->flush();

                log_info('[注文処理] 注文処理が完了しました.', [$Order->getId()]);
            } catch (ShoppingException $e) {
                log_error('[注文処理] 購入エラーが発生しました.', [$e->getMessage()]);

                $this->entityManager->rollback();

                $this->addError($e->getMessage());

                return $this->redirectToRoute('shopping_error');
            } catch (\Exception $e) {
                log_error('[注文処理] 予期しないエラーが発生しました.', [$e->getMessage()]);

                $this->entityManager->rollback();

                $this->addError('front.shopping.system_error');

                return $this->redirectToRoute('shopping_error');
            }

            // カート削除
            log_info('[注文処理] カートをクリアします.', [$Order->getId()]);
            $this->cartService->clear();

            // 受注IDをセッションにセット
            $this->session->set(OrderHelper::SESSION_ORDER_ID, $Order->getId());

            // メール送信
            log_info('[注文処理] 注文メールの送信を行います.', [$Order->getId()]);
            $this->mailService->sendOrderMail($Order);
            $this->entityManager->flush();

            log_info('[注文処理] 注文処理が完了しました. 購入完了画面へ遷移します.', [$Order->getId()]);

            return $this->redirectToRoute('shopping_complete');
        }

        log_info('[注文処理] フォームエラーのため, 購入エラー画面へ遷移します.', [$Order->getId()]);

        return $this->redirectToRoute('shopping_error');
    }

    /**
     * 購入完了画面を表示する.
     *
     * @Route("/shopping/complete", name="shopping_complete", methods={"GET"})
     * @Template("Shopping/complete.twig")
     */
    public function complete(Request $request)
    {
        log_info('[注文完了] 注文完了画面を表示します.');

        // 受注IDを取得
        $orderId = $this->session->get(OrderHelper::SESSION_ORDER_ID);

        if (empty($orderId)) {
            log_info('[注文完了] 受注IDを取得できないため, トップページへ遷移します.');

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

        log_info('[注文完了] 購入フローのセッションをクリアします. ');
        $this->orderHelper->removeSession();

        $hasNextCart = !empty($this->cartService->getCarts());

        log_info('[注文完了] 注文完了画面を表示しました. ', [$hasNextCart]);

        return [
            'Order' => $Order,
            'hasNextCart' => $hasNextCart,
        ];
    }

    /**
     * お届け先選択画面.
     *
     * 会員ログイン時, お届け先を選択する画面を表示する
     * 非会員の場合はこの画面は使用しない。
     *
     * @Route("/shopping/shipping/{id}", name="shopping_shipping", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     * @Template("Shopping/shipping.twig")
     */
    public function shipping(Request $request, Shipping $Shipping)
    {
        // ログイン状態のチェック.
        if ($this->orderHelper->isLoginRequired()) {
            return $this->redirectToRoute('shopping_login');
        }

        // 受注の存在チェック
        $preOrderId = $this->cartService->getPreOrderId();
        $Order = $this->orderHelper->getPurchaseProcessingOrder($preOrderId);
        if (!$Order) {
            return $this->redirectToRoute('shopping_error');
        }

        // 受注に紐づくShippingかどうかのチェック.
        if (!$Order->findShipping($Shipping->getId())) {
            return $this->redirectToRoute('shopping_error');
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
            $response = $this->executePurchaseFlow($Order);
            $this->entityManager->flush();

            if ($response) {
                return $response;
            }

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
     * お届け先の新規作成または編集画面.
     *
     * 会員時は新しいお届け先を作成し, 作成したお届け先を選択状態にして注文手続き画面へ遷移する.
     * 非会員時は選択されたお届け先の編集を行う.
     *
     * @Route("/shopping/shipping_edit/{id}", name="shopping_shipping_edit", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     * @Template("Shopping/shipping_edit.twig")
     */
    public function shippingEdit(Request $request, Shipping $Shipping)
    {
        // ログイン状態のチェック.
        if ($this->orderHelper->isLoginRequired()) {
            return $this->redirectToRoute('shopping_login');
        }

        // 受注の存在チェック
        $preOrderId = $this->cartService->getPreOrderId();
        $Order = $this->orderHelper->getPurchaseProcessingOrder($preOrderId);
        if (!$Order) {
            return $this->redirectToRoute('shopping_error');
        }

        // 受注に紐づくShippingかどうかのチェック.
        if (!$Order->findShipping($Shipping->getId())) {
            return $this->redirectToRoute('shopping_error');
        }

        $CustomerAddress = new CustomerAddress();
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            // ログイン時は会員と紐付け
            $CustomerAddress->setCustomer($this->getUser());
        } else {
            // 非会員時はお届け先をセット
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
            log_info('お届け先追加処理開始', ['order_id' => $Order->getId(), 'shipping_id' => $Shipping->getId()]);

            $Shipping->setFromCustomerAddress($CustomerAddress);

            if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
                $this->entityManager->persist($CustomerAddress);
            }

            // 合計金額の再計算
            $response = $this->executePurchaseFlow($Order);
            $this->entityManager->flush();

            if ($response) {
                return $response;
            }

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Shipping' => $Shipping,
                    'CustomerAddress' => $CustomerAddress,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_COMPLETE, $event);

            log_info('お届け先追加処理完了', ['order_id' => $Order->getId(), 'shipping_id' => $Shipping->getId()]);

            return $this->redirectToRoute('shopping');
        }

        return [
            'form' => $form->createView(),
            'shippingId' => $Shipping->getId(),
        ];
    }

    /**
     * ログイン画面.
     *
     * @Route("/shopping/login", name="shopping_login", methods={"GET"})
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
     * 購入エラー画面.
     *
     * @Route("/shopping/error", name="shopping_error", methods={"GET"})
     * @Template("Shopping/shopping_error.twig")
     */
    public function error(Request $request, PurchaseFlow $cartPurchaseFlow)
    {
        // 受注とカートのずれを合わせるため, カートのPurchaseFlowをコールする.
        $Cart = $this->cartService->getCart();
        if (null !== $Cart) {
            $cartPurchaseFlow->validate($Cart, new PurchaseContext($Cart, $this->getUser()));
            $this->cartService->setPreOrderId(null);
            $this->cartService->save();
        }

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
     * PaymentMethodをコンテナから取得する.
     *
     * @param Order $Order
     * @param FormInterface $form
     *
     * @return PaymentMethodInterface
     */
    private function createPaymentMethod(Order $Order, FormInterface $form)
    {
        $PaymentMethod = $this->container->get($Order->getPayment()->getMethodClass());
        $PaymentMethod->setOrder($Order);
        $PaymentMethod->setFormType($form);

        return $PaymentMethod;
    }

    /**
     * PaymentMethod::applyを実行する.
     *
     * @param PaymentMethodInterface $paymentMethod
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function executeApply(PaymentMethodInterface $paymentMethod)
    {
        $dispatcher = $paymentMethod->apply(); // 決済処理中.

        // リンク式決済のように他のサイトへ遷移する場合などは, dispatcherに処理を移譲する.
        if ($dispatcher instanceof PaymentDispatcher) {
            $response = $dispatcher->getResponse();
            $this->entityManager->flush();

            // dispatcherがresponseを保持している場合はresponseを返す
            if ($response instanceof Response && ($response->isRedirection() || $response->isSuccessful())) {
                log_info('[注文処理] PaymentMethod::applyが指定したレスポンスを表示します.');

                return $response;
            }

            // forwardすることも可能.
            if ($dispatcher->isForward()) {
                log_info('[注文処理] PaymentMethod::applyによりForwardします.',
                    [$dispatcher->getRoute(), $dispatcher->getPathParameters(), $dispatcher->getQueryParameters()]);

                return $this->forwardToRoute($dispatcher->getRoute(), $dispatcher->getPathParameters(),
                    $dispatcher->getQueryParameters());
            } else {
                log_info('[注文処理] PaymentMethod::applyによりリダイレクトします.',
                    [$dispatcher->getRoute(), $dispatcher->getPathParameters(), $dispatcher->getQueryParameters()]);

                return $this->redirectToRoute($dispatcher->getRoute(),
                    array_merge($dispatcher->getPathParameters(), $dispatcher->getQueryParameters()));
            }
        }
    }

    /**
     * PaymentMethod::checkoutを実行する.
     *
     * @param PaymentMethodInterface $paymentMethod
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|null
     */
    protected function executeCheckout(PaymentMethodInterface $paymentMethod)
    {
        $PaymentResult = $paymentMethod->checkout();
        $response = $PaymentResult->getResponse();
        // PaymentResultがresponseを保持している場合はresponseを返す
        if ($response instanceof Response && ($response->isRedirection() || $response->isSuccessful())) {
            $this->entityManager->flush();
            log_info('[注文処理] PaymentMethod::checkoutが指定したレスポンスを表示します.');

            return $response;
        }

        // エラー時はロールバックして購入エラーとする.
        if (!$PaymentResult->isSuccess()) {
            $this->entityManager->rollback();
            foreach ($PaymentResult->getErrors() as $error) {
                $this->addError($error);
            }

            log_info('[注文処理] PaymentMethod::checkoutのエラーのため, 購入エラー画面へ遷移します.', [$PaymentResult->getErrors()]);

            return $this->redirectToRoute('shopping_error');
        }

        return null;
    }
}
