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

use Eccube\Application;

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
            ->setDelFlg($this->app['config']['disabled']);

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
        }
        $ClassCategory2 = $ProductClass->getClassCategory2();
        if (!is_null($ClassCategory2)) {
            $OrderDetail->setClasscategoryName2($ClassCategory2->getName());
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
            ->setDelFlg($this->app['config']['disabled']);
        $em->persist($Shipping);

        $Order->addShipping($Shipping);

        $subTotal = 0;
        $tax = 0;
        $productTypes = array();

        // 受注詳細, 配送商品
        foreach ($cartItems as $item) {
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $item->getObject();
            /* @var $Product \Eccube\Entity\Product */
            $Product = $ProductClass->getProduct();

            $quantity = $item->getQuantity();
            $productTypes[] = $ProductClass->getProductType();

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
            }
            $ClassCategory2 = $ProductClass->getClassCategory2();
            if (!is_null($ClassCategory2)) {
                $ShipmentItem->setClasscategoryName2($ClassCategory2->getName());
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
            ->setParameter('delFlg', $this->app['config']['disabled'])
            ->orderBy("d.rank", "ASC")
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        // 配送料金の設定
        $deliveryFee = $this->app['eccube.repository.delivery_fee']->findOneBy(array('Delivery' => $delivery, 'Pref' => $Shipping->getPref()));
        $Shipping->setDelivery($delivery);
        $Shipping->setDeliveryFee($deliveryFee);

        // 初期選択の支払い方法をセット
        $paymentOptions = $delivery->getPaymentOptions();
        $payment = $paymentOptions[0]->getPayment();

        $Order->setTax($tax);
        $Order->setPayment($payment);
        $Order->setPaymentMethod($payment->getMethod());
        $Order->setCharge($payment->getCharge());
        $Order->setDeliveryFeeTotal($deliveryFee->getFee());

        $total = $subTotal + $Order->getCharge() + $Order->getDeliveryFeeTotal();

        $Order->setTotal($total);
        $Order->setSubTotal($subTotal);
        $Order->setPaymentTotal($total);
        $em->flush();

        return $Order;
    }

    public function commit(\Eccube\Entity\Order $Order)
    {
        // todo delFlagではなく確定フラグにする
        $Order->setDelFlg(0);

        // todo 在庫引当
        // todo ポイント引当
        $this->app['orm.em']->persist($Order);
        $this->app['orm.em']->flush();
    }
}
