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

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditController extends AbstractController
{
    public function index(Application $app, Request $request, $id = null)
    {
        $TargetOrder = null;
        $OriginOrder = null;

        if (is_null($id)) {
            $TargetOrder = $this->newOrder();
        } else {
            // todo 決済処理中, 購入処理中は除く
            $TargetOrder = $app['eccube.repository.order']->find($id);
            if (is_null($TargetOrder)) {
                throw new NotFoundHttpException();
            }
        }

        // 編集前の受注情報を保持
        $OriginOrder = clone $TargetOrder;

        $form = $app['form.factory']
            ->createBuilder('order', $TargetOrder)
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            // 入力情報にもとづいて再計算.
            $this->calculate($app, $TargetOrder);

            if ($form->isValid()) {
                // 受注日/発送日/入金日の更新.
                $this->updateDate($TargetOrder, $OriginOrder);

                $app['orm.em']->persist($TargetOrder);
                $app['orm.em']->flush();

                $app->addSuccess('admin.order.save.complete', 'admin');

                return $app->redirect($app->url('admin_order_edit', array('id' => $TargetOrder->getId())));
            }
        }

        return $app->render('Order/edit.twig', array(
            'form' => $form->createView(),
            'Order' => $TargetOrder,
        ));
    }

    public function searchCustomer(Application $app, Request $request)
    {
        $searchData = array(
            'multi' => $request->get('search_word'),
        );

        $Customers = $app['eccube.repository.customer']
            ->getQueryBuilderBySearchData($searchData)
            ->getQuery()
            ->getResult();

        $data = array();

        $formatTel = '%s-%s-%s';
        $formatName = '%s%s(%s%s)';
        foreach ($Customers as $Customer) {
            $data[] = array(
                'id' => $Customer->getId(),
                'name' => sprintf($formatName, $Customer->getName01(),  $Customer->getName02(),  $Customer->getKana01(), $Customer->getKana02()),
                'tel' => sprintf($formatTel, $Customer->getTel01(), $Customer->getTel02(), $Customer->getTel03()),
            );
        }
        return $app->json($data);
    }

    public function searchCustomerById(Application $app, Request $request)
    {
        $app['monolog']->addDebug('search customer by id start.');

        /** @var $Customer \Eccube\Entity\Customer */
        $Customer = $app['eccube.repository.customer']
            ->find($request->get('id'));

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
        );

        return $app->json($data);
    }

    public function searchProduct(Application $app, Request $request)
    {
        $data = array();

        return $app->json($data, 200);
    }

    protected function newOrder()
    {
        $Order = new \Eccube\Entity\Order();
        $Order->setDelFlg(0);
        $Shipping = new \Eccube\Entity\Shipping();
        $Shipping->setDelFlg(0);
        $Order->addShipping($Shipping);
        $Shipping->setOrder($Order);
        return $Order;
    }

    /**
     * フォームからの入直内容に基づいて、受注情報の再計算を行う
     *
     * TODO 新規登録時の税金マスタ取得.
     *
     * @param $app
     * @param $Order
     */
    protected function calculate($app, $Order)
    {
        $tax = 0;
        $subtotal = 0;

        // 受注明細データの税・小計を再計算
        $OrderDetails = $Order->getOrderDetails();
        foreach ($OrderDetails as $OrderDetail) {
            // 税
            $tax += $app['eccube.service.tax_rule']
                ->calcTax($OrderDetail->getPrice(), $OrderDetail->getTaxRate(), $OrderDetail->getTaxRule());
            $OrderDetail->setPriceIncTax($OrderDetail->getPrice() + $tax);

            // 小計
            $subtotal += $OrderDetail->getTotalPrice();
        }

        // 受注データの税・小計・合計を再計算
        $Order->setTax($tax);
        $Order->setSubtotal($subtotal);
        $Order->setTotal($subtotal + $Order->getCharge() + $Order->getDeliveryFeeTotal() - $Order->getDiscount());
        // お支払い合計は、totalと同一金額(2系ではtotal - point)
        $Order->setPaymentTotal($Order->getTotal());

        // お支払い方法の更新
        $Order->setPaymentMethod($Order->getPayment()->getMethod());

        // お届け先の更新
        $Shippings = $Order->getShippings();
        foreach ($Shippings as $Shipping) {
            $Shipping->setShippingDeliveryName($Shipping->getDelivery()->getName());
            $Shipping->setShippingDeliveryTime($Shipping->getDeliveryTime()->getDeliveryTime());
        }
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
     * TODO 受注ステータスの定数化.
     *
     * @param $TargetOrder
     * @param $OriginOrder
     */
    protected function updateDate($TargetOrder, $OriginOrder)
    {
        $dateTime = new \DateTime();

        // 編集
        if ($TargetOrder->getId()) {
            // 発送済
            if ($TargetOrder->getOrderStatus()->getId() == 5) {
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
            } elseif ($TargetOrder->getOrderStatus()->getId() == 6) {
                // 編集前と異なる場合のみ更新
                if ($TargetOrder->getOrderStatus()->getId() != $OriginOrder->getOrderStatus()->getId()) {
                    $TargetOrder->setPaymentDate($dateTime);
                }
            }
            // 新規
        } else {
            // 発送済
            if ($TargetOrder->getOrderStatus()->getId() == 5) {
                $TargetOrder->setCommitDate($dateTime);
                // お届け先情報の発送日も更新する.
                $Shippings = $TargetOrder->getShippings();
                foreach ($Shippings as $Shipping) {
                    $Shipping->setShippingCommitDate($dateTime);
                }
                // 入金済
            } elseif ($TargetOrder->getOrderStatus()->getId() == 6) {
                $TargetOrder->setPaymentDate($dateTime);
            }
            // 受注日時
            $TargetOrder->setOrderDate($dateTime);
        }
    }
}
