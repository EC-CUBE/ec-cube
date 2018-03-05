<?php

namespace Eccube\Controller\Admin\Shipping;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\ShippingStatus;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Form\Type\Admin\ShippingType;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\Master\ShippingStatusRepository;
use Eccube\Repository\OrderItemRepository;
use Eccube\Repository\ShippingRepository;
use Eccube\Service\TaxRuleService;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
     * @Route("/%eccube_admin_route%/shipping/new", name="admin_shipping_new")
     * @Route("/%eccube_admin_route%/shipping/{id}/edit", requirements={"id" = "\d+"}, name="admin_shipping_edit")
     * @Template("@admin/Shipping/edit.twig")
     */
    public function edit(Request $request, $id = null)
    {
        $TargetShipping = null;
        $OriginShipping = null;

        if (null === $id) {
            // 空のエンティティを作成.
            $TargetShipping = new Shipping();
        } else {
            $TargetShipping = $this->shippingRepository->find($id);
            if (null === $TargetShipping) {
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

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

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

                        $this->addSuccess('admin.shipping.edit.save.complete', 'admin');

                        log_info('出荷登録完了', array($TargetShipping->getId()));

                        return $this->redirectToRoute('admin_shipping_edit', array('id' => $TargetShipping->getId()));
                    }

                    break;

                default:
                    break;
            }
        }

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
            'Shipping' => $TargetShipping,
            'shippingDeliveryTimes' => $this->serializer->serialize($times, 'json'),
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/shipping/search/product", name="admin_shipping_search_product")
     * @Template("@admin/Shipping/search_product.twig")
     */
    public function searchProduct(Request $request, PaginatorInterface $paginator)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $page_no = (int)$request->get('pageno', 1);
        $page_count = $this->eccubeConfig['eccube_default_page_count'];

        // TODO OrderItemRepository に移動
        $qb = $this->orderItemRepository->createQueryBuilder('s')
            ->where('s.Shipping is null AND s.Order is not null')
            ->andWhere('s.OrderItemType in (1, 2)');

        /** @var \Knp\Component\Pager\Pagination\SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $qb,
            $page_no,
            $page_count,
            array('wrap-queries' => true)
        );

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/shipping/search/item", name="admin_shipping_search_item")
     * @Template("@admin/Shipping/order_item_prototype.twig")
     */
    public function searchItem(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $id = (int)$request->get('order-item-id');
        /** @var OrderItem $OrderItem */
        $OrderItem = $this->orderItemRepository->find($id);
        if (null === $OrderItem) {
            // not found.
            return $this->json([], 404);
        }

        return [
            'OrderItem' => $OrderItem,
        ];
    }

}
