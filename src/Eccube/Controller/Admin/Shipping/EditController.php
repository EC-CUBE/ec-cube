<?php

namespace Eccube\Controller\Admin\Shipping;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\ShippingStatus;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\OrderItemType;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\Form\Type\Admin\ShippingType;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\Master\ShippingStatusRepository;
use Eccube\Repository\OrderItemRepository;
use Eccube\Repository\ShippingRepository;
use Eccube\Service\TaxRuleService;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class EditController extends AbstractController
{
    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @var TaxRuleService
     */
    protected $taxRuleService;

    /**
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * @var ShippingStatusRepository
     */
    protected $shippingStatusReposisotry;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(
        OrderItemRepository $orderItemRepository,
        CategoryRepository $categoryRepository,
        DeliveryRepository $deliveryRepository,
        TaxRuleService $taxRuleService,
        ShippingRepository $shippingRepository,
        ShippingStatusRepository $shippingStatusReposisotry,
        SerializerInterface $serializer
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->taxRuleService = $taxRuleService;
        $this->shippingRepository = $shippingRepository;
        $this->shippingStatusReposisotry = $shippingStatusReposisotry;
        $this->serializer = $serializer;
    }


    /**
     * 出荷登録/編集画面.
     *
     * @Route("/%admin_route%/shipping/new", name="admin_shipping_new")
     * @Route("/%admin_route%/shipping/{id}/edit", requirements={"id" = "\d+"}, name="admin_shipping_edit")
     * @Template("@admin/Shipping/edit.twig")
     *
     * TODO templateアノテーションを利用するかどうか検討.http://symfony.com/doc/current/best_practices/controllers.html
     */
    public function edit(Request $request, $id = null)
    {
        $TargetShipping = null;
        $OriginShipping = null;

        if (is_null($id)) {
            // 空のエンティティを作成.
            $TargetShipping = new Shipping();
        } else {
            $TargetShipping = $this->shippingRepository->find($id);
            if (is_null($TargetShipping)) {
                throw new NotFoundHttpException();
            }
        }

        // 編集前の受注情報を保持
        $OriginShipping = clone $TargetShipping;
        // 編集前のお届け先のアイテム情報を保持
        $OriginalOrderItems = new ArrayCollection();

        foreach ($TargetShipping->getOrderItems() as $OrderItem) {
            $OriginalOrderItems->add($OrderItem);
        }

        $builder = $this->formFactory
            ->createBuilder(ShippingType::class, $TargetShipping);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'OriginShipping' => $OriginShipping,
                'TargetShipping' => $TargetShipping,
                'OriginalOrderItems' => $OriginalOrderItems,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $event = new EventArgs(
                array(
                    'builder' => $builder,
                    'OriginShipping' => $OriginShipping,
                    'TargetShipping' => $TargetShipping,
                    'OriginalOrderItems' => $OriginalOrderItems,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_INDEX_PROGRESS, $event);

            // FIXME 税額計算は CalculateService で処理する. ここはテストを通すための暫定処理
            // see EditControllerTest::testOrderProcessingWithTax
            $OrderItems = $TargetShipping->getOrderItems();
            $taxtotal = 0;
            foreach ($OrderItems as $OrderItem) {
                $tax = $this->taxRuleService
                    ->calcTax($OrderItem->getPrice(), $OrderItem->getTaxRate(), $OrderItem->getTaxRule());
                $OrderItem->setPriceIncTax($OrderItem->getPrice() + $tax);

                $taxtotal += $tax * $OrderItem->getQuantity();
            }

            // 登録ボタン押下
            switch ($request->get('mode')) {
                case 'register_and_commit':
                    if ($form->isValid()) {
                        $ShippingStatus = $this->shippingStatusReposisotry->find(ShippingStatus::SHIPPED);
                        $TargetShipping->setShippingStatus($ShippingStatus);
                        $TargetShipping->setShippingDate(new \DateTime());
                    }
                // no break
                case 'register':

                    log_info('出荷登録開始', array($TargetShipping->getId()));
                    // TODO 在庫の有無や販売制限数のチェックなども行う必要があるため、完了処理もcaluclatorのように抽象化できないか検討する.
                    if ($form->isValid()) {
                        // TODO 後続にある会員情報の更新のように、完了処理もcaluclatorのように抽象化できないか検討する.

                        // 画面上で削除された明細をremove
                        foreach ($OriginalOrderItems as $OrderItem) {
                            if (false === $TargetShipping->getOrderItems()->contains($OrderItem)) {
                                $OrderItem->setShipping(null);
                            }
                        }

                        foreach ($TargetShipping->getOrderItems() as $OrderItem) {
                            $OrderItem->setShipping($TargetShipping);
                        }
                        $this->entityManager->persist($TargetShipping);
                        $this->entityManager->flush();

                        $event = new EventArgs(
                            array(
                                'form' => $form,
                                'OriginShipping' => $OriginShipping,
                                'TargetShipping' => $TargetShipping,
                                'OriginalOrderItems' => $OriginalOrderItems,
                                //'Customer' => $Customer,
                            ),
                            $request
                        );
                        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_INDEX_COMPLETE, $event);

                        $this->addSuccess('admin.order.save.complete', 'admin');

                        log_info('出荷登録完了', array($TargetShipping->getId()));

                        return $this->redirectToRoute('admin_shipping_edit', array('id' => $TargetShipping->getId()));
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
            array(
                'builder' => $builder,
                'OriginShipping' => $OriginShipping,
                'TargetShipping' => $TargetShipping,
                'OriginalOrderItems' => $OriginalOrderItems,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_INITIALIZE, $event);

        $searchCustomerModalForm = $builder->getForm();

        // 商品検索フォーム
        $builder = $this->formFactory
            ->createBuilder(SearchProductType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'OriginShipping' => $OriginShipping,
                'TargetShipping' => $TargetShipping,
                'OriginalOrderItems' => $OriginalOrderItems,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_INITIALIZE, $event);

        $searchProductModalForm = $builder->getForm();

        // 配送業者のお届け時間
        $times = array();
        $deliveries = $this->deliveryRepository->findAll();
        foreach ($deliveries as $Delivery) {
            $deliveryTiems = $Delivery->getDeliveryTimes();
            foreach ($deliveryTiems as $DeliveryTime) {
                $times[$Delivery->getId()][$DeliveryTime->getId()] = $DeliveryTime->getDeliveryTime();
            }
        }

        return [
            'form' => $form->createView(),
            'shippingForm' => $form->createView(),
            'searchCustomerModalForm' => $searchCustomerModalForm->createView(),
            'searchProductModalForm' => $searchProductModalForm->createView(),
            'Shipping' => $TargetShipping,
            'id' => $id,
            'shippingDeliveryTimes' => $this->serializer->serialize($times, 'json'),
        ];
    }

    /**
     * @Route("/%admin_route%/shipping/search/product", name="admin_shipping_search_product")
     * @Template("@admin/shipping/search_product.twig")
     */
    public function searchProduct(Request $request, $page_no = null, Paginator $paginator)
    {
        if ($request->isXmlHttpRequest()) {
            log_debug('search product start.');
            $page_count = $this->eccubeConfig['default_page_count'];
            $session = $this->session;

            if ('POST' === $request->getMethod()) {

                $page_no = 1;

                $searchData = array(
                    'id' => $request->get('id'),
                );

                if ($categoryId = $request->get('category_id')) {
                    $Category = $this->categoryRepository->find($categoryId);
                    $searchData['category_id'] = $Category;
                }

                $session->set('eccube.admin.order.product.search', $searchData);
                $session->set('eccube.admin.order.product.search.page_no', $page_no);
            } else {
                $searchData = (array)$session->get('eccube.admin.order.product.search');
                if (is_null($page_no)) {
                    $page_no = intval($session->get('eccube.admin.order.product.search.page_no'));
                } else {
                    $session->set('eccube.admin.order.product.search.page_no', $page_no);
                }
            }
            // TODO OrderItemRepository に移動
            $qb = $this->orderItemRepository->createQueryBuilder('s')
                ->where('s.Shipping is null AND s.Order is not null')
                ->andWhere('s.OrderItemType in (1, 2)');

            $event = new EventArgs(
                array(
                    'qb' => $qb,
                    'searchData' => $searchData,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_SEARCH, $event);

            /** @var \Knp\Component\Pager\Pagination\SlidingPagination $pagination */
            $pagination = $paginator->paginate(
                $qb,
                $page_no,
                $page_count,
                array('wrap-queries' => true)
            );

            $OrderItems = $pagination->getItems();

            if (empty($OrderItems)) {
                log_debug('search product not found.');
            }

            $forms = array();
            foreach ($OrderItems as $OrderItem) {
                /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
                $builder = $this->formFactory->createNamedBuilder('', OrderItemType::class, $OrderItem);
                $addCartForm = $builder->getForm();
                $forms[$OrderItem->getId()] = $addCartForm->createView();
            }

            $event = new EventArgs(
                array(
                    'forms' => $forms,
                    'OrderItems' => $OrderItems,
                    'pagination' => $pagination,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_COMPLETE, $event);

            return [
                'forms' => $forms,
                'OrderItems' => $OrderItems,
                'pagination' => $pagination,
            ];
        }
    }
}
