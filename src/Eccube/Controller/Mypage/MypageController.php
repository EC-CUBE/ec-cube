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

namespace Eccube\Controller\Mypage;

use Eccube\Controller\AbstractController;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Form\Type\Front\CustomerLoginType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CustomerFavoriteProductRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MypageController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CustomerFavoriteProductRepository
     */
    protected $customerFavoriteProductRepository;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
     * MypageController constructor.
     *
     * @param OrderRepository $orderRepository
     * @param CustomerFavoriteProductRepository $customerFavoriteProductRepository
     * @param CartService $cartService
     * @param BaseInfoRepository $baseInfoRepository
     * @param PurchaseFlow $purchaseFlow
     */
    public function __construct(
        OrderRepository $orderRepository,
        CustomerFavoriteProductRepository $customerFavoriteProductRepository,
        CartService $cartService,
        BaseInfoRepository $baseInfoRepository,
        PurchaseFlow $purchaseFlow
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerFavoriteProductRepository = $customerFavoriteProductRepository;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->cartService = $cartService;
        $this->purchaseFlow = $purchaseFlow;
    }

    /**
     * ログイン画面.
     *
     * @Route("/mypage/login", name="mypage_login", methods={"GET", "POST"})
     * @Template("Mypage/login.twig")
     */
    public function login(Request $request, AuthenticationUtils $utils)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            log_info('認証済のためログイン処理をスキップ');

            return $this->redirectToRoute('mypage');
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $builder = $this->formFactory
            ->createNamedBuilder('', CustomerLoginType::class);

        $builder->get('login_memory')->setData((bool) $request->getSession()->get('_security.login_memory'));

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $Customer = $this->getUser();
            if ($Customer instanceof Customer) {
                $builder->get('login_email')
                    ->setData($Customer->getEmail());
            }
        }

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_MYPAGE_LOGIN_INITIALIZE);

        $form = $builder->getForm();

        return [
            'error' => $utils->getLastAuthenticationError(),
            'form' => $form->createView(),
        ];
    }

    /**
     * マイページ.
     *
     * @Route("/mypage/", name="mypage", methods={"GET"})
     * @Template("Mypage/index.twig")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $Customer = $this->getUser();

        // 購入処理中/決済処理中ステータスの受注を非表示にする.
        $this->entityManager
            ->getFilters()
            ->enable('incomplete_order_status_hidden');

        // paginator
        $qb = $this->orderRepository->getQueryBuilderByCustomer($Customer);

        $event = new EventArgs(
            [
                'qb' => $qb,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_MYPAGE_INDEX_SEARCH);

        $pagination = $paginator->paginate(
            $qb,
            $request->get('pageno', 1),
            $this->eccubeConfig['eccube_search_pmax']
        );

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * 購入履歴詳細を表示する.
     *
     * @Route("/mypage/history/{order_no}", name="mypage_history", methods={"GET"})
     * @Template("Mypage/history.twig")
     */
    public function history(Request $request, $order_no)
    {
        $this->entityManager->getFilters()
            ->enable('incomplete_order_status_hidden');
        $Order = $this->orderRepository->findOneBy(
            [
                'order_no' => $order_no,
                'Customer' => $this->getUser(),
            ]
        );

        $event = new EventArgs(
            [
                'Order' => $Order,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_MYPAGE_HISTORY_INITIALIZE);

        /** @var Order $Order */
        $Order = $event->getArgument('Order');

        if (!$Order) {
            throw new NotFoundHttpException();
        }

        $stockOrder = true;
        foreach ($Order->getOrderItems() as $orderItem) {
            if ($orderItem->isProduct() && $orderItem->getQuantity() < 0) {
                $stockOrder = false;
                break;
            }
        }

        return [
            'Order' => $Order,
            'stockOrder' => $stockOrder,
        ];
    }

    /**
     * 再購入を行う.
     *
     * @Route("/mypage/order/{order_no}", name="mypage_order", methods={"PUT"})
     */
    public function order(Request $request, $order_no)
    {
        $this->isTokenValid();

        log_info('再注文開始', [$order_no]);

        $Customer = $this->getUser();

        /* @var $Order \Eccube\Entity\Order */
        $Order = $this->orderRepository->findOneBy(
            [
                'order_no' => $order_no,
                'Customer' => $Customer,
            ]
        );

        $event = new EventArgs(
            [
                'Order' => $Order,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_MYPAGE_ORDER_INITIALIZE);

        if (!$Order) {
            log_info('対象の注文が見つかりません', [$order_no]);
            throw new NotFoundHttpException();
        }

        // エラーメッセージの配列
        $errorMessages = [];

        foreach ($Order->getOrderItems() as $OrderItem) {
            try {
                if ($OrderItem->getProduct() && $OrderItem->getProductClass()) {
                    $this->cartService->addProduct($OrderItem->getProductClass(), $OrderItem->getQuantity());

                    // 明細の正規化
                    $Carts = $this->cartService->getCarts();
                    foreach ($Carts as $Cart) {
                        $result = $this->purchaseFlow->validate($Cart, new PurchaseContext($Cart, $this->getUser()));
                        // 復旧不可のエラーが発生した場合は追加した明細を削除.
                        if ($result->hasError()) {
                            $this->cartService->removeProduct($OrderItem->getProductClass());
                            foreach ($result->getErrors() as $error) {
                                $errorMessages[] = $error->getMessage();
                            }
                        }
                        foreach ($result->getWarning() as $warning) {
                            $errorMessages[] = $warning->getMessage();
                        }
                    }

                    $this->cartService->save();
                }
            } catch (CartException $e) {
                log_info($e->getMessage(), [$order_no]);
                $this->addRequestError($e->getMessage());
            }
        }

        foreach ($errorMessages as $errorMessage) {
            $this->addRequestError($errorMessage);
        }

        $event = new EventArgs(
            [
                'Order' => $Order,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_MYPAGE_ORDER_COMPLETE);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        log_info('再注文完了', [$order_no]);

        return $this->redirect($this->generateUrl('cart'));
    }

    /**
     * お気に入り商品を表示する.
     *
     * @Route("/mypage/favorite", name="mypage_favorite", methods={"GET"})
     * @Template("Mypage/favorite.twig")
     */
    public function favorite(Request $request, PaginatorInterface $paginator)
    {
        if (!$this->BaseInfo->isOptionFavoriteProduct()) {
            throw new NotFoundHttpException();
        }
        $Customer = $this->getUser();

        // paginator
        $qb = $this->customerFavoriteProductRepository->getQueryBuilderByCustomer($Customer);

        $event = new EventArgs(
            [
                'qb' => $qb,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_MYPAGE_FAVORITE_SEARCH);

        $pagination = $paginator->paginate(
            $qb,
            $request->get('pageno', 1),
            $this->eccubeConfig['eccube_search_pmax'],
            ['wrap-queries' => true]
        );

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * お気に入り商品を削除する.
     *
     * @Route("/mypage/favorite/{id}/delete", name="mypage_favorite_delete", methods={"DELETE"}, requirements={"id" = "\d+"})
     */
    public function delete(Request $request, Product $Product)
    {
        $this->isTokenValid();

        $Customer = $this->getUser();

        log_info('お気に入り商品削除開始', [$Customer->getId(), $Product->getId()]);

        $CustomerFavoriteProduct = $this->customerFavoriteProductRepository->findOneBy(['Customer' => $Customer, 'Product' => $Product]);

        if ($CustomerFavoriteProduct) {
            $this->customerFavoriteProductRepository->delete($CustomerFavoriteProduct);
        } else {
            throw new BadRequestHttpException();
        }

        $event = new EventArgs(
            [
                'Customer' => $Customer,
                'CustomerFavoriteProduct' => $CustomerFavoriteProduct,
            ], $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_MYPAGE_MYPAGE_DELETE_COMPLETE);

        log_info('お気に入り商品削除完了', [$Customer->getId(), $CustomerFavoriteProduct->getId()]);

        return $this->redirect($this->generateUrl('mypage_favorite'));
    }
}
