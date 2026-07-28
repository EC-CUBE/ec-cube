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

use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Order;
use Eccube\Entity\Payment;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\ShoppingException;
use Eccube\Form\Type\Front\CustomerLoginType;
use Eccube\Form\Type\Front\ShoppingShippingType;
use Eccube\Form\Type\Shopping\CustomerAddressType;
use Eccube\Form\Type\Shopping\OrderType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerAddressRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Repository\TradeLawRepository;
use Eccube\Service\CartService;
use Eccube\Service\MailService;
use Eccube\Service\OrderHelper;
use Eccube\Service\Payment\PaymentDispatcher;
use Eccube\Service\Payment\PaymentMethodInterface;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ShoppingController extends AbstractShoppingController
{
    public function __construct(protected CartService $cartService, protected MailService $mailService, protected OrderRepository $orderRepository, protected OrderHelper $orderHelper, protected ContainerInterface $serviceContainer, protected TradeLawRepository $tradeLawRepository, protected RateLimiterFactoryInterface $shoppingConfirmIpLimiter, protected RateLimiterFactoryInterface $shoppingConfirmCustomerLimiter, protected RateLimiterFactoryInterface $shoppingCheckoutIpLimiter, protected RateLimiterFactoryInterface $shoppingCheckoutCustomerLimiter, protected BaseInfoRepository $baseInfoRepository, protected PrefRepository $prefRepository, protected DeliveryRepository $deliveryRepository, protected PaymentRepository $paymentRepository, private readonly PurchaseFlow $cartPurchaseFlow, private readonly AuthenticationUtils $authenticationUtils, protected CustomerAddressRepository $customerAddressRepository)
    {
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
     * @return RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/shopping', name: 'shopping', methods: ['GET'])]
    #[Template(template: 'Shopping/index.twig')]
    public function index(): RedirectResponse|array
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
        /** @var Customer $Customer */
        $Customer = $this->getUser() ?: $this->orderHelper->getNonMember();
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
            $this->cartPurchaseFlow->validate($Cart, new PurchaseContext($Cart, $this->getUser()));

            // 注文フローで取得されるカートの入れ替わりを防止する
            // @see https://github.com/EC-CUBE/ec-cube/issues/4293
            $this->cartService->setPrimary($Cart->getCartKey());
        }
        // マイページで会員情報が更新されていれば, Orderの注文者情報も更新する.
        if ($Customer->getId()) {
            $this->orderHelper->updateCustomerInfo($Order, $Customer);
            $this->entityManager->flush();
        }
        $activeTradeLaws = $this->tradeLawRepository->findBy(['displayOrderScreen' => true], ['sortNo' => 'ASC']);
        $form = $this->createForm(OrderType::class, $Order);

        // 初期選択された支払方法が利用条件に合致せず選択肢に含まれない場合は,
        // 選択可能な支払方法の先頭に再設定し, 手数料を再集計する.
        // @see https://github.com/EC-CUBE/ec-cube/issues/6200
        if ($this->reselectUnavailablePayment($Order, $form)) {
            $flowResult = $this->executePurchaseFlow($Order, false);
            $this->entityManager->flush();
            if ($flowResult->hasError()) {
                log_info('[注文手続] Errorが発生したため購入エラー画面へ遷移します.', [$flowResult->getErrors()]);

                return $this->redirectToRoute('shopping_error');
            }
            if ($flowResult->hasWarning()) {
                log_info('[注文手続] Warningが発生しました.', [$flowResult->getWarning()]);

                // 受注明細と同期をとるため, CartPurchaseFlowを実行する
                $this->cartPurchaseFlow->validate($Cart, new PurchaseContext($Cart, $this->getUser()));

                // 注文フローで取得されるカートの入れ替わりを防止する
                // @see https://github.com/EC-CUBE/ec-cube/issues/4293
                $this->cartService->setPrimary($Cart->getCartKey());
            }
            $form = $this->createForm(OrderType::class, $Order);
        }

        // 保存された配送方法・支払い方法の検証(会員IDがない場合は何も表示しない).
        $preferredInfo = $this->validatePreferredShippingPayment($Customer, $Order, '[注文手続][保存情報検証]');

        return [
            'form' => $form->createView(),
            'Order' => $Order,
            'activeTradeLaws' => $activeTradeLaws,
            'Prefs' => $this->prefRepository->findAll(),
            'preferredPaymentId' => $preferredInfo['preferredPaymentId'],
            'preferredPaymentName' => $preferredInfo['preferredPaymentName'],
            'preferredDeliveryId' => $preferredInfo['preferredDeliveryId'],
            'preferredDeliveryName' => $preferredInfo['preferredDeliveryName'],
            'isMultipleShipping' => $preferredInfo['isMultipleShipping'],
            'preferredUnavailableReason' => $preferredInfo['preferredUnavailableReason'],
        ];
    }

    /**
     * 受注に設定された支払方法が, フォームで選択可能な支払方法に含まれているかを判定する.
     *
     * 利用条件(利用可能金額の範囲)に合致しない支払方法が初期選択されている場合,
     * 選択可能な支払方法の先頭に再設定する. 再設定を行った場合は true を返す.
     */
    private function reselectUnavailablePayment(Order $Order, FormInterface $form): bool
    {
        if (!$form->has('Payment')) {
            return false;
        }

        /** @var Payment[] $Payments */
        $Payments = $form->get('Payment')->getConfig()->getOption('choices');
        $Payment = $Order->getPayment();

        // 選択中の支払方法が選択肢に含まれていれば補正不要.
        $selectableIds = array_map(static fn (Payment $p) => $p->getId(), $Payments);
        if ($Payment && in_array($Payment->getId(), $selectableIds, true)) {
            return false;
        }

        $NewPayment = $Payments ? reset($Payments) : null;
        // 既に同じ状態(共にnull)であれば補正不要.
        if ($Payment === $NewPayment) {
            return false;
        }

        $Order->setPayment($NewPayment ?: null);
        $Order->setPaymentMethod($NewPayment ? $NewPayment->getMethod() : null);

        return true;
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
     * @return RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/shopping/redirect_to', name: 'shopping_redirect_to', methods: ['POST'])]
    #[Template(template: 'Shopping/index.twig')]
    public function redirectTo(Request $request): RedirectResponse|array
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
                $redirectTo = preg_replace($pattern, '', (string) $redirectTo);
                $result = $this->router->match($redirectTo);
                // パラメータのみ抽出
                $params = array_filter($result, fn ($key) => !str_starts_with((string) $key, '_'), ARRAY_FILTER_USE_KEY);

                log_info('[リダイレクト] リダイレクトを実行します.', [$result['_route'], $params]);

                // pathからurlを再構築してリダイレクト.
                return $this->redirectToRoute($result['_route'], $params);
            } catch (\Exception $e) {
                log_info('[リダイレクト] URLの形式が不正です', [$redirectTo, $e->getMessage()]);

                return $this->redirectToRoute('shopping_error');
            }
        }

        $activeTradeLaws = $this->tradeLawRepository->findBy(['displayOrderScreen' => true], ['sortNo' => 'ASC']);

        log_info('[リダイレクト] フォームエラーのため, 注文手続き画面を表示します.', [$Order->getId()]);

        // 保存された配送方法・支払い方法の検証(非会員の場合は検証しない).
        $Customer = $this->getUser();
        if ($Customer instanceof Customer) {
            $preferredInfo = $this->validatePreferredShippingPayment($Customer, $Order, '[リダイレクト][保存情報検証]');
        } else {
            $preferredInfo = [
                'preferredPaymentId' => null,
                'preferredPaymentName' => null,
                'preferredDeliveryId' => null,
                'preferredDeliveryName' => null,
                'isMultipleShipping' => $Order->getShippings()->count() > 1,
                'preferredUnavailableReason' => null,
            ];
        }

        return [
            'form' => $form->createView(),
            'Order' => $Order,
            'activeTradeLaws' => $activeTradeLaws,
            'Prefs' => $this->prefRepository->findAll(),
            'preferredPaymentId' => $preferredInfo['preferredPaymentId'],
            'preferredPaymentName' => $preferredInfo['preferredPaymentName'],
            'preferredDeliveryId' => $preferredInfo['preferredDeliveryId'],
            'preferredDeliveryName' => $preferredInfo['preferredDeliveryName'],
            'isMultipleShipping' => $preferredInfo['isMultipleShipping'],
            'preferredUnavailableReason' => $preferredInfo['preferredUnavailableReason'],
        ];
    }

    /**
     * 保存された配送方法・支払い方法を受注へ適用する.
     *
     * 会員が保存した優先配送方法・優先支払方法を購入処理中の受注へ適用し, 合計金額を再計算する.
     * 複数配送先の場合や保存情報が利用できない場合は適用せず, 警告メッセージを表示して注文手続き画面へ戻す.
     */
    #[Route(path: '/shopping/restore_preferred', name: 'shopping_restore_preferred', methods: ['POST'])]
    public function restorePreferred(): RedirectResponse
    {
        // ログイン状態のチェック.
        if ($this->orderHelper->isLoginRequired()) {
            log_info('[保存設定復元] 未ログインもしくはRememberMeログインのため, ログイン画面に遷移します.');

            return $this->redirectToRoute('shopping_login');
        }

        // 復元は会員のみ利用可能.
        $Customer = $this->getUser();
        if (!$Customer instanceof Customer) {
            log_info('[保存設定復元] 非会員のため復元できません.');

            throw new AccessDeniedHttpException();
        }

        $this->isTokenValid();

        // 受注の存在チェック.
        $preOrderId = $this->cartService->getPreOrderId();
        $Order = $this->orderHelper->getPurchaseProcessingOrder($preOrderId);
        if (!$Order) {
            log_info('[保存設定復元] 購入処理中の受注が存在しません.', [$preOrderId]);

            return $this->redirectToRoute('shopping_error');
        }

        // 複数配送先の場合は復元しない.
        if ($Order->getShippings()->count() > 1) {
            log_info('[保存設定復元] 複数配送先のため復元をスキップします.', [$Order->getId()]);
            $this->addWarning('front.shopping.preferred_multiple_shipping_notice');

            return $this->redirectToRoute('shopping');
        }

        // 保存情報の検証.
        $preferredInfo = $this->validatePreferredShippingPayment($Customer, $Order, '[保存設定復元][保存情報検証]');
        if ($preferredInfo['preferredUnavailableReason'] !== null) {
            log_info('[保存設定復元] 保存情報が利用できないため復元をスキップします.', [$preferredInfo['preferredUnavailableReason']]);
            $this->addWarning($preferredInfo['preferredUnavailableReason']);

            return $this->redirectToRoute('shopping');
        }
        if ($preferredInfo['preferredPaymentId'] === null || $preferredInfo['preferredDeliveryId'] === null) {
            log_info('[保存設定復元] 保存情報が存在しないため復元をスキップします.');

            return $this->redirectToRoute('shopping');
        }

        // 保存値を受注へ適用する.
        $Payment = $Customer->getPreferredPayment();
        $Delivery = $Customer->getPreferredDelivery();
        $Order->setPayment($Payment);
        $Order->setPaymentMethod($Payment->getMethod());
        foreach ($Order->getShippings() as $Shipping) {
            $Shipping->setDelivery($Delivery);
            $Shipping->setShippingDeliveryName($Delivery->getName());
            // 配送方法を差し替えると, 設定済みのお届け時間は新しい配送業者に属さない可能性があるためクリアする.
            $Shipping->setShippingDeliveryTime();
            $Shipping->setTimeId(null);
        }

        // 合計金額の再計算.
        log_info('[保存設定復元] 集計処理を開始します.', [$Order->getId()]);
        $response = $this->executePurchaseFlow($Order);
        $this->entityManager->flush();

        if ($response) {
            return $response;
        }

        log_info('[保存設定復元] 保存された設定を適用しました.', [$Order->getId()]);
        $this->addSuccess('front.shopping.preferred_restored_success');

        return $this->redirectToRoute('shopping');
    }

    /**
     * 注文確認画面を表示する.
     *
     * ここではPaymentMethod::verifyがコールされます.
     * PaymentMethod::verifyではクレジットカードの有効性チェック等, 注文手続きを進められるかどうかのチェック処理を行う事を想定しています.
     * PaymentMethod::verifyでエラーが発生した場合は, 注文手続き画面へリダイレクトします.
     *
     * @return RedirectResponse|Response|array<string, mixed>
     *
     * @throws TooManyRequestsHttpException
     */
    #[Route(path: '/shopping/confirm', name: 'shopping_confirm', methods: ['POST'])]
    #[Template(template: 'Shopping/confirm.twig')]
    public function confirm(Request $request): RedirectResponse|Response|array
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

        $activeTradeLaws = $this->tradeLawRepository->findBy(['displayOrderScreen' => true], ['sortNo' => 'ASC']);
        $form = $this->createForm(OrderType::class, $Order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('[注文確認] 集計処理を開始します.', [$Order->getId()]);
            $response = $this->executePurchaseFlow($Order);
            $this->entityManager->flush();

            if ($response) {
                return $response;
            }

            log_info('[注文確認] IPベースのスロットリングを実行します.');
            $ipLimiter = $this->shoppingConfirmIpLimiter->create($request->getClientIp());
            if (!$ipLimiter->consume()->isAccepted()) {
                log_info('[注文確認] 試行回数制限を超過しました(IPベース)');
                throw new TooManyRequestsHttpException();
            }

            $Customer = $this->getUser();
            if ($Customer instanceof Customer) {
                log_info('[注文確認] 会員ベースのスロットリングを実行します.');
                $customerLimiter = $this->shoppingConfirmCustomerLimiter->create((string) $Customer->getId());
                if (!$customerLimiter->consume()->isAccepted()) {
                    log_info('[注文確認] 試行回数制限を超過しました(会員ベース)');
                    throw new TooManyRequestsHttpException();
                }
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
                if ($response->isRedirection() || $response->isSuccessful()) {
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
                'activeTradeLaws' => $activeTradeLaws,
                'isMultipleShipping' => $Order->getShippings()->count() > 1,
            ];
        }

        log_info('[注文確認] フォームエラーのため, 注文手続画面を表示します.', [$Order->getId()]);

        // $template = new Template([
        //     'owner' => $this->confirm(...),
        //     'template' => 'Shopping/index.twig',
        // ]);
        // TODO これであっているか要確認
        $request->attributes->set('_template', new Template('Shopping/index.twig'));

        return [
            'form' => $form->createView(),
            'Order' => $Order,
            'activeTradeLaws' => $activeTradeLaws,
            'Prefs' => $this->prefRepository->findAll(),
        ];
    }

    /**
     * 注文処理を行う.
     *
     * 決済プラグインによる決済処理および注文の確定処理を行います.
     *
     * @return RedirectResponse|array<string, mixed>|Response
     *
     * @throws TooManyRequestsHttpException
     */
    #[Route(path: '/shopping/checkout', name: 'shopping_checkout', methods: ['POST'])]
    #[Template(template: 'Shopping/confirm.twig')]
    public function checkout(Request $request): RedirectResponse|array|Response
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

                log_info('[注文完了] IPベースのスロットリングを実行します.');
                $ipLimiter = $this->shoppingCheckoutIpLimiter->create($request->getClientIp());
                if (!$ipLimiter->consume()->isAccepted()) {
                    log_info('[注文完了] 試行回数制限を超過しました(IPベース)');
                    throw new TooManyRequestsHttpException();
                }

                $Customer = $this->getUser();
                if ($Customer instanceof Customer) {
                    log_info('[注文完了] 会員ベースのスロットリングを実行します.');
                    $customerLimiter = $this->shoppingCheckoutCustomerLimiter->create((string) $Customer->getId());
                    if (!$customerLimiter->consume()->isAccepted()) {
                        log_info('[注文完了] 試行回数制限を超過しました(会員ベース)');
                        throw new TooManyRequestsHttpException();
                    }
                }

                log_info('[注文処理] PaymentMethodを取得します.', [$Order->getPayment()->getMethodClass()]);
                $paymentMethod = $this->createPaymentMethod($Order, $form);

                // Symfony 7対応: トランザクションを明示的に開始
                // PurchaseFlow::prepare()およびcommit()内でentityManager->lock()を使用するため、トランザクションが必要
                if (!$this->entityManager->getConnection()->isTransactionActive()) {
                    $this->entityManager->beginTransaction();
                }

                /*
                 * 決済実行(前処理)
                 */
                log_info('[注文処理] PaymentMethod::applyを実行します.');
                if ($response = $this->executeApply($paymentMethod)) {
                    // 成功時はトランザクションをコミット
                    if ($this->entityManager->getConnection()->isTransactionActive()) {
                        $this->entityManager->commit();
                    }

                    return $response;
                }

                /*
                 * 決済実行
                 *
                 * PaymentMethod::checkoutでは決済処理が行われ, 正常に処理出来た場合はPurchaseFlow::commitがコールされます.
                 */
                log_info('[注文処理] PaymentMethod::checkoutを実行します.');

                if ($response = $this->executeCheckout($paymentMethod)) {
                    // 成功時はトランザクションをコミット
                    if ($this->entityManager->getConnection()->isTransactionActive()) {
                        $this->entityManager->commit();
                    }

                    return $response;
                }

                $this->entityManager->flush();

                // トランザクションをコミット
                if ($this->entityManager->getConnection()->isTransactionActive()) {
                    $this->entityManager->commit();
                }

                log_info('[注文処理] 注文処理が完了しました.', [$Order->getId()]);
            } catch (ShoppingException $e) {
                log_error('[注文処理] 購入エラーが発生しました.', [$e->getMessage()]);

                // トランザクションをロールバック
                if ($this->entityManager->getConnection()->isTransactionActive()) {
                    $this->entityManager->rollback();
                }

                $this->addError($e->getMessage());

                return $this->redirectToRoute('shopping_error');
            } catch (\Exception $e) {
                log_error('[注文処理] 予期しないエラーが発生しました.', [$e->getMessage()]);

                // トランザクションをロールバック
                if ($this->entityManager->getConnection()->isTransactionActive()) {
                    $this->entityManager->rollback();
                }

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

            // 配送方法・支払い方法の保存処理(会員かつ単一配送先のみ. 失敗しても注文は継続する).
            $this->savePreferredShippingPayment($Order, $form);

            log_info('[注文処理] 注文処理が完了しました. 購入完了画面へ遷移します.', [$Order->getId()]);

            return $this->redirectToRoute('shopping_complete');
        }

        log_info('[注文処理] フォームエラーのため, 購入エラー画面へ遷移します.', [$Order->getId()]);

        return $this->redirectToRoute('shopping_error');
    }

    /**
     * 購入完了画面を表示する.
     *
     * @return RedirectResponse|Response|array<string, mixed>
     */
    #[Route(path: '/shopping/complete', name: 'shopping_complete', methods: ['GET'])]
    #[Template(template: 'Shopping/complete.twig')]
    public function complete(Request $request): RedirectResponse|Response|array
    {
        log_info('[注文完了] 注文完了画面を表示します.');

        // 受注IDを取得
        $orderId = $this->session->get(OrderHelper::SESSION_ORDER_ID);

        if (empty($orderId)) {
            log_info('[注文完了] 受注IDを取得できないため, トップページへ遷移します.');

            return $this->redirectToRoute('homepage');
        }

        $Order = $this->orderRepository->find($orderId);

        if (!$Order) {
            log_info('[注文完了] 受注が存在しないため, トップページへ遷移します.', [$orderId]);

            return $this->redirectToRoute('homepage');
        }

        // 受注の所有者チェック
        $Customer = $this->getUser();
        if ($Customer instanceof Customer) {
            if ($Order->getCustomer() && $Order->getCustomer()->getId() !== $Customer->getId()) {
                log_info('[注文完了] 受注の所有者が一致しないため, トップページへ遷移します.', [$orderId]);

                return $this->redirectToRoute('homepage');
            }
        }

        $event = new EventArgs(
            [
                'Order' => $Order,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_SHOPPING_COMPLETE_INITIALIZE);

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
     * @return RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/shopping/shipping/{id}', name: 'shopping_shipping', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: 'Shopping/shipping.twig')]
    public function shipping(Request $request, Shipping $Shipping): RedirectResponse|array
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
            $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_SHOPPING_SHIPPING_COMPLETE);

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
     * @return RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/shopping/shipping_edit/{id}', name: 'shopping_shipping_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: 'Shopping/shipping_edit.twig')]
    public function shippingEdit(Request $request, Shipping $Shipping): RedirectResponse|array
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
            /** @var Customer $Customer */
            $Customer = $this->getUser();
            $addressCurrNum = count($Customer->getCustomerAddresses());
            $addressMax = $this->eccubeConfig['eccube_deliv_addr_max'];
            if ($addressCurrNum >= $addressMax) {
                throw new NotFoundHttpException();
            }

            // ログイン時は会員と紐付け
            /** @var Customer $Customer */
            $Customer = $this->getUser();
            $CustomerAddress->setCustomer($Customer);
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
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_INITIALIZE);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('お届け先追加処理開始', ['order_id' => $Order->getId(), 'shipping_id' => $Shipping->getId()]);

            $Shipping->setFromCustomerAddress($CustomerAddress);

            if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
                $this->entityManager->persist($CustomerAddress);

                // 会員情報変更時にメールを送信
                if ($this->baseInfoRepository->get()->isOptionMailNotifier()) {
                    /** @var Customer $Customer */
                    $Customer = $this->getUser();

                    // 情報のセット
                    $userData['userAgent'] = $request->headers->get('User-Agent');
                    $userData['ipAddress'] = $request->getClientIp();

                    $this->mailService->sendCustomerChangeNotifyMail($Customer, $userData, trans('front.mypage.delivery.notify_title'));
                }
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
            $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_COMPLETE);

            log_info('お届け先追加処理完了', ['order_id' => $Order->getId(), 'shipping_id' => $Shipping->getId()]);

            return $this->redirectToRoute('shopping');
        }

        return [
            'form' => $form->createView(),
            'shippingId' => $Shipping->getId(),
            'customerAddressId' => null,
        ];
    }

    /**
     * お届け先(会員のお届け先住所)の編集画面.
     *
     * 注文手続き中に, 登録済みのお届け先を編集する. 編集後はお届け先選択画面へ戻る.
     * マイページのお届け先編集と同等の機能を注文手続き画面に提供する.
     *
     * 編集した住所が現在のお届け先に適用中の場合は, お届け先にも反映して合計金額を再計算する.
     *
     * @return RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/shopping/shipping_edit/{id}/{ca_id}', name: 'shopping_shipping_customer_address_edit', requirements: ['id' => '\d+', 'ca_id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: 'Shopping/shipping_edit.twig')]
    public function shippingEditCustomerAddress(Request $request, Shipping $Shipping, int $ca_id): RedirectResponse|array
    {
        // ログイン状態のチェック.
        if ($this->orderHelper->isLoginRequired()) {
            return $this->redirectToRoute('shopping_login');
        }

        // 会員のお届け先住所の編集のため, 会員ログインを必須とする.
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new NotFoundHttpException();
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

        /** @var Customer $Customer */
        $Customer = $this->getUser();

        // 本人のお届け先住所のみ編集可能とする.
        $CustomerAddress = $this->customerAddressRepository->findOneBy([
            'id' => $ca_id,
            'Customer' => $Customer,
        ]);
        if (!$CustomerAddress) {
            throw new NotFoundHttpException();
        }

        // フォームは $CustomerAddress を直接バインドするため, handleRequest 後は編集前の値が失われる.
        // お届け先に適用中の住所かどうかは, ここで判定して退避しておく.
        $isAppliedToShipping = $Shipping->getShippingMultipleDefaultName() === $CustomerAddress->getShippingMultipleDefaultName();

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
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_SHOPPING_SHIPPING_CUSTOMER_ADDRESS_EDIT_INITIALIZE);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('お届け先編集処理開始', ['order_id' => $Order->getId(), 'shipping_id' => $Shipping->getId(), 'customer_address_id' => $ca_id]);

            $this->entityManager->persist($CustomerAddress);

            if ($isAppliedToShipping) {
                // 編集した住所が現在のお届け先に適用中の場合は, お届け先にも反映する.
                $Shipping->setFromCustomerAddress($CustomerAddress);
            }

            // 会員情報変更時にメールを送信
            if ($this->baseInfoRepository->get()->isOptionMailNotifier()) {
                // 情報のセット
                $userData['userAgent'] = $request->headers->get('User-Agent');
                $userData['ipAddress'] = $request->getClientIp();

                $this->mailService->sendCustomerChangeNotifyMail($Customer, $userData, trans('front.mypage.delivery.notify_title'));
            }

            $response = null;
            if ($isAppliedToShipping) {
                // 送料は都道府県に依存するため, 合計金額を再計算する.
                $response = $this->executePurchaseFlow($Order);
            }

            $this->entityManager->flush();

            if ($response) {
                return $response;
            }

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Order' => $Order,
                    'Shipping' => $Shipping,
                    'CustomerAddress' => $CustomerAddress,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_SHOPPING_SHIPPING_CUSTOMER_ADDRESS_EDIT_COMPLETE);

            log_info('お届け先編集処理完了', ['order_id' => $Order->getId(), 'shipping_id' => $Shipping->getId(), 'customer_address_id' => $ca_id]);

            return $this->redirectToRoute('shopping_shipping', ['id' => $Shipping->getId()]);
        }

        return [
            'form' => $form->createView(),
            'shippingId' => $Shipping->getId(),
            'customerAddressId' => $ca_id,
        ];
    }

    /**
     * お届け先(会員のお届け先住所)を削除する.
     *
     * 注文手続き中に, 登録済みのお届け先を削除する. 削除後はお届け先選択画面へ戻る.
     *
     * @throws \Exception
     */
    #[Route(path: '/shopping/shipping_delete/{id}/{ca_id}', name: 'shopping_shipping_customer_address_delete', requirements: ['id' => '\d+', 'ca_id' => '\d+'], methods: ['DELETE'])]
    public function shippingDeleteCustomerAddress(Request $request, Shipping $Shipping, int $ca_id): RedirectResponse
    {
        // ログイン状態のチェック.
        if ($this->orderHelper->isLoginRequired()) {
            return $this->redirectToRoute('shopping_login');
        }

        $this->isTokenValid();

        // 会員のお届け先住所の削除のため, 会員ログインを必須とする.
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new NotFoundHttpException();
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

        /** @var Customer $Customer */
        $Customer = $this->getUser();

        // 本人のお届け先住所のみ削除可能とする.
        $CustomerAddress = $this->customerAddressRepository->findOneBy([
            'id' => $ca_id,
            'Customer' => $Customer,
        ]);
        if (!$CustomerAddress) {
            throw new NotFoundHttpException();
        }

        log_info('お届け先削除処理開始', ['order_id' => $Order->getId(), 'shipping_id' => $Shipping->getId(), 'customer_address_id' => $ca_id]);

        $this->customerAddressRepository->delete($CustomerAddress);

        $event = new EventArgs(
            [
                'Order' => $Order,
                'Shipping' => $Shipping,
                'Customer' => $Customer,
                'CustomerAddress' => $CustomerAddress,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_SHOPPING_SHIPPING_CUSTOMER_ADDRESS_DELETE_COMPLETE);

        // 会員情報変更時にメールを送信
        if ($this->baseInfoRepository->get()->isOptionMailNotifier()) {
            // 情報のセット
            $userData['userAgent'] = $request->headers->get('User-Agent');
            $userData['ipAddress'] = $request->getClientIp();

            $this->mailService->sendCustomerChangeNotifyMail($Customer, $userData, trans('front.mypage.delivery.notify_title'));
        }

        log_info('お届け先削除処理完了', ['order_id' => $Order->getId(), 'shipping_id' => $Shipping->getId(), 'customer_address_id' => $ca_id]);

        return $this->redirectToRoute('shopping_shipping', ['id' => $Shipping->getId()]);
    }

    /**
     * ログイン画面.
     *
     * @return RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/shopping/login', name: 'shopping_login', methods: ['GET'])]
    #[Template(template: 'Shopping/login.twig')]
    public function login(Request $request): RedirectResponse|array
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('shopping');
        }

        /** @var FormBuilderInterface $builder */
        $builder = $this->formFactory->createNamedBuilder('', CustomerLoginType::class);

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            /** @var Customer|null $Customer */
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
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_SHOPPING_LOGIN_INITIALIZE);

        $form = $builder->getForm();

        return [
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'form' => $form->createView(),
        ];
    }

    /**
     * 購入エラー画面.
     *
     * @return Response|array<empty>
     */
    #[Route(path: '/shopping/error', name: 'shopping_error', methods: ['GET'])]
    #[Template(template: 'Shopping/shopping_error.twig')]
    public function error(Request $request): Response|array
    {
        // 受注とカートのずれを合わせるため, カートのPurchaseFlowをコールする.
        $Cart = $this->cartService->getCart();
        if (null !== $Cart) {
            $this->cartPurchaseFlow->validate($Cart, new PurchaseContext($Cart, $this->getUser()));
            $this->cartService->setPreOrderId(null);
            $this->cartService->save();
        }

        // 購入エラー画面についてはwarninメッセージを出力しない為、warningレベルのメッセージが存在する場合、削除する.
        // (warningが残っている場合、購入エラー画面以降のタイミングで誤って表示されてしまう為.)
        /** @var Session $session */
        $session = $this->session;
        if ($session->getFlashBag()->has('eccube.front.warning')) {
            $session->getFlashBag()->get('eccube.front.warning');
        }

        $event = new EventArgs(
            [],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_SHOPPING_SHIPPING_ERROR_COMPLETE);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        return [];
    }

    /**
     * 注文確定時に配送方法・支払い方法を会員へ保存する.
     *
     * 会員かつ単一配送先で, 保存チェックボックスがONの場合のみ保存する.
     * 保存処理で例外が発生しても注文は継続し, 注文完了画面で保存失敗メッセージを表示する.
     */
    private function savePreferredShippingPayment(Order $Order, FormInterface $form): void
    {
        $Customer = $this->getUser();
        if (!$Customer instanceof Customer) {
            return;
        }

        // 複数配送先の場合は保存しない.
        if ($Order->getShippings()->count() > 1) {
            log_info('[注文処理] 複数配送先のため, 配送方法・支払い方法の保存をスキップします.', [$Order->getId()]);

            return;
        }

        if (!$form->has('save_preferred_shipping_payment') || !$form->get('save_preferred_shipping_payment')->getData()) {
            return;
        }

        try {
            log_info('[注文処理] 配送方法・支払い方法の保存を開始します.', [$Order->getId()]);

            $Payment = $Order->getPayment();
            $Shipping = $Order->getShippings()->first();
            $Delivery = $Shipping ? $Shipping->getDelivery() : null;

            if (!$Payment || !$Delivery) {
                log_warning('[注文処理] 支払い方法または配送方法が取得できないため, 保存をスキップします.', [$Order->getId()]);

                return;
            }

            $Customer->setPreferredPayment($Payment);
            $Customer->setPreferredDelivery($Delivery);
            $this->entityManager->flush();

            log_info('[注文処理] 配送方法・支払い方法の保存が完了しました.', [$Order->getId()]);
        } catch (\Exception $e) {
            // 保存失敗で注文は失敗させない.
            log_error('[注文処理] 配送方法・支払い方法の保存に失敗しました.', [$e->getMessage()]);
            $this->session->getFlashBag()->add('preferred_save_error', 'front.shopping.preferred_save_failed');
        }
    }

    /**
     * 会員の保存済み配送方法・支払い方法を検証する.
     *
     * 表示(index/redirectTo)と復元(restorePreferred)で共用する.
     * すべての検証をパスした場合のみ名称・IDを設定し, 利用できない場合は理由のメッセージキーを返す.
     *
     * @return array{preferredPaymentId: int|null, preferredPaymentName: string|null, preferredDeliveryId: int|null, preferredDeliveryName: string|null, isMultipleShipping: bool, preferredUnavailableReason: string|null}
     */
    private function validatePreferredShippingPayment(Customer $Customer, Order $Order, string $logPrefix = '[保存情報検証]'): array
    {
        $result = [
            'preferredPaymentId' => null,
            'preferredPaymentName' => null,
            'preferredDeliveryId' => null,
            'preferredDeliveryName' => null,
            'isMultipleShipping' => $Order->getShippings()->count() > 1,
            'preferredUnavailableReason' => null,
        ];

        // 会員IDがない場合(非会員)は対象外.
        if (!$Customer->getId()) {
            return $result;
        }

        $PreferredPayment = $Customer->getPreferredPayment();
        $PreferredDelivery = $Customer->getPreferredDelivery();

        if (!$PreferredPayment && !$PreferredDelivery) {
            log_info($logPrefix.' 保存情報が存在しません.', [$Customer->getId()]);

            return $result;
        }

        // 片方のみ保存されている場合(参照先削除によるSET NULL等)は, 欠けている方を利用不可とする.
        if (!$PreferredDelivery) {
            $result['preferredUnavailableReason'] = 'front.shopping.preferred_delivery_unavailable';

            return $result;
        }
        if (!$PreferredPayment) {
            $result['preferredUnavailableReason'] = 'front.shopping.preferred_payment_unavailable';

            return $result;
        }

        // 非公開チェック.
        if (!$PreferredPayment->isVisible()) {
            log_info($logPrefix.' 保存された支払い方法が非公開です.', [$PreferredPayment->getId()]);
            $result['preferredUnavailableReason'] = 'front.shopping.preferred_payment_unavailable';

            return $result;
        }
        if (!$PreferredDelivery->isVisible()) {
            log_info($logPrefix.' 保存された配送方法が非公開です.', [$PreferredDelivery->getId()]);
            $result['preferredUnavailableReason'] = 'front.shopping.preferred_delivery_unavailable';

            return $result;
        }

        // 受注の販売種別に対応する配送方法に, 保存された配送方法が含まれるかチェック.
        $availableDeliveries = $this->deliveryRepository->getDeliveries($Order->getSaleTypes());
        $matchedDelivery = null;
        foreach ($availableDeliveries as $Delivery) {
            if ($Delivery->getId() === $PreferredDelivery->getId()) {
                $matchedDelivery = $Delivery;
                break;
            }
        }
        if (!$matchedDelivery) {
            log_info($logPrefix.' 保存された配送方法が販売種別に対応していません.', [$PreferredDelivery->getId()]);
            $result['preferredUnavailableReason'] = 'front.shopping.preferred_incompatible_combination';

            return $result;
        }

        // 保存された配送方法で利用可能な支払い方法に, 保存された支払い方法が含まれるかチェック.
        $allowedPayments = $this->paymentRepository->findAllowedPayments([$matchedDelivery], true);
        $allowedPaymentIds = [];
        foreach ($allowedPayments as $Payment) {
            if ($Payment->isVisible()) {
                $allowedPaymentIds[] = $Payment->getId();
            }
        }
        if (!in_array($PreferredPayment->getId(), $allowedPaymentIds, true)) {
            log_info($logPrefix.' 保存された配送方法と支払い方法の組み合わせが利用できません.', [$PreferredDelivery->getId(), $PreferredPayment->getId()]);
            $result['preferredUnavailableReason'] = 'front.shopping.preferred_incompatible_combination';

            return $result;
        }

        // すべての検証をパスした場合のみ名称・IDを設定する.
        $result['preferredPaymentId'] = $PreferredPayment->getId();
        $result['preferredPaymentName'] = $PreferredPayment->getMethod();
        $result['preferredDeliveryId'] = $PreferredDelivery->getId();
        $result['preferredDeliveryName'] = $PreferredDelivery->getName();
        log_info($logPrefix.' 検証をパスしました. 復元可能です.', [$PreferredDelivery->getId(), $PreferredPayment->getId()]);

        return $result;
    }

    /**
     * PaymentMethodをコンテナから取得する.
     */
    private function createPaymentMethod(Order $Order, FormInterface $form): PaymentMethodInterface
    {
        $PaymentMethod = $this->serviceContainer->get($Order->getPayment()->getMethodClass());
        $PaymentMethod->setOrder($Order);
        $PaymentMethod->setFormType($form);

        return $PaymentMethod;
    }

    /**
     * PaymentMethod::applyを実行する.
     */
    protected function executeApply(PaymentMethodInterface $paymentMethod): RedirectResponse|Response|null
    {
        $dispatcher = $paymentMethod->apply(); // 決済処理中.

        // リンク式決済のように他のサイトへ遷移する場合などは, dispatcherに処理を移譲する.
        if ($dispatcher instanceof PaymentDispatcher) {
            $response = $dispatcher->getResponse();
            $this->entityManager->flush();

            // dispatcherがresponseを保持している場合はresponseを返す
            if ($response->isRedirection() || $response->isSuccessful()) {
                log_info('[注文処理] PaymentMethod::applyが指定したレスポンスを表示します.');

                return $response;
            }

            // forwardすることも可能.
            if ($dispatcher->isForward()) {
                log_info('[注文処理] PaymentMethod::applyによりForwardします.',
                    [$dispatcher->getRoute(), $dispatcher->getPathParameters(), $dispatcher->getQueryParameters()]);

                return $this->forwardToRoute($dispatcher->getRoute(), $dispatcher->getPathParameters(),
                    $dispatcher->getQueryParameters());
            }
            log_info('[注文処理] PaymentMethod::applyによりリダイレクトします.',
                [$dispatcher->getRoute(), $dispatcher->getPathParameters(), $dispatcher->getQueryParameters()]);

            return $this->redirectToRoute($dispatcher->getRoute(),
                array_merge($dispatcher->getPathParameters(), $dispatcher->getQueryParameters()));
        }

        return null;
    }

    /**
     * PaymentMethod::checkoutを実行する.
     */
    protected function executeCheckout(PaymentMethodInterface $paymentMethod): RedirectResponse|Response|null
    {
        $PaymentResult = $paymentMethod->checkout();
        $response = $PaymentResult->getResponse();
        // PaymentResultがresponseを保持している場合はresponseを返す
        if ($response && ($response->isRedirection() || $response->isSuccessful())) {
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
