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

namespace Eccube\Controller\Admin\Order;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\ShipmentItem;
use Doctrine\Common\EventArgs;
use Eccube\Event\EccubeEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditController extends AbstractController
{
    public function index(Application $app, Request $request, $id = null)
    {
        $TargetOrder = null;
        $OriginOrder = null;

        if (is_null($id)) {
            // 空のエンティティを作成.
            $TargetOrder = $this->newOrder();
        } else {
            $TargetOrder = $app['eccube.repository.order']->find($id);
            if (is_null($TargetOrder)) {
                throw new NotFoundHttpException();
            }
        }

        // 編集前の受注情報を保持
        $OriginOrder = clone $TargetOrder;
        $OriginalOrderDetails = new ArrayCollection();

        foreach ($TargetOrder->getOrderDetails() as $OrderDetail) {
            $OriginalOrderDetails->add($OrderDetail);
        }

        $builder = $app['form.factory']
            ->createBuilder('order', $TargetOrder);

        $form = $builder->getForm();

        $event = new EventArgs(
            array(
                'form' => $form,
                'originOrder' => $OriginOrder,
                'targetOrder' => $TargetOrder,
                'originOrderDetails' => $OriginalOrderDetails
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_INDEX_INITIALIZE, $event);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            // 入力情報にもとづいて再計算.
            $this->calculate($app, $TargetOrder);

            // 登録ボタン押下
            switch ($request->get('mode')) {
                case 'register':
                    if ($TargetOrder->getTotal() > $app['config']['max_total_fee']) {
                        $form['charge']->addError(new FormError('合計金額の上限を超えております。'));
                    } elseif ($form->isValid()) {

                        $BaseInfo = $app['eccube.repository.base_info']->get();

                        // お支払い方法の更新
                        $TargetOrder->setPaymentMethod($TargetOrder->getPayment()->getMethod());

                        // 配送業者・お届け時間の更新
                        $Shippings = $TargetOrder->getShippings();
                        foreach ($Shippings as $Shipping) {
                            $Shipping->setShippingDeliveryName($Shipping->getDelivery()->getName());
                            if (!is_null($Shipping->getDeliveryTime())) {
                                $Shipping->setShippingDeliveryTime($Shipping->getDeliveryTime()->getDeliveryTime());
                            } else {
                                $Shipping->setShippingDeliveryTime(null);
                            }
                        }


                        // 受注日/発送日/入金日の更新.
                        $this->updateDate($app, $TargetOrder, $OriginOrder);

                        // 受注明細で削除されているものをremove
                        foreach ($OriginalOrderDetails as $OrderDetail) {
                            if (false === $TargetOrder->getOrderDetails()->contains($OrderDetail)) {
                                $app['orm.em']->remove($OrderDetail);
                            }
                        }


                        if ($BaseInfo->getOptionMultipleShipping() == Constant::ENABLED) {
                            foreach ($TargetOrder->getOrderDetails() as $OrderDetail) {
                                /** @var $OrderDetail \Eccube\Entity\OrderDetail */
                                $OrderDetail->setOrder($TargetOrder);
                            }

                            /** @var \Eccube\Entity\Shipping $Shipping */
                            foreach ($Shippings as $Shipping) {
                                $shipmentItems = $Shipping->getShipmentItems();
                                /** @var \Eccube\Entity\ShipmentItem $ShipmentItem */
                                foreach ($shipmentItems as $ShipmentItem) {
                                    $ShipmentItem->setOrder($TargetOrder);
                                    $ShipmentItem->setShipping($Shipping);
                                    $app['orm.em']->persist($ShipmentItem);
                                }
                                $Shipping->setOrder($TargetOrder);
                                $app['orm.em']->persist($Shipping);
                            }
                        } else {

                            $NewShipmentItems = new ArrayCollection();

                            foreach ($TargetOrder->getOrderDetails() as $OrderDetail) {
                                /** @var $OrderDetail \Eccube\Entity\OrderDetail */
                                $OrderDetail->setOrder($TargetOrder);

                                $NewShipmentItem = new ShipmentItem();
                                $NewShipmentItem
                                    ->setProduct($OrderDetail->getProduct())
                                    ->setProductClass($OrderDetail->getProductClass())
                                    ->setProductName($OrderDetail->getProduct()->getName())
                                    ->setProductCode($OrderDetail->getProductClass()->getCode())
                                    ->setClassCategoryName1($OrderDetail->getClassCategoryName1())
                                    ->setClassCategoryName2($OrderDetail->getClassCategoryName2())
                                    ->setClassName1($OrderDetail->getClassName1())
                                    ->setClassName2($OrderDetail->getClassName2())
                                    ->setPrice($OrderDetail->getPrice())
                                    ->setQuantity($OrderDetail->getQuantity())
                                    ->setOrder($TargetOrder);
                                $NewShipmentItems[] = $NewShipmentItem;

                            }
                            // 配送商品の更新. delete/insert.
                            $Shippings = $TargetOrder->getShippings();
                            foreach ($Shippings as $Shipping) {
                                $ShipmentItems = $Shipping->getShipmentItems();
                                foreach ($ShipmentItems as $ShipmentItem) {
                                    $app['orm.em']->remove($ShipmentItem);
                                }
                                $ShipmentItems->clear();
                                foreach ($NewShipmentItems as $NewShipmentItem) {
                                    $NewShipmentItem->setShipping($Shipping);
                                    $ShipmentItems->add($NewShipmentItem);
                                }
                            }
                        }

                        $app['orm.em']->persist($TargetOrder);
                        $event = new EventArgs(
                            array(
                                'form' => $form,
                                'originOrder' => $OriginOrder,
                                'targetOrder' => $TargetOrder,
                                'originOrderDetails' => $OriginalOrderDetails
                            ),
                            $request
                        );
                        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_INDEX_COMPLETE, $event);
                        $app['orm.em']->flush();

                        $Customer = $TargetOrder->getCustomer();
                        if ($Customer) {
                            // 会員の場合、購入回数、購入金額などを更新
                            $app['eccube.repository.customer']->updateBuyData($app, $Customer, $TargetOrder->getOrderStatus()->getId());
                        }

                        $app->addSuccess('admin.order.save.complete', 'admin');

                        return $app->redirect($app->url('admin_order_edit', array('id' => $TargetOrder->getId())));
                    }

                    break;

                case 'add_delivery':
                    // お届け先情報の新規追加

                    $form = $builder->getForm();

                    $Shipping = new \Eccube\Entity\Shipping();
                    $Shipping->setDelFlg(Constant::DISABLED);

                    $TargetOrder->addShipping($Shipping);

                    $Shipping->setOrder($TargetOrder);

                    $form->setData($TargetOrder);

                    break;

                default:
                    break;
            }
        }

        // 会員検索フォーム
        $searchCustomerModalForm = $app['form.factory']
            ->createBuilder('admin_search_customer')
            ->getForm();

        $event = new EventArgs(
            array(
                'form' => $searchCustomerModalForm,
                'originOrder' => $OriginOrder,
                'targetOrder' => $TargetOrder,
                'originOrderDetails' => $OriginalOrderDetails
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_INITIALIZE, $event);

        // 商品検索フォーム
        $searchProductModalForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();

        $event = new EventArgs(
            array(
                'form' => $searchProductModalForm,
                'originOrder' => $OriginOrder,
                'targetOrder' => $TargetOrder,
                'originOrderDetails' => $OriginalOrderDetails
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_INITIALIZE, $event);

        // 配送業者のお届け時間
        $times = array();
        $deliveries = $app['eccube.repository.delivery']->findAll();
        foreach ($deliveries as $Delivery) {
            $deliveryTiems = $Delivery->getDeliveryTimes();
            foreach ($deliveryTiems as $DeliveryTime) {
                $times[$Delivery->getId()][$DeliveryTime->getId()] = $DeliveryTime->getDeliveryTime();
            }
        }

        return $app->render('Order/edit.twig', array(
            'form' => $form->createView(),
            'searchCustomerModalForm' => $searchCustomerModalForm->createView(),
            'searchProductModalForm' => $searchProductModalForm->createView(),
            'Order' => $TargetOrder,
            'id' => $id,
            'shippingDeliveryTimes' => $app['serializer']->serialize($times, 'json'),
        ));
    }

    /**
     * 顧客情報を検索する.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function searchCustomer(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $app['monolog']->addDebug('search customer start.');

            $searchData = array(
                'multi' => $request->get('search_word'),
            );

            $Customers = $app['eccube.repository.customer']
                ->getQueryBuilderBySearchData($searchData)
                ->getQuery()
                ->getResult();

            $event = new EventArgs(
                array(
                    'searchData' => $searchData,
                    'customers' => $Customers
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_COMPLETE, $event);

            if (empty($Customers)) {
                $app['monolog']->addDebug('search customer not found.');
            }

            $data = array();

            $formatTel = '%s-%s-%s';
            $formatName = '%s%s(%s%s)';
            foreach ($Customers as $Customer) {
                $data[] = array(
                    'id' => $Customer->getId(),
                    'name' => sprintf($formatName, $Customer->getName01(), $Customer->getName02(), $Customer->getKana01(),
                        $Customer->getKana02()),
                    'tel' => sprintf($formatTel, $Customer->getTel01(), $Customer->getTel02(), $Customer->getTel03()),
                );
            }

            return $app->json($data);
        }
    }

    /**
     * 顧客情報を検索する.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function searchCustomerById(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $app['monolog']->addDebug('search customer by id start.');

            /** @var $Customer \Eccube\Entity\Customer */
            $Customer = $app['eccube.repository.customer']
                ->find($request->get('id'));

            $event = new EventArgs(
                array(
                    'customer' => $Customer
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_BY_ID_COMPLETE, $event);

            if (is_null($Customer)) {
                $app['monolog']->addDebug('search customer by id not found.');

                return $app->json(array(), 404);
            }

            $app['monolog']->addDebug('search customer by id found.');

            $data = array(
                'id' => $Customer->getId(),
                'name01' => $Customer->getName01(),
                'name02' => $Customer->getName02(),
                'kana01' => $Customer->getKana01(),
                'kana02' => $Customer->getKana02(),
                'zip01' => $Customer->getZip01(),
                'zip02' => $Customer->getZip02(),
                'pref' => is_null($Customer->getPref()) ? null : $Customer->getPref()->getId(),
                'addr01' => $Customer->getAddr01(),
                'addr02' => $Customer->getAddr02(),
                'email' => $Customer->getEmail(),
                'tel01' => $Customer->getTel01(),
                'tel02' => $Customer->getTel02(),
                'tel03' => $Customer->getTel03(),
                'fax01' => $Customer->getFax01(),
                'fax02' => $Customer->getFax02(),
                'fax03' => $Customer->getFax03(),
                'company_name' => $Customer->getCompanyName(),
            );

            return $app->json($data);
        }
    }

    public function searchProduct(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $app['monolog']->addDebug('search product start.');

            $searchData = array(
                'name' => $request->get('id'),
            );

            if ($categoryId = $request->get('category_id')) {
                $Category = $app['eccube.repository.category']->find($categoryId);
                $searchData['category_id'] = $Category;
            }

            /** @var $Products \Eccube\Entity\Product[] */
            $Products = $app['eccube.repository.product']
                ->getQueryBuilderBySearchData($searchData)
                ->getQuery()
                ->getResult();

            $event = new EventArgs(
                array(
                    'searchData' => $searchData,
                    'products' => $Products
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_COMPLETE, $event);

            if (empty($Products)) {
                $app['monolog']->addDebug('search product not found.');
            }

            $forms = array();
            foreach ($Products as $Product) {
                /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
                $builder = $app['form.factory']->createNamedBuilder('', 'add_cart', null, array(
                    'product' => $Product,
                ));
                $addCartForm = $builder->getForm();
                $forms[$Product->getId()] = $addCartForm->createView();
            }

            return $app->render('Order/search_product.twig', array(
                'forms' => $forms,
                'Products' => $Products,
            ));
        }
    }

    protected function newOrder()
    {
        $Order = new \Eccube\Entity\Order();
        $Shipping = new \Eccube\Entity\Shipping();
        $Shipping->setDelFlg(0);
        $Order->addShipping($Shipping);
        $Shipping->setOrder($Order);

        return $Order;
    }

    /**
     * フォームからの入直内容に基づいて、受注情報の再計算を行う
     *
     * @param $app
     * @param $Order
     */
    protected function calculate($app, \Eccube\Entity\Order $Order)
    {
        $taxtotal = 0;
        $subtotal = 0;

        // 受注明細データの税・小計を再計算
        /** @var $OrderDetails \Eccube\Entity\OrderDetail[] */
        $OrderDetails = $Order->getOrderDetails();
        foreach ($OrderDetails as $OrderDetail) {
            // 新規登録の場合は, 入力されたproduct_id/produc_class_idから明細にセットする.
            if (!$OrderDetail->getId()) {
                $TaxRule = $app['eccube.repository.tax_rule']->getByRule($OrderDetail->getProduct(),
                    $OrderDetail->getProductClass());
                $OrderDetail->setTaxRule($TaxRule->getCalcRule()->getId());
                $OrderDetail->setProductName($OrderDetail->getProduct()->getName());
                $OrderDetail->setProductCode($OrderDetail->getProductClass()->getCode());
                $OrderDetail->setClassName1($OrderDetail->getProductClass()->hasClassCategory1()
                    ? $OrderDetail->getProductClass()->getClassCategory1()->getClassName()->getName()
                    : null);
                $OrderDetail->setClassName2($OrderDetail->getProductClass()->hasClassCategory2()
                    ? $OrderDetail->getProductClass()->getClassCategory2()->getClassName()->getName()
                    : null);
                $OrderDetail->setClassCategoryName1($OrderDetail->getProductClass()->hasClassCategory1()
                    ? $OrderDetail->getProductClass()->getClassCategory1()->getName()
                    : null);
                $OrderDetail->setClassCategoryName2($OrderDetail->getProductClass()->hasClassCategory2()
                    ? $OrderDetail->getProductClass()->getClassCategory2()->getName()
                    : null);
            }

            // 税
            $tax = $app['eccube.service.tax_rule']
                ->calcTax($OrderDetail->getPrice(), $OrderDetail->getTaxRate(), $OrderDetail->getTaxRule());
            $OrderDetail->setPriceIncTax($OrderDetail->getPrice() + $tax);

            $taxtotal += $tax;

            // 小計
            $subtotal += $OrderDetail->getTotalPrice();
        }

        $shippings = $Order->getShippings();
        /** @var \Eccube\Entity\Shipping $Shipping */
        foreach ($shippings as $Shipping) {
            $shipmentItems = $Shipping->getShipmentItems();
            $Shipping->setDelFlg(Constant::DISABLED);
            /** @var \Eccube\Entity\ShipmentItem $ShipmentItem */
            foreach ($shipmentItems as $ShipmentItem) {
                $ShipmentItem->setProductName($ShipmentItem->getProduct()->getName());
                $ShipmentItem->setProductCode($ShipmentItem->getProductClass()->getCode());
                $ShipmentItem->setClassName1($ShipmentItem->getProductClass()->hasClassCategory1()
                    ? $ShipmentItem->getProductClass()->getClassCategory1()->getClassName()->getName()
                    : null);
                $ShipmentItem->setClassName2($ShipmentItem->getProductClass()->hasClassCategory2()
                    ? $ShipmentItem->getProductClass()->getClassCategory2()->getClassName()->getName()
                    : null);
                $ShipmentItem->setClassCategoryName1($ShipmentItem->getProductClass()->hasClassCategory1()
                    ? $ShipmentItem->getProductClass()->getClassCategory1()->getName()
                    : null);
                $ShipmentItem->setClassCategoryName2($ShipmentItem->getProductClass()->hasClassCategory2()
                    ? $ShipmentItem->getProductClass()->getClassCategory2()->getName()
                    : null);
            }
        }

        // 受注データの税・小計・合計を再計算
        $Order->setTax($taxtotal);
        $Order->setSubtotal($subtotal);
        $Order->setTotal($subtotal + $Order->getCharge() + $Order->getDeliveryFeeTotal() - $Order->getDiscount());
        // お支払い合計は、totalと同一金額(2系ではtotal - point)
        $Order->setPaymentTotal($Order->getTotal());
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
     *
     * @param $app
     * @param $TargetOrder
     * @param $OriginOrder
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
