<?php

namespace Acme\Controller\Admin\Shipping;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Application;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\SearchOrderType;
use Eccube\Form\Type\Admin\ShippingType;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Form\Type\Admin\SearchProductType;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * // FIXME UrlGenerator で {_admin} を認識しない問題あり
 * @Route("/admin/shipping")
 */
class ShippingController
{
    /**
     * @Route("/", name="admin/shipping")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("shipping/index.twig")
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request, $page_no = null)
    {
        $session = $request->getSession();

        $builder = $app['form.factory']
            ->createBuilder(SearchOrderType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_INITIALIZE, $event);

        $searchForm = $builder->getForm();

        $pagination = array();

        $disps = $app['eccube.repository.master.disp']->findAll();
        $pageMaxis = $app['eccube.repository.master.page_max']->findAll();
        $page_count = $app['config']['default_page_count'];
        $page_status = null;
        $active = false;

        if ('POST' === $request->getMethod()) {

            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();

                // paginator
                $qb = $app['eccube.repository.shipping']->getQueryBuilderBySearchDataForAdmin($searchData);

                $event = new EventArgs(
                    array(
                        'form' => $searchForm,
                        'qb' => $qb,
                    ),
                    $request
                );
                $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_SEARCH, $event);

                $page_no = 1;
                $pagination = $app['paginator']()->paginate(
                    $qb,
                    $page_no,
                    $page_count
                );

                // sessionのデータ保持
                $session->set('eccube.admin.order.search', $searchData);
                $session->set('eccube.admin.order.search.page_no', $page_no);
            }
        } else {
            if (is_null($page_no) && $request->get('resume') != Constant::ENABLED) {
                // sessionを削除
                $session->remove('eccube.admin.order.search');
                $session->remove('eccube.admin.order.search.page_no');
            } else {
                // pagingなどの処理
                $searchData = $session->get('eccube.admin.order.search');
                if (is_null($page_no)) {
                    $page_no = intval($session->get('eccube.admin.order.search.page_no'));
                } else {
                    $session->set('eccube.admin.order.search.page_no', $page_no);
                }

                if (!is_null($searchData)) {

                    // 公開ステータス
                    $status = $request->get('status');
                    if (!empty($status)) {
                        if ($status != $app['config']['admin_product_stock_status']) {
                            $searchData['status']->clear();
                            $searchData['status']->add($status);
                        } else {
                            $searchData['stock_status'] = $app['config']['disabled'];
                        }
                        $page_status = $status;
                    }
                    // 表示件数
                    $pcount = $request->get('page_count');

                    $page_count = empty($pcount) ? $page_count : $pcount;

                    $qb = $app['eccube.repository.order']->getQueryBuilderBySearchDataForAdmin($searchData);

                    $event = new EventArgs(
                        array(
                            'form' => $searchForm,
                            'qb' => $qb,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_INDEX_SEARCH, $event);

                    $pagination = $app['paginator']()->paginate(
                        $qb,
                        $page_no,
                        $page_count
                    );

                    // セッションから検索条件を復元
                    if (!empty($searchData['status'])) {
                        $searchData['status'] = $app['eccube.repository.master.order_status']->find($searchData['status']);
                    }
                    if (count($searchData['multi_status']) > 0) {
                        $statusIds = array();
                        foreach ($searchData['multi_status'] as $Status) {
                            $statusIds[] = $Status->getId();
                        }
                        $searchData['multi_status'] = $app['eccube.repository.master.order_status']->findBy(array('id' => $statusIds));
                    }
                    if (count($searchData['sex']) > 0) {
                        $sex_ids = array();
                        foreach ($searchData['sex'] as $Sex) {
                            $sex_ids[] = $Sex->getId();
                        }
                        $searchData['sex'] = $app['eccube.repository.master.sex']->findBy(array('id' => $sex_ids));
                    }
                    if (count($searchData['payment']) > 0) {
                        $payment_ids = array();
                        foreach ($searchData['payment'] as $Payment) {
                            $payment_ids[] = $Payment->getId();
                        }
                        $searchData['payment'] = $app['eccube.repository.payment']->findBy(array('id' => $payment_ids));
                    }
                    $searchForm->setData($searchData);
                }
            }
        }

        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'disps' => $disps,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_status' => $page_status,
            'page_count' => $page_count,
            'active' => $active,
        ];
    }

    /**
     * 出荷登録/編集画面.
     *
     * @Route("/edit", name="admin/shipping/edit")
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
            $TargetOrder = $this->newOrder($app);
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
            $OrderDetails = $TargetOrder->getOrderDetails();
            $taxtotal = 0;
            foreach ($OrderDetails as $OrderDetail) {
                $tax = $app['eccube.service.tax_rule']
                    ->calcTax($OrderDetail->getPrice(), $OrderDetail->getTaxRate(), $OrderDetail->getTaxRule());
                $OrderDetail->setPriceIncTax($OrderDetail->getPrice() + $tax);

                $taxtotal += $tax * $OrderDetail->getQuantity();
            }
            $TargetOrder->setTax($taxtotal);

            // 入力情報にもとづいて再計算.
            // TODO 購入フローのように、明細の自動生成をどこまで行うか検討する. 単純集計でよいような気がする
            // 集計は,この1行でいけるはず
            // プラグインで Strategy をセットしたりする
            // TODO 編集前のOrder情報が必要かもしれない
            $app['eccube.service.calculate']($TargetOrder, $TargetOrder->getCustomer())->calculate();

            // 登録ボタン押下
            switch ($request->get('mode')) {
                case 'register':

                    log_info('受注登録開始', array($TargetOrder->getId()));

                    // TODO 在庫の有無や販売制限数のチェックなども行う必要があるため、完了処理もcaluclatorのように抽象化できないか検討する.
                    if ($TargetOrder->getTotal() > $app['config']['max_total_fee']) {
                        log_info('受注登録入力チェックエラー', array($TargetOrder->getId()));
                        $form['charge']->addError(new FormError('合計金額の上限を超えております。'));
                    } elseif ($form->isValid()) {

                        $BaseInfo = $app['eccube.repository.base_info']->get();

                        // TODO 後続にある会員情報の更新のように、完了処理もcaluclatorのように抽象化できないか検討する.
                        // 受注日/発送日/入金日の更新.
                        $this->updateDate($app, $TargetOrder, $OriginOrder);

                        // 画面上で削除された明細は、受注明細で削除されているものをremove
                        foreach ($OriginalOrderDetails as $OrderDetail) {
                            if (false === $TargetOrder->getOrderDetails()->contains($OrderDetail)) {
                                $app['orm.em']->remove($OrderDetail);
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
                            $Shipping = $TargetOrder->getShippings()->first();
                            foreach ($Shipping->getShipmentItems() as $ShipmentItem) {
                                $Shipping->removeShipmentItem($ShipmentItem);
                                $app['orm.em']->remove($ShipmentItem);
                            }
                            foreach ($TargetOrder->getOrderDetails() as $OrderDetail) {
                                $OrderDetail->setOrder($TargetOrder);
                                if ($OrderDetail->getProduct()) {
                                    $ShipmentItem = new ShipmentItem();
                                    $ShipmentItem->copyProperties($OrderDetail);
                                    $ShipmentItem->setShipping($Shipping);
                                    $Shipping->addShipmentItem($ShipmentItem);
                                }
                            }
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

                        return $app->redirect($app->url('admin_order_edit', array('id' => $TargetOrder->getId())));
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
}
