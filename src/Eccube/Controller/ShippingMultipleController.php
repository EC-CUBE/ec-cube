<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Controller;


use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\ShippingMultipleType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;

class ShippingMultipleController extends AbstractShoppingController
{

    /**
     * 複数配送処理
     */
    public function index(Application $app, Request $request)
    {
        // カートチェック
        $response = $app->forward($app->path("shopping/checkToCart"));
        if ($response->isRedirection() || $response->getContent()) {
            return $response;
        }

        /** @var \Eccube\Entity\Order $Order */
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            log_info('購入処理中の受注情報がないため購入エラー');
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

        // 処理しやすいようにすべてのShippingItemをまとめる
        $ShipmentItems = array();
        foreach ($Order->getShippings() as $Shipping) {
            foreach ($Shipping->getProductOrderItems() as $ShipmentItem) {
                $ShipmentItems[] = $ShipmentItem;
            }
        }

        // Orderに含まれる商品ごとの数量を求める
        $ItemQuantitiesByClassId = array();
        foreach ($ShipmentItems as $item) {
            $itemId = $item->getProductClass()->getId();
            $quantity = $item->getQuantity();
            if (array_key_exists($itemId, $ItemQuantitiesByClassId)) {
                $ItemQuantitiesByClassId[$itemId] += $quantity;
            } else {
                $ItemQuantitiesByClassId[$itemId] = $quantity;
            }
        }

        // FormBuilder用に商品ごとにShippingItemをまとめる
        $ShipmentItemsForFormBuilder = array();
        $tmpAddedClassIds = array();
        foreach ($ShipmentItems as $item) {
            $itemId = $item->getProductClass()->getId();
            if (!in_array($itemId, $tmpAddedClassIds)) {
                $ShipmentItemsForFormBuilder[] = $item;
                $tmpAddedClassIds[] = $itemId;
            }
        }

        // Form生成
        $builder = $app->form();
        $builder
            ->add('shipping_multiple', CollectionType::class, array(
                'entry_type' => ShippingMultipleType::class,
                'data' => $ShipmentItemsForFormBuilder,
                'allow_add' => true,
                'allow_delete' => true,
            ));
        // Event
        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        $errors = array();
        if ($form->isSubmitted() && $form->isValid()) {

            log_info('複数配送設定処理開始', array($Order->getId()));

            $data = $form['shipping_multiple'];

            // フォームの入力から、送り先ごとに商品の数量を集計する
            $arrShipmentItemTemp = array();
            foreach ($data as $mulitples) {
                $ShipmentItem = $mulitples->getData();
                foreach ($mulitples as $items) {
                    foreach ($items as $item) {
                        $cusAddId = $this->getCustomerAddressId($item['customer_address']->getData());
                        $itemId = $ShipmentItem->getProductClass()->getId();
                        $quantity = $item['quantity']->getData();

                        if (isset($arrShipmentItemTemp[$cusAddId]) && array_key_exists($itemId, $arrShipmentItemTemp[$cusAddId])) {
                            $arrShipmentItemTemp[$cusAddId][$itemId] = $arrShipmentItemTemp[$cusAddId][$itemId] + $quantity;
                        } else {
                            $arrShipmentItemTemp[$cusAddId][$itemId] = $quantity;
                        }
                    }
                }
            }

            // フォームの入力から、商品ごとの数量を集計する
            $itemQuantities = array();
            foreach ($arrShipmentItemTemp as $FormItemByAddress) {
                foreach ($FormItemByAddress as $itemId => $quantity) {
                    if (array_key_exists($itemId, $itemQuantities)) {
                        $itemQuantities[$itemId] = $itemQuantities[$itemId] + $quantity;
                    } else {
                        $itemQuantities[$itemId] = $quantity;
                    }
                }
            }

            // 「Orderに含まれる商品ごとの数量」と「フォームに入力された商品ごとの数量」が一致しているかの確認
            // 数量が異なっているならエラーを表示する
            foreach ($ItemQuantitiesByClassId as $key => $value) {
                if (array_key_exists($key, $itemQuantities)) {
                    if ($itemQuantities[$key] != $value) {
                        $errors[] = array('message' => $app->trans('shopping.multiple.quantity.diff'));

                        // 対象がなければエラー
                        log_info('複数配送設定入力チェックエラー', array($Order->getId()));
                        return $app->render('Shopping/shipping_multiple.twig', array(
                            'form' => $form->createView(),
                            'shipmentItems' => $ShipmentItemsForFormBuilder,
                            'compItemQuantities' => $ItemQuantitiesByClassId,
                            'errors' => $errors,
                        ));
                    }
                }
            }

            // -- ここから先がお届け先を再生成する処理 --

            // お届け先情報をすべて削除
            foreach ($Order->getShippings() as $Shipping) {
                $app['orm.em']->remove($Shipping);
            }

            // お届け先のリストを作成する
            $ShippingList = array();
            foreach ($data as $mulitples) {
                $ShipmentItem = $mulitples->getData();
                $ProductClass = $ShipmentItem->getProductClass();
                $Delivery = $ShipmentItem->getShipping()->getDelivery();
                $productTypeId = $ProductClass->getProductType()->getId();

                foreach ($mulitples as $items) {
                    foreach ($items as $item) {
                        $CustomerAddress = $this->getCustomerAddress($app, $item['customer_address']->getData());
                        $cusAddId = $this->getCustomerAddressId($item['customer_address']->getData());

                        $Shipping = new Shipping();
                        $Shipping
                            ->setFromCustomerAddress($CustomerAddress)
                            ->setDelivery($Delivery)
                            ->setDelFlg(Constant::DISABLED);

                        $ShippingList[$cusAddId][$productTypeId] = $Shipping;
                    }
                }
            }
            // お届け先のリストを保存
            foreach ($ShippingList as $ShippingListByAddress) {
                foreach ($ShippingListByAddress as $Shipping) {
                    $app['orm.em']->persist($Shipping);
                }
            }

            $ProductOrderType = $app['eccube.repository.master.order_item_type']->find(OrderItemType::PRODUCT);

            // お届け先に、配送商品の情報(ShipmentItem)を関連付ける
            foreach ($data as $mulitples) {
                $ShipmentItem = $mulitples->getData();
                $ProductClass = $ShipmentItem->getProductClass();
                $Product = $ShipmentItem->getProduct();
                $productTypeId = $ProductClass->getProductType()->getId();
                $productClassId = $ProductClass->getId();

                foreach ($mulitples as $items) {
                    foreach ($items as $item) {
                        $cusAddId = $this->getCustomerAddressId($item['customer_address']->getData());

                        // お届け先から商品の数量を取得
                        $quantity = 0;
                        if (isset($arrShipmentItemTemp[$cusAddId]) && array_key_exists($productClassId, $arrShipmentItemTemp[$cusAddId])) {
                            $quantity = $arrShipmentItemTemp[$cusAddId][$productClassId];
                            unset($arrShipmentItemTemp[$cusAddId][$productClassId]);
                        } else {
                            // この配送先には送る商品がないのでスキップ（通常ありえない）
                            continue;
                        }

                        // 関連付けるお届け先のインスタンスを取得
                        $Shipping = $ShippingList[$cusAddId][$productTypeId];

                        // インスタンスを生成して保存
                        $ShipmentItem = new ShipmentItem();
                        $ShipmentItem->setShipping($Shipping)
                            ->setOrder($Order)
                            ->setProductClass($ProductClass)
                            ->setProduct($Product)
                            ->setProductName($Product->getName())
                            ->setProductCode($ProductClass->getCode())
                            ->setPrice($ProductClass->getPrice02())
                            ->setQuantity($quantity)
                            ->setOrderItemType($ProductOrderType);

                        $ClassCategory1 = $ProductClass->getClassCategory1();
                        if (!is_null($ClassCategory1)) {
                            $ShipmentItem->setClasscategoryName1($ClassCategory1->getName());
                            $ShipmentItem->setClassName1($ClassCategory1->getClassName()->getName());
                        }
                        $ClassCategory2 = $ProductClass->getClassCategory2();
                        if (!is_null($ClassCategory2)) {
                            $ShipmentItem->setClasscategoryName2($ClassCategory2->getName());
                            $ShipmentItem->setClassName2($ClassCategory2->getClassName()->getName());
                        }
                        $Shipping->addShipmentItem($ShipmentItem);
                        $app['orm.em']->persist($ShipmentItem);
                    }
                }
            }

            // 送料を計算（お届け先ごと）
            foreach ($ShippingList as $data) {
                // data is product type => shipping
                foreach ($data as $Shipping) {
                    // 配送料金の設定
                    $app['eccube.service.shopping']->setShippingDeliveryFee($Shipping);
                }
            }

            // 合計金額の再計算
            $flowResult = $this->executePurchaseFlow($app, $Order);
            if ($flowResult->hasWarning() || $flowResult->hasError()) {
                return $app->redirect($app->url('shopping_error'));
            }

            // 配送先を更新
            $app['orm.em']->flush();

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_COMPLETE, $event);

            log_info('複数配送設定処理完了', array($Order->getId()));
            return $app->redirect($app->url('shopping'));
        }

        return $app->render('Shopping/shipping_multiple.twig', array(
            'form' => $form->createView(),
            'shipmentItems' => $ShipmentItemsForFormBuilder,
            'compItemQuantities' => $ItemQuantitiesByClassId,
            'errors' => $errors,
        ));
    }

    /**
     * フォームの情報からお届け先のインデックスを返す
     * @param mixed $CustomerAddressData
     * @return int
     */
    private function getCustomerAddressId($CustomerAddressData)
    {
        if ($CustomerAddressData instanceof CustomerAddress) {
            return $CustomerAddressData->getId();
        } else {
            return $CustomerAddressData;
        }
    }

    /**
     * フォームの情報からお届け先のインスタンスを返す
     *
     * @param Application $app
     * @param mixed $CustomerAddressData
     * @return CustomerAddress
     */
    private function getCustomerAddress(Application $app, $CustomerAddressData)
    {
        if ($CustomerAddressData instanceof CustomerAddress) {
            return $CustomerAddressData;
        } else {
            $cusAddId = $CustomerAddressData;
            $customerAddresses = $app['session']->get($this->sessionCustomerAddressKey);
            $customerAddresses = unserialize($customerAddresses);

            $CustomerAddress = $customerAddresses[$cusAddId];
            $pref = $app['eccube.repository.master.pref']->find($CustomerAddress->getPref()->getId());
            $CustomerAddress->setPref($pref);

            return $CustomerAddress;
        }
    }
}