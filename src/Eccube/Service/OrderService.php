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

namespace Eccube\Service;

use Doctrine\DBAL\LockMode;
use Eccube\Application;
use Eccube\Common\Constant;


class OrderService
{
    /** @var \Eccube\Application */
    public $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function newOrder()
    {
        $Order = new \Eccube\Entity\Order();
        $Order->setDiscount(0)
            ->setSubtotal(0)
            ->setTotal(0)
            ->setPaymentTotal(0)
            ->setCharge(0)
            ->setTax(0)
            ->setOrderStatus($this->app['eccube.repository.order_status']->find($this->app['config']['order_processing']))
            ->setDelFlg(Constant::DISABLED);

        return $Order;
    }

    public function newOrderDetail($Product, $ProductClass, $quantity)
    {
        $OrderDetail = new \Eccube\Entity\OrderDetail();
        $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule($Product, $ProductClass);
        $OrderDetail->setProduct($Product)
            ->setProductClass($ProductClass)
            ->setProductName($Product->getName())
            ->setProductCode($ProductClass->getCode())
            ->setPrice($ProductClass->getPrice02())
            ->setQuantity($quantity)
            ->setTaxRule($TaxRule->getCalcRule()->getId())
            ->setTaxRate($TaxRule->getTaxRate());

        $ClassCategory1 = $ProductClass->getClassCategory1();
        if (!is_null($ClassCategory1)) {
            $OrderDetail->setClasscategoryName1($ClassCategory1->getName());
            $OrderDetail->setClassName1($ClassCategory1->getClassName()->getName());
        }
        $ClassCategory2 = $ProductClass->getClassCategory2();
        if (!is_null($ClassCategory2)) {
            $OrderDetail->setClasscategoryName2($ClassCategory2->getName());
            $OrderDetail->setClassName2($ClassCategory2->getClassName()->getName());
        }

        return $OrderDetail;
    }

     public function copyToOrderFromCustomer(\Eccube\Entity\Order $Order, \Eccube\Entity\Customer $Customer = null)
     {
        if (is_null($Customer)) {
            return $Order;
        }

        if ($Customer->getId()) {
            $Order->setCustomer($Customer);
        }
        $Order
            ->setName01($Customer->getName01())
            ->setName02($Customer->getName02())
            ->setKana01($Customer->getKana01())
            ->setKana02($Customer->getKana02())
            ->setCompanyName($Customer->getCompanyName())
            ->setEmail($Customer->getEmail())
            ->setTel01($Customer->getTel01())
            ->setTel02($Customer->getTel02())
            ->setTel03($Customer->getTel03())
            ->setFax01($Customer->getFax01())
            ->setFax02($Customer->getFax02())
            ->setFax03($Customer->getFax03())
            ->setZip01($Customer->getZip01())
            ->setZip02($Customer->getZip02())
            ->setZipCode($Customer->getZip01() . $Customer->getZip02())
            ->setPref($Customer->getPref())
            ->setAddr01($Customer->getAddr01())
            ->setAddr02($Customer->getAddr02())
            ->setSex($Customer->getSex())
            ->setBirth($Customer->getBirth())
            ->setJob($Customer->getJob());

        return $Order;
     }

    public function copyToShippingFromCustomer(\Eccube\Entity\Shipping $Shipping, \Eccube\Entity\Customer $Customer = null)
    {
        if (is_null($Customer)) {
            return $Shipping;
        }
        $Shipping
            ->setName01($Customer->getName01())
            ->setName02($Customer->getName02())
            ->setKana01($Customer->getKana01())
            ->setKana02($Customer->getKana02())
            ->setCompanyName($Customer->getCompanyName())
            ->setTel01($Customer->getTel01())
            ->setTel02($Customer->getTel02())
            ->setTel03($Customer->getTel03())
            ->setFax01($Customer->getFax01())
            ->setFax02($Customer->getFax02())
            ->setFax03($Customer->getFax03())
            ->setZip01($Customer->getZip01())
            ->setZip02($Customer->getZip02())
            ->setZipCode($Customer->getZip01() . $Customer->getZip02())
            ->setPref($Customer->getPref())
            ->setAddr01($Customer->getAddr01())
            ->setAddr02($Customer->getAddr02());

        return $Shipping;
    }

    public function registerPreOrderFromCartItems($cartItems, \Eccube\Entity\Customer $Customer = null, $preOrderId)
    {

        $em = $this->app['orm.em'];
        // 受注
        $Order = $this->newOrder();
        $this->copyToOrderFromCustomer($Order, $Customer);
        $Order->setPreOrderId($preOrderId);

        $em->persist($Order);

        // 配送先
        $Shipping = new \Eccube\Entity\Shipping();

        $this->copyToShippingFromCustomer($Shipping, $Customer)
            ->setOrder($Order)
            ->setDelFlg(Constant::DISABLED);
        $em->persist($Shipping);

        $Order->addShipping($Shipping);

        $subTotal = 0;
        $tax = 0;
        $totalQuantity = 0;
        $productTypes = array();
        $productDeliveryFeeTotal = 0;

        $baseInfo = $this->app['eccube.repository.base_info']->get();
        $optionProductDeliveryFee = $baseInfo->getOptionProductDeliveryFee();

        // 受注詳細, 配送商品
        foreach ($cartItems as $item) {
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $item->getObject();
            /* @var $Product \Eccube\Entity\Product */
            $Product = $ProductClass->getProduct();

            $quantity = $item->getQuantity();
            $productTypes[] = $ProductClass->getProductType();
            $totalQuantity += $quantity;
            if (!is_null($optionProductDeliveryFee)) {
                // 商品ごとの配送料が設定
                if (!is_null($ProductClass->getDeliveryFee())) {
                    $productDeliveryFeeTotal += $ProductClass->getDeliveryFee() * $quantity;
                }
            }

            // 受注詳細
            $OrderDetail = $this->newOrderDetail($Product, $ProductClass, $quantity);
            $OrderDetail->setOrder($Order);

            $Order->addOrderDetail($OrderDetail);
            $em->persist($OrderDetail);

            // 小計
            $subTotal += $ProductClass->getPrice02IncTax() * $quantity;
            // 消費税のみの小計
            $tax += ($OrderDetail->getPriceIncTax() - $OrderDetail->getPrice()) * $quantity;

            // 配送商品
            $ShipmentItem = new \Eccube\Entity\ShipmentItem();
            $ShipmentItem->setShipping($Shipping)
                ->setOrder($Order)
                ->setProductClass($ProductClass)
                ->setProduct($Product)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($quantity);

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
            $em->persist($ShipmentItem);
        }

        // 初期選択の配送業者をセット
        $qb = $em->createQueryBuilder();
        $delivery = $qb->select("d")
            ->from("\Eccube\Entity\Delivery", "d")
            ->where($qb->expr()->in('d.ProductType', ':productTypes'))
            ->setParameter('productTypes', $productTypes)
            ->andWhere("d.del_flg = :delFlg")
            ->setParameter('delFlg', Constant::DISABLED)
            ->orderBy("d.rank", "ASC")
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        // 配送料金の設定
        $deliveryFee = $this->app['eccube.repository.delivery_fee']->findOneBy(array('Delivery' => $delivery, 'Pref' => $Shipping->getPref()));
        $Shipping->setDelivery($delivery);
        // $Shipping->setDeliveryFee($deliveryFee + $productDeliveryFeeTotal);
        $Shipping->setDeliveryFee($deliveryFee);
        $Shipping->setShippingDeliveryFee($deliveryFee->getFee() + $productDeliveryFeeTotal);

        $Order->setDeliveryFeeTotal($deliveryFee->getFee() + $productDeliveryFeeTotal);
        // 配送料無料条件(合計金額)
        $deliveryFreeAmount = $baseInfo->getDeliveryFreeAmount();
        if (!is_null($deliveryFreeAmount)) {
            // 合計金額が設定金額以上であれば送料無料
            if ($subTotal >= $deliveryFreeAmount) {
                $Order->setDeliveryFeeTotal(0);
            }
        }

        // 配送料無料条件(合計数量)
        $deliveryFreeQuantity = $baseInfo->getDeliveryFreeQuantity();
        if (!is_null($deliveryFreeQuantity)) {
            // 合計数量が設定数量以上であれば送料無料
            if ($totalQuantity >= $deliveryFreeQuantity) {
                $Order->setDeliveryFeeTotal(0);
            }
        }

        // 初期選択の支払い方法をセット
        $paymentOptions = $delivery->getPaymentOptions();
        $payments = $this->getPayments($paymentOptions, $subTotal);

        if (count($payments) > 0) {
            $payment = $payments[0];
            $Order->setPayment($payment);
            $Order->setPaymentMethod($payment->getMethod());
            $Order->setCharge($payment->getCharge());
        } else {
            $Order->setCharge(0);
        }

        $Order->setTax($tax);

        $total = $subTotal + $Order->getCharge() + $Order->getDeliveryFeeTotal();

        $Order->setTotal($total);
        $Order->setSubTotal($subTotal);
        $Order->setPaymentTotal($total);

        $em->flush();

        return $Order;
    }



    /**
     * 住所などの情報が変更された時に金額の再計算を行う
     */
    public function getAmount(\Eccube\Entity\Order $Order, \Eccube\Entity\Cart $Cart)
    {

        // 初期選択の配送業者をセット
        $shippings = $Order->getShippings();
        $delivery = $shippings[0]->getDelivery();

        $deliveryFee = $this->app['eccube.repository.delivery_fee']->findOneBy(array('Delivery' => $delivery, 'Pref' => $shippings[0]->getPref()));

        // 配送料金の設定
        $payment = $Order->getPayment();

        if (!is_null($payment)) {
            $Order->setPayment($payment);
            $Order->setPaymentMethod($payment->getMethod());
            $Order->setCharge($payment->getCharge());
        }
        $Order->setDeliveryFeeTotal($deliveryFee->getFee());

        $baseInfo = $this->app['eccube.repository.base_info']->get();
        // 配送料無料条件(合計金額)
        $deliveryFreeAmount = $baseInfo->getDeliveryFreeAmount();
        if (!is_null($deliveryFreeAmount)) {
            // 合計金額が設定金額以上であれば送料無料
            if ($Order->getSubTotal() >= $deliveryFreeAmount) {
                $Order->setDeliveryFeeTotal(0);
            }
        }

        // 配送料無料条件(合計数量)
        $deliveryFreeQuantity = $baseInfo->getDeliveryFreeQuantity();
        if (!is_null($deliveryFreeQuantity)) {
            // 合計数量が設定数量以上であれば送料無料
            if ($Cart->getTotalQuantity() >= $deliveryFreeQuantity) {
                $Order->setDeliveryFeeTotal(0);
            }
        }

        $total = $Order->getSubTotal()  + $Order->getCharge() + $Order->getDeliveryFeeTotal();

        $Order->setTotal($total);
        $Order->setPaymentTotal($total);
        $this->app['orm.em']->flush();

        return $Order;

    }


    /**
     * 商品公開ステータスチェック、在庫チェック、購入制限数チェックを行い、在庫情報をロックする
     *
     * @param $em トランザクション制御されているEntityManager
     * @param $Order 受注情報
     * @return true : 成功、 false : 失敗
     */
    public function isOrderProduct($em, \Eccube\Entity\Order $Order)
    {
        // 商品公開ステータスチェック
        $orderDetails = $Order->getOrderDetails();

        foreach ($orderDetails as $orderDetail) {
            if ($orderDetail->getProduct()->getStatus()->getId() != \Eccube\Entity\Master\Disp::DISPLAY_SHOW) {
                // 商品が非公開ならエラー
                return false;
            }

            // 購入制限数チェック
            if (!is_null($orderDetail->getProductClass()->getSaleLimit())) {
                if ($orderDetail->getQuantity() > $orderDetail->getProductClass()->getSaleLimit()) {
                    return false;
                }
            }

        }

        // 在庫チェック
        foreach ($orderDetails as $orderDetail) {
            // 在庫が無制限かチェックし、制限ありなら在庫数をチェック
            if ($orderDetail->getProductClass()->getStockUnlimited() == Constant::DISABLED) {
                // 在庫チェックあり
                // 在庫に対してロック(select ... for update)を実行
                $productStock = $em->getRepository('Eccube\Entity\ProductStock')->find(
                        $orderDetail->getProductClass()->getProductStock()->getId(), LockMode::PESSIMISTIC_WRITE
                );
                // 購入数量と在庫数をチェックして在庫がなければエラー
                if ($orderDetail->getQuantity() > $productStock->getStock()) {
                    return false;
                }
            }
        }

        return true;
 
    }

    /**
     * 受注情報、お届け先情報の更新
     *
     * @param $em トランザクション制御されているEntityManager
     * @param $Order 受注情報
     * @param $formData フォームデータ
     */
    public function setOrderUpdate($em, \Eccube\Entity\Order $Order, $formData)
    {

        // 受注情報を更新
        $Order->setOrderDate(new \DateTime());
        $Order->setOrderStatus($this->app['eccube.repository.order_status']->find($this->app['config']['order_new']));
        $Order->setMessage($formData['message']);

        // お届け先情報を更新
        $shippings = $Order->getShippings();
        foreach ($shippings as $shipping) {
            $shipping->setShippingDeliveryName($formData['delivery']->getName());
            if (!empty($formData['deliveryTime'])) {
                $shipping->setShippingDeliveryTime($formData['deliveryTime']->getDeliveryTime());
            }
            if (!empty($formData['deliveryDate'])) {
                $shipping->setShippingDeliveryDate(new \DateTime($formData['deliveryDate']));
            }
            $shipping->setShippingDeliveryFee($shipping->getDeliveryFee()->getFee());
        }

    }


    /**
     * 在庫情報の更新
     *
     * @param $em トランザクション制御されているEntityManager
     * @param $Order 受注情報
     */
    public function setStockUpdate($em, \Eccube\Entity\Order $Order)
    {

        $orderDetails = $Order->getOrderDetails();

        // 在庫情報更新
        foreach ($orderDetails as $orderDetail) {
            // 在庫が無制限かチェックし、制限ありなら在庫数を更新
            if ($orderDetail->getProductClass()->getStockUnlimited() == Constant::DISABLED) {

                $productStock = $em->getRepository('Eccube\Entity\ProductStock')->find(
                        $orderDetail->getProductClass()->getProductStock()->getId()
                );

                // 在庫情報の在庫数を更新
                $stock = $productStock->getStock() - $orderDetail->getQuantity();
                $productStock->setStock($stock);

                // 商品規格情報の在庫数を更新
                $orderDetail->getProductClass()->setStock($stock);

            }
        }

    }


    /**
     * 会員情報の更新
     *
     * @param $em トランザクション制御されているEntityManager
     * @param $Order 受注情報
     * @param $user ログインユーザ
     */
    public function setCustomerUpdate($em, \Eccube\Entity\Order $Order, \Eccube\Entity\Customer $user)
    {

        $orderDetails = $Order->getOrderDetails();

        // 顧客情報を更新
        $now = new \DateTime();
        $firstBuyDate = $user->getFirstBuyDate();
        if (empty($firstBuyDate)) {
            $user->setFirstBuyDate($now);
        }
        $user->setLastBuyDate($now);

        $user->setBuyTimes($user->getBuyTimes() + 1);
        $user->setBuyTotal($user->getBuyTotal() + $Order->getTotal());

    }


    /**
     * 支払方法選択の表示設定
     *
     * @param $paymentOptions 支払選択肢情報
     * @param $subTotal 小計
     */
    public function getPayments($paymentOptions, $subTotal)
    {
        $payments = array();
        foreach ($paymentOptions as $paymentOption) {
            $payment = $paymentOption->getPayment();
            // 支払方法の制限値内であれば表示
            if (intval($payment->getRuleMin()) <= $subTotal) {
                if (is_null($payment->getRuleMax()) || $payment->getRuleMax() >= $subTotal) {
                    $payments[] = $payment;
                }
            }
        }

        return $payments;

    }


}
