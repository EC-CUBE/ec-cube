<?php

namespace Eccube\Controller\Admin\Shipping;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Application;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Shipping;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\AddCartType;
use Eccube\Form\Type\Admin\SearchOrderType;
use Eccube\Form\Type\Admin\ShippingType;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Form\Type\Admin\SearchProductType;
use Eccube\Form\Type\Admin\ShipmentItemType;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * // FIXME UrlGenerator で {_admin} を認識しない問題あり
 * @Route("/admin/shipping")
 */
class EditController
{
    /**
     * 出荷登録/編集画面.
     *
     * @Route("/edit", name="admin/shipping/new")
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="admin/shipping/edit")
     * @Template("shipping/edit.twig")
     *
     * TODO templateアノテーションを利用するかどうか検討.http://symfony.com/doc/current/best_practices/controllers.html
     */
    public function edit(Application $app, Request $request, $id = null)
    {
        /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
        $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(array(
            'Eccube\Entity\ProductClass',
            'Eccube\Entity\Product',
        ));

        $TargetOrder = null;
        $OriginOrder = null;

        if (is_null($id)) {
            // 空のエンティティを作成.
            $TargetOrder = new Shipping();
        } else {
            $TargetOrder = $app['eccube.repository.shipping']->find($id);
            if (is_null($TargetOrder)) {
                throw new NotFoundHttpException();
            }
        }

        // 編集前の受注情報を保持
        $OriginOrder = clone $TargetOrder;
        $OriginalOrderDetails = new ArrayCollection();
        // 編集前のお届け先情報を保持
        $OriginalShippings = new ArrayCollection();
        // 編集前のお届け先のアイテム情報を保持
        $OriginalShipmentItems = new ArrayCollection();

        foreach ($TargetOrder->getShipmentItems() as $OrderDetail) {
            $OriginalOrderDetails->add($OrderDetail);
        }

        // // 編集前の情報を保持
        // foreach ($TargetOrder->getShippings() as $tmpOriginalShippings) {
        //     foreach ($tmpOriginalShippings->getShipmentItems() as $tmpOriginalShipmentItem) {
        //         // アイテム情報
        //         $OriginalShipmentItems->add($tmpOriginalShipmentItem);
        //     }
        //     // お届け先情報
        //     $OriginalShippings->add($tmpOriginalShippings);
        // }

        $builder = $app['form.factory']
            ->createBuilder(ShippingType::class, $TargetOrder);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'OriginOrder' => $OriginOrder,
                'TargetOrder' => $TargetOrder,
                'OriginOrderDetails' => $OriginalOrderDetails,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $event = new EventArgs(
                array(
                    'builder' => $builder,
                    'OriginOrder' => $OriginOrder,
                    'TargetOrder' => $TargetOrder,
                    'OriginOrderDetails' => $OriginalOrderDetails,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_INDEX_PROGRESS, $event);

            // FIXME 税額計算は CalculateService で処理する. ここはテストを通すための暫定処理
            // see EditControllerTest::testOrderProcessingWithTax
            $OrderDetails = $TargetOrder->getShipmentItems();
            $taxtotal = 0;
            foreach ($OrderDetails as $OrderDetail) {
                $tax = $app['eccube.service.tax_rule']
                    ->calcTax($OrderDetail->getPrice(), $OrderDetail->getTaxRate(), $OrderDetail->getTaxRule());
                $OrderDetail->setPriceIncTax($OrderDetail->getPrice() + $tax);

                $taxtotal += $tax * $OrderDetail->getQuantity();
            }

            // 登録ボタン押下
            switch ($request->get('mode')) {
                case 'register':

                    log_info('受注登録開始', array($TargetOrder->getId()));
                    // TODO 在庫の有無や販売制限数のチェックなども行う必要があるため、完了処理もcaluclatorのように抽象化できないか検討する.
                    if ($form->isValid()) {

                        $BaseInfo = $app['eccube.repository.base_info']->get();

                        // TODO 後続にある会員情報の更新のように、完了処理もcaluclatorのように抽象化できないか検討する.
                        // 受注日/発送日/入金日の更新.
                        // $this->updateDate($app, $TargetOrder, $OriginOrder);

                        // 画面上で削除された明細をremove
                        foreach ($OriginalOrderDetails as $OrderDetail) {
                            if (false === $TargetOrder->getShipmentItems()->contains($OrderDetail)) {
                                $OrderDetail->setShipping(null);
                            }
                        }

                        // 複数配送の場合,
                        if ($BaseInfo->getOptionMultipleShipping() == Constant::ENABLED) {
                            foreach ($TargetOrder->getOrderDetails() as $OrderDetail) {
                                $OrderDetail->setOrder($TargetOrder);
                            }
                            $Shippings = $TargetOrder->getShippings();
                            foreach ($Shippings as $Shipping) {
                                $shipmentItems = $Shipping->getShipmentItems();
                                foreach ($shipmentItems as $ShipmentItem) {
                                    // 削除予定から商品アイテムを外す
                                    $OriginalShipmentItems->removeElement($ShipmentItem);
                                    $ShipmentItem->setOrder($TargetOrder);
                                    $ShipmentItem->setShipping($Shipping);
                                    $app['orm.em']->persist($ShipmentItem);
                                }
                                // 削除予定からお届け先情報を外す
                                $OriginalShippings->removeElement($Shipping);
                                $Shipping->setOrder($TargetOrder);
                                $app['orm.em']->persist($Shipping);
                            }
                            // 商品アイテムを削除する
                            foreach ($OriginalShipmentItems as $OriginalShipmentItem) {
                                $app['orm.em']->remove($OriginalShipmentItem);
                            }
                            // お届け先情報削除する
                            foreach ($OriginalShippings as $OriginalShipping) {
                                $app['orm.em']->remove($OriginalShipping);
                            }
                        } else {
                            // 単一配送の場合, ShippimentItemsはOrderDetailの内容をコピーし、delete/insertで作り直す.
                            // TODO あまり本質的な処理ではないので簡略化したい.
                            // $Shipping = $TargetOrder->getShippings()->first();
                            // foreach ($Shipping->getShipmentItems() as $ShipmentItem) {
                            //     $Shipping->removeShipmentItem($ShipmentItem);
                            //     $app['orm.em']->remove($ShipmentItem);
                            // }
                            // foreach ($TargetOrder->getOrderDetails() as $OrderDetail) {
                            //     $OrderDetail->setOrder($TargetOrder);
                            //     if ($OrderDetail->getProduct()) {
                            //         $ShipmentItem = new ShipmentItem();
                            //         $ShipmentItem->copyProperties($OrderDetail);
                            //         $ShipmentItem->setShipping($Shipping);
                            //         $Shipping->addShipmentItem($ShipmentItem);
                            //     }
                            // }
                        }
                        foreach ($TargetOrder->getShipmentItems() as $ShipmentItem) {
                            $ShipmentItem->setShipping($TargetOrder);
                        }
                        $app['orm.em']->persist($TargetOrder);
                        $app['orm.em']->flush();

                        // TODO 集計系に移動
//                        if ($Customer) {
//                            // 会員の場合、購入回数、購入金額などを更新
//                            $app['eccube.repository.customer']->updateBuyData($app, $Customer, $TargetOrder->getOrderStatus()->getId());
//                        }

                        $event = new EventArgs(
                            array(
                                'form' => $form,
                                'OriginOrder' => $OriginOrder,
                                'TargetOrder' => $TargetOrder,
                                'OriginOrderDetails' => $OriginalOrderDetails,
                                //'Customer' => $Customer,
                            ),
                            $request
                        );
                        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_INDEX_COMPLETE, $event);

                        $app->addSuccess('admin.order.save.complete', 'admin');

                        log_info('受注登録完了', array($TargetOrder->getId()));

                        return $app->redirect($app->url('admin/shipping/edit', array('id' => $TargetOrder->getId())));
                    }

                    break;

                case 'add_delivery':
                    // お届け先情報の新規追加

                    $form = $builder->getForm();

                    $Shipping = new \Eccube\Entity\Shipping();
                    $TargetOrder->addShipping($Shipping);

                    $Shipping->setOrder($TargetOrder);

                    $form->setData($TargetOrder);

                    break;

                default:
                    break;
            }
        }

        // 会員検索フォーム
        $builder = $app['form.factory']
            ->createBuilder(SearchCustomerType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'OriginOrder' => $OriginOrder,
                'TargetOrder' => $TargetOrder,
                'OriginOrderDetails' => $OriginalOrderDetails,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_INITIALIZE, $event);

        $searchCustomerModalForm = $builder->getForm();

        // 商品検索フォーム
        $builder = $app['form.factory']
            ->createBuilder(SearchProductType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'OriginOrder' => $OriginOrder,
                'TargetOrder' => $TargetOrder,
                'OriginOrderDetails' => $OriginalOrderDetails,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_INITIALIZE, $event);

        $searchProductModalForm = $builder->getForm();

        // 配送業者のお届け時間
        $times = array();
        $deliveries = $app['eccube.repository.delivery']->findAll();
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
            'Order' => $TargetOrder, // Deprecated
            'Shipping' => $TargetOrder,
            'id' => $id,
            'shippingDeliveryTimes' => $app['serializer']->serialize($times, 'json'),
        ];
    }

    /**
     * @Route("/search/product", name="admin_shipping_search_product")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("shipping/search_product.twig")
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchProduct(Application $app, Request $request, $page_no = null)
    {
        if ($request->isXmlHttpRequest()) {
            $app['monolog']->addDebug('search product start.');
            $page_count = $app['config']['default_page_count'];
            $session = $app['session'];

            if ('POST' === $request->getMethod()) {

                $page_no = 1;

                $searchData = array(
                    'id' => $request->get('id'),
                );

                if ($categoryId = $request->get('category_id')) {
                    $Category = $app['eccube.repository.category']->find($categoryId);
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
            // TODO ShipmentItemRepository に移動
            $qb = $app['eccube.repository.shipment_item']->createQueryBuilder('s')
                ->where('s.Shipping is null AND s.Order is not null')
                ->andWhere('s.OrderItemType in (1, 2)');

            $event = new EventArgs(
                array(
                    'qb' => $qb,
                    'searchData' => $searchData,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_SEARCH, $event);

            /** @var \Knp\Component\Pager\Pagination\SlidingPagination $pagination */
            $pagination = $app['paginator']()->paginate(
                $qb,
                $page_no,
                $page_count,
                array('wrap-queries' => true)
            );

            $ShipmentItems = $pagination->getItems();

            if (empty($ShipmentItems)) {
                $app['monolog']->addDebug('search product not found.');
            }

            $forms = array();
            foreach ($ShipmentItems as $ShipmentItem) {
                /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
                $builder = $app['form.factory']->createNamedBuilder('', ShipmentItemType::class, $ShipmentItem);
                $addCartForm = $builder->getForm();
                $forms[$ShipmentItem->getId()] = $addCartForm->createView();
            }

            $event = new EventArgs(
                array(
                    'forms' => $forms,
                    'ShipmentItems' => $ShipmentItems,
                    'pagination' => $pagination,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_COMPLETE, $event);

            return [
                'forms' => $forms,
                'ShipmentItems' => $ShipmentItems,
                'pagination' => $pagination,
            ];
        }
    }
}
