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

namespace Eccube\Controller\Admin\Order;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\ShoppingException;
use Eccube\Form\Type\AddCartType;
use Eccube\Form\Type\Admin\OrderType;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Service\OrderHelper;
use Eccube\Service\OrderStateMachine;
use Eccube\Service\PurchaseFlow\Processor\OrderNoProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseException;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\TaxRuleService;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class EditController extends AbstractController
{
    /**
     * @var TaxRuleService
     */
    protected $taxRuleService;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderNoProcessor
     */
    protected $orderNoProcessor;

    /**
     * @var OrderItemTypeRepository
     */
    protected $orderItemTypeRepository;

    /**
     * @var OrderStateMachine
     */
    protected $orderStateMachine;

    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var OrderHelper
     */
    private $orderHelper;

    /**
     * EditController constructor.
     *
     * @param TaxRuleService $taxRuleService
     * @param DeviceTypeRepository $deviceTypeRepository
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @param CustomerRepository $customerRepository
     * @param SerializerInterface $serializer
     * @param DeliveryRepository $deliveryRepository
     * @param PurchaseFlow $orderPurchaseFlow
     * @param OrderRepository $orderRepository
     * @param OrderNoProcessor $orderNoProcessor
     * @param OrderItemTypeRepository $orderItemTypeRepository
     * @param OrderStatusRepository $orderStatusRepository
     * @param OrderStateMachine $orderStateMachine
     * @param OrderHelper $orderHelper
     */
    public function __construct(
        TaxRuleService $taxRuleService,
        DeviceTypeRepository $deviceTypeRepository,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        CustomerRepository $customerRepository,
        SerializerInterface $serializer,
        DeliveryRepository $deliveryRepository,
        PurchaseFlow $orderPurchaseFlow,
        OrderRepository $orderRepository,
        OrderNoProcessor $orderNoProcessor,
        OrderItemTypeRepository $orderItemTypeRepository,
        OrderStatusRepository $orderStatusRepository,
        OrderStateMachine $orderStateMachine,
        OrderHelper $orderHelper
    ) {
        $this->taxRuleService = $taxRuleService;
        $this->deviceTypeRepository = $deviceTypeRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->customerRepository = $customerRepository;
        $this->serializer = $serializer;
        $this->deliveryRepository = $deliveryRepository;
        $this->purchaseFlow = $orderPurchaseFlow;
        $this->orderRepository = $orderRepository;
        $this->orderNoProcessor = $orderNoProcessor;
        $this->orderItemTypeRepository = $orderItemTypeRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderStateMachine = $orderStateMachine;
        $this->orderHelper = $orderHelper;
    }

    /**
     * 受注登録/編集画面.
     *
     * @Route("/%eccube_admin_route%/order/new", name="admin_order_new", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/order/{id}/edit", requirements={"id" = "\d+"}, name="admin_order_edit", methods={"GET", "POST"})
     * @Template("@admin/Order/edit.twig")
     */
    public function index(Request $request, RouterInterface $router, $id = null)
    {
        if (null === $id) {
            // 空のエンティティを作成.
            $TargetOrder = new Order();
            $TargetOrder->addShipping((new Shipping())->setOrder($TargetOrder));

            $preOrderId = $this->orderHelper->createPreOrderId();
            $TargetOrder->setPreOrderId($preOrderId);
        } else {
            $TargetOrder = $this->orderRepository->find($id);
            if (null === $TargetOrder) {
                throw new NotFoundHttpException();
            }
        }

        // 編集前の受注情報を保持
        $OriginOrder = clone $TargetOrder;
        $OriginItems = new ArrayCollection();
        foreach ($TargetOrder->getOrderItems() as $Item) {
            $OriginItems->add($Item);
        }

        $builder = $this->formFactory->createBuilder(OrderType::class, $TargetOrder);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'OriginOrder' => $OriginOrder,
                'TargetOrder' => $TargetOrder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_INDEX_INITIALIZE);

        $form = $builder->getForm();

        $form->handleRequest($request);
        $purchaseContext = new PurchaseContext($OriginOrder, $OriginOrder->getCustomer());

        if ($form->isSubmitted() && $form['OrderItems']->isValid()) {
            $event = new EventArgs(
                [
                    'builder' => $builder,
                    'OriginOrder' => $OriginOrder,
                    'TargetOrder' => $TargetOrder,
                    'PurchaseContext' => $purchaseContext,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_INDEX_PROGRESS);

            $flowResult = $this->purchaseFlow->validate($TargetOrder, $purchaseContext);

            if ($flowResult->hasWarning()) {
                foreach ($flowResult->getWarning() as $warning) {
                    $this->addWarning($warning->getMessage(), 'admin');
                }
            }

            if ($flowResult->hasError()) {
                foreach ($flowResult->getErrors() as $error) {
                    $this->addError($error->getMessage(), 'admin');
                }
            }

            // 登録ボタン押下
            switch ($request->get('mode')) {
                case 'register':
                    log_info('受注登録開始', [$TargetOrder->getId()]);

                    if (!$flowResult->hasError() && $form->isValid()) {
                        try {
                            $this->purchaseFlow->prepare($TargetOrder, $purchaseContext);
                            $this->purchaseFlow->commit($TargetOrder, $purchaseContext);
                        } catch (PurchaseException $e) {
                            $this->addError($e->getMessage(), 'admin');
                            break;
                        }

                        $OldStatus = $OriginOrder->getOrderStatus();
                        $NewStatus = $TargetOrder->getOrderStatus();

                        // ステータスが変更されている場合はステートマシンを実行.
                        if ($TargetOrder->getId() && $OldStatus->getId() != $NewStatus->getId()) {
                            // 発送済に変更された場合は, 発送日をセットする.
                            if ($NewStatus->getId() == OrderStatus::DELIVERED) {
                                $TargetOrder->getShippings()->map(function (Shipping $Shipping) {
                                    if (!$Shipping->isShipped()) {
                                        $Shipping->setShippingDate(new \DateTime());
                                    }
                                });
                            }
                            // ステートマシンでステータスは更新されるので, 古いステータスに戻す.
                            $TargetOrder->setOrderStatus($OldStatus);
                            try {
                                // FormTypeでステータスの遷移チェックは行っているのでapplyのみ実行.
                                $this->orderStateMachine->apply($TargetOrder, $NewStatus);
                            } catch (ShoppingException $e) {
                                $this->addError($e->getMessage(), 'admin');
                                break;
                            }
                        }

                        $this->entityManager->persist($TargetOrder);
                        $this->entityManager->flush();

                        foreach ($OriginItems as $Item) {
                            if ($TargetOrder->getOrderItems()->contains($Item) === false) {
                                $this->entityManager->remove($Item);
                            }
                        }
                        $this->entityManager->flush();

                        // 新規登録時はMySQL対応のためflushしてから採番
                        $this->orderNoProcessor->process($TargetOrder, $purchaseContext);
                        $this->entityManager->flush();

                        // 会員の場合、購入回数、購入金額などを更新
                        if ($Customer = $TargetOrder->getCustomer()) {
                            $this->orderRepository->updateOrderSummary($Customer);
                            $this->entityManager->flush();
                        }

                        $event = new EventArgs(
                            [
                                'form' => $form,
                                'OriginOrder' => $OriginOrder,
                                'TargetOrder' => $TargetOrder,
                                'Customer' => $Customer,
                            ],
                            $request
                        );
                        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_INDEX_COMPLETE);

                        $this->addSuccess('admin.common.save_complete', 'admin');

                        log_info('受注登録完了', [$TargetOrder->getId()]);

                        if ($returnLink = $form->get('return_link')->getData()) {
                            try {
                                // $returnLinkはpathの形式で渡される. pathが存在するかをルータでチェックする.
                                $pattern = '/^'.preg_quote($request->getBasePath(), '/').'/';
                                $returnLink = preg_replace($pattern, '', $returnLink);
                                $result = $router->match($returnLink);
                                // パラメータのみ抽出
                                $params = array_filter($result, function ($key) {
                                    return 0 !== \strpos($key, '_');
                                }, ARRAY_FILTER_USE_KEY);

                                // pathからurlを再構築してリダイレクト.
                                return $this->redirectToRoute($result['_route'], $params);
                            } catch (\Exception $e) {
                                // マッチしない場合はログ出力してスキップ.
                                log_warning('URLの形式が不正です。');
                            }
                        }

                        return $this->redirectToRoute('admin_order_edit', ['id' => $TargetOrder->getId()]);
                    }

                    break;
                default:
                    break;
            }
        }

        // 会員検索フォーム
        $builder = $this->formFactory
            ->createBuilder(SearchCustomerType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'OriginOrder' => $OriginOrder,
                'TargetOrder' => $TargetOrder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_INITIALIZE);

        $searchCustomerModalForm = $builder->getForm();

        // 商品検索フォーム
        $builder = $this->formFactory
            ->createBuilder(SearchProductType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'OriginOrder' => $OriginOrder,
                'TargetOrder' => $TargetOrder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_INITIALIZE);

        $searchProductModalForm = $builder->getForm();

        // 配送業者のお届け時間
        $times = [];
        $deliveries = $this->deliveryRepository->findAll();
        foreach ($deliveries as $Delivery) {
            $deliveryTimes = $Delivery->getDeliveryTimes();
            foreach ($deliveryTimes as $DeliveryTime) {
                $times[$Delivery->getId()][$DeliveryTime->getId()] = $DeliveryTime->getDeliveryTime();
            }
        }

        return [
            'form' => $form->createView(),
            'searchCustomerModalForm' => $searchCustomerModalForm->createView(),
            'searchProductModalForm' => $searchProductModalForm->createView(),
            'Order' => $TargetOrder,
            'id' => $id,
            'shippingDeliveryTimes' => $this->serializer->serialize($times, 'json'),
        ];
    }

    /**
     * 顧客情報を検索する.
     *
     * @Route("/%eccube_admin_route%/order/search/customer/html", name="admin_order_search_customer_html", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/order/search/customer/html/page/{page_no}", requirements={"page_no" = "\d+"}, name="admin_order_search_customer_html_page", methods={"GET", "POST"})
     * @Template("@admin/Order/search_customer.twig")
     *
     * @param Request $request
     * @param integer $page_no
     *
     * @return array
     */
    public function searchCustomerHtml(Request $request, PaginatorInterface $paginator, $page_no = null)
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            log_debug('search customer start.');
            $page_count = $this->eccubeConfig['eccube_default_page_count'];
            $session = $this->session;

            if ('POST' === $request->getMethod()) {
                $page_no = 1;

                $searchData = [
                    'multi' => $request->get('search_word'),
                    'customer_status' => [
                        CustomerStatus::REGULAR,
                    ],
                ];

                $session->set('eccube.admin.order.customer.search', $searchData);
                $session->set('eccube.admin.order.customer.search.page_no', $page_no);
            } else {
                $searchData = (array) $session->get('eccube.admin.order.customer.search');
                if (is_null($page_no)) {
                    $page_no = intval($session->get('eccube.admin.order.customer.search.page_no'));
                } else {
                    $session->set('eccube.admin.order.customer.search.page_no', $page_no);
                }
            }

            $qb = $this->customerRepository->getQueryBuilderBySearchData($searchData);

            $event = new EventArgs(
                [
                    'qb' => $qb,
                    'data' => $searchData,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_SEARCH);

            /** @var \Knp\Component\Pager\Pagination\SlidingPagination $pagination */
            $pagination = $paginator->paginate(
                $qb,
                $page_no,
                $page_count,
                ['wrap-queries' => true]
            );

            /** @var $Customers \Eccube\Entity\Customer[] */
            $Customers = $pagination->getItems();

            if (empty($Customers)) {
                log_debug('search customer not found.');
            }

            $data = [];
            $formatName = '%s%s(%s%s)';
            foreach ($Customers as $Customer) {
                $data[] = [
                    'id' => $Customer->getId(),
                    'name' => sprintf($formatName, $Customer->getName01(), $Customer->getName02(),
                        $Customer->getKana01(),
                        $Customer->getKana02()),
                    'phone_number' => $Customer->getPhoneNumber(),
                    'email' => $Customer->getEmail(),
                ];
            }

            $event = new EventArgs(
                [
                    'data' => $data,
                    'Customers' => $pagination,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_COMPLETE);
            $data = $event->getArgument('data');

            return [
                'data' => $data,
                'pagination' => $pagination,
            ];
        }

        throw new BadRequestHttpException();
    }

    /**
     * 顧客情報を検索する.
     *
     * @Route("/%eccube_admin_route%/order/search/customer/id", name="admin_order_search_customer_by_id", methods={"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function searchCustomerById(Request $request)
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            log_debug('search customer by id start.');

            /** @var $Customer \Eccube\Entity\Customer */
            $Customer = $this->customerRepository
                ->find($request->get('id'));

            $event = new EventArgs(
                [
                    'Customer' => $Customer,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_BY_ID_INITIALIZE);

            if (is_null($Customer)) {
                log_debug('search customer by id not found.');

                return $this->json([], 404);
            }

            log_debug('search customer by id found.');

            $data = [
                'id' => $Customer->getId(),
                'name01' => $Customer->getName01(),
                'name02' => $Customer->getName02(),
                'kana01' => $Customer->getKana01(),
                'kana02' => $Customer->getKana02(),
                'postal_code' => $Customer->getPostalCode(),
                'pref' => is_null($Customer->getPref()) ? null : $Customer->getPref()->getId(),
                'addr01' => $Customer->getAddr01(),
                'addr02' => $Customer->getAddr02(),
                'email' => $Customer->getEmail(),
                'phone_number' => $Customer->getPhoneNumber(),
                'company_name' => $Customer->getCompanyName(),
            ];

            $event = new EventArgs(
                [
                    'data' => $data,
                    'Customer' => $Customer,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_BY_ID_COMPLETE);
            $data = $event->getArgument('data');

            return $this->json($data);
        }

        throw new BadRequestHttpException();
    }

    /**
     * @Route("/%eccube_admin_route%/order/search/product", name="admin_order_search_product", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/order/search/product/page/{page_no}", requirements={"page_no" = "\d+"}, name="admin_order_search_product_page", methods={"GET", "POST"})
     * @Template("@admin/Order/search_product.twig")
     */
    public function searchProduct(Request $request, PaginatorInterface $paginator, $page_no = null)
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            log_debug('search product start.');
            $page_count = $this->eccubeConfig['eccube_default_page_count'];
            $session = $this->session;

            if ('POST' === $request->getMethod()) {
                $page_no = 1;

                $searchData = [
                    'id' => $request->get('id'),
                ];

                if ($categoryId = $request->get('category_id')) {
                    $Category = $this->categoryRepository->find($categoryId);
                    $searchData['category_id'] = $Category;
                }

                $session->set('eccube.admin.order.product.search', $searchData);
                $session->set('eccube.admin.order.product.search.page_no', $page_no);
            } else {
                $searchData = (array) $session->get('eccube.admin.order.product.search');
                if (is_null($page_no)) {
                    $page_no = intval($session->get('eccube.admin.order.product.search.page_no'));
                } else {
                    $session->set('eccube.admin.order.product.search.page_no', $page_no);
                }
            }

            $qb = $this->productRepository
                ->getQueryBuilderBySearchDataForAdmin($searchData);

            $event = new EventArgs(
                [
                    'qb' => $qb,
                    'searchData' => $searchData,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_SEARCH);

            /** @var \Knp\Component\Pager\Pagination\SlidingPagination $pagination */
            $pagination = $paginator->paginate(
                $qb,
                $page_no,
                $page_count,
                ['wrap-queries' => true]
            );

            /** @var $Products \Eccube\Entity\Product[] */
            $Products = $pagination->getItems();

            if (empty($Products)) {
                log_debug('search product not found.');
            }

            $forms = [];
            foreach ($Products as $Product) {
                /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
                $builder = $this->formFactory->createNamedBuilder('', AddCartType::class, null, [
                    'product' => $Product,
                ]);
                $addCartForm = $builder->getForm();
                $forms[$Product->getId()] = $addCartForm->createView();
            }

            $event = new EventArgs(
                [
                    'forms' => $forms,
                    'Products' => $Products,
                    'pagination' => $pagination,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_COMPLETE);

            return [
                'forms' => $forms,
                'Products' => $Products,
                'pagination' => $pagination,
            ];
        }
    }

    /**
     * その他明細情報を取得
     *
     * @Route("/%eccube_admin_route%/order/search/order_item_type", name="admin_order_search_order_item_type", methods={"POST"})
     * @Template("@admin/Order/order_item_type.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function searchOrderItemType(Request $request)
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            log_debug('search order item type start.');

            $Charge = $this->entityManager->find(OrderItemType::class, OrderItemType::CHARGE);
            $DeliveryFee = $this->entityManager->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
            $Discount = $this->entityManager->find(OrderItemType::class, OrderItemType::DISCOUNT);

            $NonTaxable = $this->entityManager->find(TaxType::class, TaxType::NON_TAXABLE);
            $Taxation = $this->entityManager->find(TaxType::class, TaxType::TAXATION);

            $OrderItemTypes = [
                ['OrderItemType' => $Charge, 'TaxType' => $Taxation],
                ['OrderItemType' => $DeliveryFee, 'TaxType' => $Taxation],
                ['OrderItemType' => $Discount, 'TaxType' => $Taxation],
                ['OrderItemType' => $Discount, 'TaxType' => $NonTaxable],
            ];

            return [
                'OrderItemTypes' => $OrderItemTypes,
            ];
        }

        throw new BadRequestHttpException();
    }
}
