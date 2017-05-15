<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Acme\Controller\Admin\Order;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\ShipmentItem;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\AddCartType;
use Eccube\Form\Type\Admin\OrderType;
use Eccube\Form\Type\Admin\SearchCustomerType;
use Eccube\Form\Type\Admin\SearchProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * TODO 管理画面のルーティングは動的に行う.おそらくコントローラのディレクトリをフロント/管理で分ける必要がある
 *
 * @Route("/admin/order")
 */
class EditController extends AbstractController
{
    /**
     * 受注登録/編集画面.
     *
     * @Route("/edit", name="admin_order_new")
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="admin_order_edit")
     * @Template("Order/edit.twig")
     *
     * TODO templateアノテーションを利用するかどうか検討.http://symfony.com/doc/current/best_practices/controllers.html
     */
    public function index(Application $app, Request $request, $id = null)
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
            $TargetOrder = $app['eccube.repository.order']->find($id);
            if (is_null($TargetOrder)) {
                throw new NotFoundHttpException();
            }
        }

        // 編集前の受注情報を保持
        $OriginOrder = clone $TargetOrder;
        // $OriginalOrderDetails = new ArrayCollection();
        // 編集前のお届け先情報を保持
        $OriginalShippings = new ArrayCollection();
        // 編集前のお届け先のアイテム情報を保持
        $OriginalShipmentItems = new ArrayCollection();

        // foreach ($TargetOrder->getOrderDetails() as $OrderDetail) {
        //     $OriginalOrderDetails->add($OrderDetail);
        // }

        // 編集前の情報を保持
        foreach ($TargetOrder->getShipmentItems() as $tmpShipmentItem) {
            $OriginalShipmentItems->add($tmpShipmentItem);
        }
        
        // foreach ($TargetOrder->getShippings() as $tmpOriginalShippings) {
        //     foreach ($tmpOriginalShippings->getShipmentItems() as $tmpOriginalShipmentItem) {
        //         // アイテム情報
        //         $OriginalShipmentItems->add($tmpOriginalShipmentItem);
        //     }
        //     // お届け先情報
        //     $OriginalShippings->add($tmpOriginalShippings);
        // }

        $builder = $app['form.factory']
            ->createBuilder(OrderType::class, $TargetOrder);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'OriginOrder' => $OriginOrder,
                'TargetOrder' => $TargetOrder,
                // 'OriginOrderDetails' => $OriginalOrderDetails,
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
                    // 'OriginOrderDetails' => $OriginalOrderDetails,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_INDEX_PROGRESS, $event);

            // FIXME 税額計算は CalculateService で処理する. ここはテストを通すための暫定処理
            // see EditControllerTest::testOrderProcessingWithTax
            // $OrderDetails = $TargetOrder->getOrderDetails();
            // $taxtotal = 0;
            // foreach ($OrderDetails as $OrderDetail) {
            //     $tax = $app['eccube.service.tax_rule']
            //         ->calcTax($OrderDetail->getPrice(), $OrderDetail->getTaxRate(), $OrderDetail->getTaxRule());
            //     $OrderDetail->setPriceIncTax($OrderDetail->getPrice() + $tax);

            //     $taxtotal += $tax * $OrderDetail->getQuantity();
            // }
            // $TargetOrder->setTax($taxtotal);

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

                        // 画面上で削除された明細をremove
                        foreach ($OriginalShipmentItems as $ShipmentItem) {
                            if (false === $TargetOrder->getShipmentItems()->contains($ShipmentItem)) {
                                $app['orm.em']->remove($ShipmentItem);
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
                            /*
                            if (is_object($Shipping)) {
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
                            */
                        }
                        // foreach ($TargetOrder->getOrderDetails() as $OrderDetail) {
                        //     // XXX OrderDetail は使用しないため削除
                        //     $TargetOrder->removeOrderDetail($OrderDetail);
                        //     $app['orm.em']->remove($OrderDetail);
                        // }
                        foreach ($TargetOrder->getShipmentItems() as $ShipmentItem) {
                            $ShipmentItem->setOrder($TargetOrder);
                        }

                        $TargetOrder->setDeliveryFeeTotal($TargetOrder->calculateDeliveryFeeTotal()); // FIXME
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
                                // 'OriginOrderDetails' => $OriginalOrderDetails,
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
                // 'OriginOrderDetails' => $OriginalOrderDetails,
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
                // 'OriginOrderDetails' => $OriginalOrderDetails,
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
            'searchCustomerModalForm' => $searchCustomerModalForm->createView(),
            'searchProductModalForm' => $searchProductModalForm->createView(),
            'Order' => $TargetOrder,
            'id' => $id,
            'shippingDeliveryTimes' => $app['serializer']->serialize($times, 'json'),
        ];
    }

    /**
     * 受注ステータスに応じて, 受注日/入金日/発送日を更新する,
     * 発送済ステータスが設定された場合は, お届け先情報の発送日も更新を行う.
     *
     * 編集の場合
     * - 受注ステータスが他のステータスから発送済へ変更された場合に発送日を更新
     * - 受注ステータスが他のステータスから入金済へ変更された場合に入金日を更新
     *
     * 新規登録の場合
     * - 受注日を更新
     * - 受注ステータスが発送済に設定された場合に発送日を更新
     * - 受注ステータスが入金済に設定された場合に入金日を更新
     *
     * @param $app
     * @param $TargetOrder
     * @param $OriginOrder
     * @see \Eccube\Controller\Admin\Order\EditController::updateDate
     *
     * TODO Service へ移動する
     */
    protected function updateDate($app, $TargetOrder, $OriginOrder)
    {
        $dateTime = new \DateTime();

        // 編集
        if ($TargetOrder->getId()) {
            // 発送済
            if ($TargetOrder->getOrderStatus()->getId() == $app['config']['order_deliv']) {
                // 編集前と異なる場合のみ更新
                if ($TargetOrder->getOrderStatus()->getId() != $OriginOrder->getOrderStatus()->getId()) {
                    $TargetOrder->setCommitDate($dateTime);
                    // お届け先情報の発送日も更新する.
                    $Shippings = $TargetOrder->getShippings();
                    foreach ($Shippings as $Shipping) {
                        $Shipping->setShippingCommitDate($dateTime);
                    }
                }
                // 入金済
            } elseif ($TargetOrder->getOrderStatus()->getId() == $app['config']['order_pre_end']) {
                // 編集前と異なる場合のみ更新
                if ($TargetOrder->getOrderStatus()->getId() != $OriginOrder->getOrderStatus()->getId()) {
                    $TargetOrder->setPaymentDate($dateTime);
                }
            }
            // 新規
        } else {
            // 発送済
            if ($TargetOrder->getOrderStatus()->getId() == $app['config']['order_deliv']) {
                $TargetOrder->setCommitDate($dateTime);
                // お届け先情報の発送日も更新する.
                $Shippings = $TargetOrder->getShippings();
                foreach ($Shippings as $Shipping) {
                    $Shipping->setShippingCommitDate($dateTime);
                }
                // 入金済
            } elseif ($TargetOrder->getOrderStatus()->getId() == $app['config']['order_pre_end']) {
                $TargetOrder->setPaymentDate($dateTime);
            }
            // 受注日時
            $TargetOrder->setOrderDate($dateTime);
        }
    }
}
