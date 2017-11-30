<?php

namespace Eccube\Controller\Admin\Shipping;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Application;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route(service=EditController::class)
 */
class EditController
{
    /**
     * @Inject(OrderItemRepository::class)
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @Inject(CategoryRepository::class)
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @Inject("session")
     * @var Session
     */
    protected $session;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject("monolog")
     * @var Logger
     */
    protected $logger;

    /**
     * @Inject("serializer")
     * @var Serializer
     */
    protected $serializer;

    /**
     * @Inject(DeliveryRepository::class)
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @Inject(TaxRuleService::class)
     * @var TaxRuleService
     */
    protected $taxRuleService;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject(ShippingRepository::class)
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * @Inject(ShippingStatusRepository::class)
     * @var ShippingStatusRepository
     */
    protected $shippingStatusReposisotry;

    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * 出荷登録/編集画面.
     *
     * @Route("/{_admin}/shipping/edit", name="admin/shipping/new")
     * @Route("/{_admin}/shipping/{id}/edit", requirements={"id" = "\d+"}, name="admin/shipping/edit")
     * @Template("Shipping/edit.twig")
     *
     * TODO templateアノテーションを利用するかどうか検討.http://symfony.com/doc/current/best_practices/controllers.html
     */
    public function edit(Application $app, Request $request, $id = null)
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

                        $app->addSuccess('admin.order.save.complete', 'admin');

                        log_info('出荷登録完了', array($TargetShipping->getId()));

                        return $app->redirect($app->url('admin/shipping/edit', array('id' => $TargetShipping->getId())));
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
     * @Route("/{_admin}/shipping/search/product", name="admin_shipping_search_product")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("shipping/search_product.twig")
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchProduct(Application $app, Request $request, $page_no = null)
    {
        if ($request->isXmlHttpRequest()) {
            $this->logger->addDebug('search product start.');
            $page_count = $this->appConfig['default_page_count'];
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
            $pagination = $app['paginator']()->paginate(
                $qb,
                $page_no,
                $page_count,
                array('wrap-queries' => true)
            );

            $OrderItems = $pagination->getItems();

            if (empty($OrderItems)) {
                $this->logger->addDebug('search product not found.');
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
