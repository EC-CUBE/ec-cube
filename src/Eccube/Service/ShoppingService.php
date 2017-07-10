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
use Eccube\Entity\Customer;
use Eccube\Entity\Delivery;
use Eccube\Entity\MailHistory;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Exception\ShoppingException;
use Eccube\Form\Type\ShippingItemType;
use Eccube\Util\Str;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class ShoppingService
{
    /** @var \Eccube\Application */
    public $app;

    /** @var \Eccube\Service\CartService */
    protected $cartService;

    /** @var \Eccube\Service\OrderService */
    protected $orderService;

    /** @var \Eccube\Entity\BaseInfo */
    protected $BaseInfo;

    /** @var  \Doctrine\ORM\EntityManager */
    protected $em;

    public function __construct(Application $app, $cartService, $orderService)
    {
        $this->app = $app;
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->BaseInfo = $app['eccube.repository.base_info']->get();
    }

    /**
     * セッションにセットされた受注情報を取得
     *
     * @param null $status
     * @return null|object
     */
    public function getOrder($status = null)
    {

        // 受注データを取得
        $preOrderId = $this->cartService->getPreOrderId();
        if (!$preOrderId) {
            return null;
        }

        $condition = array(
            'pre_order_id' => $preOrderId,
        );

        if (!is_null($status)) {
            $condition += array(
                'OrderStatus' => $status,
            );
        }

        $Order = $this->app['eccube.repository.order']->findOneBy($condition);

        return $Order;

    }

    /**
     * 非会員情報を取得
     *
     * @param $sesisonKey
     * @return $Customer|null
     */
    public function getNonMember($sesisonKey)
    {

        // 非会員でも一度会員登録されていればショッピング画面へ遷移
        $nonMember = $this->app['session']->get($sesisonKey);
        if (is_null($nonMember)) {
            return null;
        }
        if (!array_key_exists('customer', $nonMember) || !array_key_exists('pref', $nonMember)) {
            return null;
        }

        $Customer = $nonMember['customer'];
        $Customer->setPref($this->app['eccube.repository.master.pref']->find($nonMember['pref']));

        foreach ($Customer->getCustomerAddresses() as $CustomerAddress) {
            $Pref = $CustomerAddress->getPref();
            if ($Pref) {
                $CustomerAddress->setPref($this->app['eccube.repository.master.pref']->find($Pref->getId()));
            }
        }

        return $Customer;

    }

    /**
     * 受注情報を作成
     *
     * @param $Customer
     * @return \Eccube\Entity\Order
     */
    public function createOrder($Customer)
    {
        // ランダムなpre_order_idを作成
        do {
            $preOrderId = sha1(Str::random(32));
            $Order = $this->app['eccube.repository.order']->findOneBy(array(
                'pre_order_id' => $preOrderId,
                'OrderStatus' => $this->app['config']['order_processing'],
            ));
        } while ($Order);

        // 受注情報、受注明細情報、お届け先情報、配送商品情報を作成
        $Order = $this->registerPreOrder(
            $Customer,
            $preOrderId);

        $this->cartService->setPreOrderId($preOrderId);
        $this->cartService->save();

        return $Order;
    }

    /**
     * 仮受注情報作成
     *
     * @param $Customer
     * @param $preOrderId
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function registerPreOrder(Customer $Customer, $preOrderId)
    {

        $this->em = $this->app['orm.em'];

        // 受注情報を作成
        $Order = $this->getNewOrder($Customer);
        $Order->setPreOrderId($preOrderId);

        $DeviceType = $this->app['eccube.repository.master.device_type']->find($this->app['mobile_detect.device_type']);
        $Order->setDeviceType($DeviceType);

        $this->em->persist($Order);

        // 配送業者情報を取得
        $deliveries = $this->getDeliveriesCart();

        // お届け先情報を作成
        $Order = $this->getNewShipping($Order, $Customer, $deliveries);

        // 受注明細情報、配送商品情報を作成
        $Order = $this->getNewDetails($Order);

        // 小計
        $subTotal = $this->orderService->getSubTotal($Order);

        // 消費税のみの小計
        $tax = $this->orderService->getTotalTax($Order);

        // 配送料合計金額
        // TODO CalculateDeliveryFeeStrategy でセットする
        // $Order->setDeliveryFeeTotal($this->getShippingDeliveryFeeTotal($Order->getShippings()));

        // 小計
        $Order->setSubTotal($subTotal);

        // 配送料無料条件(合計金額)
        $this->setDeliveryFreeAmount($Order);

        // 配送料無料条件(合計数量)
        $this->setDeliveryFreeQuantity($Order);

        // 初期選択の支払い方法をセット
        $payments = $this->app['eccube.repository.payment']->findAllowedPayments($deliveries);
        $payments = $this->getPayments($payments, $subTotal);

        if (count($payments) > 0) {
            $payment = $payments[0];
            $Order->setPayment($payment);
            $Order->setPaymentMethod($payment->getMethod());
            $Order->setCharge($payment->getCharge());
        } else {
            $Order->setCharge(0);
        }

        $Order->setTax($tax);

        // 合計金額の計算
        $this->calculatePrice($Order);

        $this->em->flush();

        return $Order;

    }

    /**
     * 受注情報を作成
     *
     * @param $Customer
     * @return \Eccube\Entity\Order
     */
    public function getNewOrder(Customer $Customer)
    {
        $Order = $this->newOrder();
        $this->copyToOrderFromCustomer($Order, $Customer);

        return $Order;
    }


    /**
     * 受注情報を作成
     *
     * @return \Eccube\Entity\Order
     */
    public function newOrder()
    {
        $OrderStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_processing']);
        $Order = new \Eccube\Entity\Order($OrderStatus);

        return $Order;
    }

    /**
     * 受注情報を作成
     *
     * @param \Eccube\Entity\Order $Order
     * @param \Eccube\Entity\Customer|null $Customer
     * @return \Eccube\Entity\Order
     */
    public function copyToOrderFromCustomer(Order $Order, Customer $Customer = null)
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
            ->setZipCode($Customer->getZip01().$Customer->getZip02())
            ->setPref($Customer->getPref())
            ->setAddr01($Customer->getAddr01())
            ->setAddr02($Customer->getAddr02())
            ->setSex($Customer->getSex())
            ->setBirth($Customer->getBirth())
            ->setJob($Customer->getJob());

        return $Order;
    }


    /**
     * 配送業者情報を取得
     *
     * @return array
     */
    public function getDeliveriesCart()
    {

        // カートに保持されている商品種別を取得
        $productTypes = $this->cartService->getProductTypes();

        return $this->getDeliveries($productTypes);

    }

    /**
     * 配送業者情報を取得
     *
     * @param Order $Order
     * @return array
     */
    public function getDeliveriesOrder(Order $Order)
    {

        // 受注情報から商品種別を取得
        $productTypes = $this->orderService->getProductTypes($Order);

        return $this->getDeliveries($productTypes);

    }

    /**
     * 配送業者情報を取得
     *
     * @param $productTypes
     * @return array
     */
    public function getDeliveries($productTypes)
    {

        // 商品種別に紐づく配送業者を取得
        $deliveries = $this->app['eccube.repository.delivery']->getDeliveries($productTypes);

        if ($this->BaseInfo->getOptionMultipleShipping() == Constant::ENABLED) {
            // 複数配送対応

            // 支払方法を取得
            $payments = $this->app['eccube.repository.payment']->findAllowedPayments($deliveries);

            if (count($productTypes) > 1) {
                // 商品種別が複数ある場合、配送対象となる配送業者を取得
                $deliveries = $this->app['eccube.repository.delivery']->findAllowedDeliveries($productTypes, $payments);
            }

        }

        return $deliveries;

    }


    /**
     * お届け先情報を作成
     *
     * @param Order $Order
     * @param Customer $Customer
     * @param $deliveries
     * @return Order
     */
    public function getNewShipping(Order $Order, Customer $Customer, $deliveries)
    {
        $productTypes = array();
        foreach ($deliveries as $Delivery) {
            if (!in_array($Delivery->getProductType()->getId(), $productTypes)) {
                $Shipping = new Shipping();

                $this->copyToShippingFromCustomer($Shipping, $Customer)
                    ->setOrder($Order)
                    ->setDelFlg(Constant::DISABLED);

                // 配送料金の設定
                $this->setShippingDeliveryFee($Shipping, $Delivery);

                $this->em->persist($Shipping);

                $Order->addShipping($Shipping);

                $productTypes[] = $Delivery->getProductType()->getId();
            }
        }

        return $Order;
    }

    /**
     * お届け先情報を作成
     *
     * @param \Eccube\Entity\Shipping $Shipping
     * @param \Eccube\Entity\Customer|null $Customer
     * @return \Eccube\Entity\Shipping
     */
    public function copyToShippingFromCustomer(Shipping $Shipping, Customer $Customer = null)
    {
        if (is_null($Customer)) {
            return $Shipping;
        }

        $CustomerAddress = $this->app['eccube.repository.customer_address']->findOneBy(
            array('Customer' => $Customer),
            array('id' => 'ASC')
        );

        if (!is_null($CustomerAddress)) {
            $Shipping
                ->setName01($CustomerAddress->getName01())
                ->setName02($CustomerAddress->getName02())
                ->setKana01($CustomerAddress->getKana01())
                ->setKana02($CustomerAddress->getKana02())
                ->setCompanyName($CustomerAddress->getCompanyName())
                ->setTel01($CustomerAddress->getTel01())
                ->setTel02($CustomerAddress->getTel02())
                ->setTel03($CustomerAddress->getTel03())
                ->setFax01($CustomerAddress->getFax01())
                ->setFax02($CustomerAddress->getFax02())
                ->setFax03($CustomerAddress->getFax03())
                ->setZip01($CustomerAddress->getZip01())
                ->setZip02($CustomerAddress->getZip02())
                ->setZipCode($CustomerAddress->getZip01().$CustomerAddress->getZip02())
                ->setPref($CustomerAddress->getPref())
                ->setAddr01($CustomerAddress->getAddr01())
                ->setAddr02($CustomerAddress->getAddr02());
        } else {
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
                ->setZipCode($Customer->getZip01().$Customer->getZip02())
                ->setPref($Customer->getPref())
                ->setAddr01($Customer->getAddr01())
                ->setAddr02($Customer->getAddr02());
        }

        return $Shipping;
    }


    /**
     * 受注明細情報、配送商品情報を作成
     *
     * @param Order $Order
     * @return Order
     */
    public function getNewDetails(Order $Order)
    {

        // 受注詳細, 配送商品
        foreach ($this->cartService->getCart()->getCartItems() as $item) {
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $item->getObject();
            /* @var $Product \Eccube\Entity\Product */
            $Product = $ProductClass->getProduct();

            $quantity = $item->getQuantity();

            // 受注明細情報を作成
            $OrderDetail = $this->getNewOrderDetail($Product, $ProductClass, $quantity);
            $OrderDetail->setOrder($Order);
            $Order->addOrderDetail($OrderDetail);

            // 配送商品情報を作成
            $this->getNewShipmentItem($Order, $Product, $ProductClass, $quantity);
        }

        return $Order;

    }

    /**
     * 受注明細情報を作成
     *
     * @param Product $Product
     * @param ProductClass $ProductClass
     * @param $quantity
     * @return \Eccube\Entity\OrderDetail
     */
    public function getNewOrderDetail(Product $Product, ProductClass $ProductClass, $quantity)
    {
        $OrderDetail = new OrderDetail();
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

        $this->em->persist($OrderDetail);

        return $OrderDetail;
    }

    /**
     * 配送商品情報を作成
     *
     * @param Order $Order
     * @param Product $Product
     * @param ProductClass $ProductClass
     * @param $quantity
     * @return \Eccube\Entity\ShipmentItem
     */
    public function getNewShipmentItem(Order $Order, Product $Product, ProductClass $ProductClass, $quantity)
    {

        $ShipmentItem = new ShipmentItem();
        $shippings = $Order->getShippings();

        // 選択された商品がどのお届け先情報と関連するかチェック
        $Shipping = null;
        foreach ($shippings as $s) {
            if ($s->getDelivery()->getProductType()->getId() == $ProductClass->getProductType()->getId()) {
                // 商品種別が同一のお届け先情報と関連させる
                $Shipping = $s;
                break;
            }
        }

        if (is_null($Shipping)) {
            // お届け先情報と関連していない場合、エラー
            throw new CartException('shopping.delivery.not.producttype');
        }

        // 商品ごとの配送料合計
        $productDeliveryFeeTotal = 0;
        if (!is_null($this->BaseInfo->getOptionProductDeliveryFee())) {
            $productDeliveryFeeTotal = $ProductClass->getDeliveryFee() * $quantity;
        }

        $Shipping->setShippingDeliveryFee($Shipping->getShippingDeliveryFee() + $productDeliveryFeeTotal);

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
        $this->em->persist($ShipmentItem);

        return $ShipmentItem;

    }

    /**
     * お届け先ごとの送料合計を取得
     *
     * @param $shippings
     * @return int
     */
    public function getShippingDeliveryFeeTotal($shippings)
    {
        $deliveryFeeTotal = 0;
        foreach ($shippings as $Shipping) {
            $deliveryFeeTotal += $Shipping->getShippingDeliveryFee();
        }

        return $deliveryFeeTotal;

    }

    /**
     * 商品ごとの配送料を取得
     *
     * @param Shipping $Shipping
     * @return int
     */
    public function getProductDeliveryFee(Shipping $Shipping)
    {
        $productDeliveryFeeTotal = 0;
        $shipmentItems = $Shipping->getShipmentItems();
        foreach ($shipmentItems as $ShipmentItem) {
            $productDeliveryFeeTotal += $ShipmentItem->getProductClass()->getDeliveryFee() * $ShipmentItem->getQuantity();
        }

        return $productDeliveryFeeTotal;
    }

    /**
     * 住所などの情報が変更された時に金額の再計算を行う
     * @deprecated PurchaseFlowで行う
     * @param Order $Order
     * @return Order
     */
    public function getAmount(Order $Order)
    {

        // 初期選択の配送業者をセット
        $shippings = $Order->getShippings();

        // 配送料合計金額
        // TODO CalculateDeliveryFeeStrategy でセットする
        // $Order->setDeliveryFeeTotal($this->getShippingDeliveryFeeTotal($shippings));

        // 配送料無料条件(合計金額)
        $this->setDeliveryFreeAmount($Order);

        // 配送料無料条件(合計数量)
        $this->setDeliveryFreeQuantity($Order);

        // 合計金額の計算
        $this->calculatePrice($Order);

        return $Order;

    }

    /**
     * 配送料金の設定
     *
     * @param Shipping $Shipping
     * @param Delivery|null $Delivery
     */
    public function setShippingDeliveryFee(Shipping $Shipping, Delivery $Delivery = null)
    {
        // 配送料金の設定
        if (is_null($Delivery)) {
            $Delivery = $Shipping->getDelivery();
        }
        $deliveryFee = $this->app['eccube.repository.delivery_fee']->findOneBy(array('Delivery' => $Delivery, 'Pref' => $Shipping->getPref()));

        $Shipping->setDeliveryFee($deliveryFee);
        $Shipping->setDelivery($Delivery);

        // 商品ごとの配送料合計
        $productDeliveryFeeTotal = 0;
        if (!is_null($this->BaseInfo->getOptionProductDeliveryFee())) {
            $productDeliveryFeeTotal += $this->getProductDeliveryFee($Shipping);
        }

        $Shipping->setShippingDeliveryFee($deliveryFee->getFee() + $productDeliveryFeeTotal);
        $Shipping->setShippingDeliveryName($Delivery->getName());
    }

    /**
     * 配送料無料条件(合計金額)の条件を満たしていれば配送料金を0に設定
     *
     * @param Order $Order
     */
    public function setDeliveryFreeAmount(Order $Order)
    {
        // 配送料無料条件(合計金額)
        $deliveryFreeAmount = $this->BaseInfo->getDeliveryFreeAmount();
        if (!is_null($deliveryFreeAmount)) {
            // 合計金額が設定金額以上であれば送料無料
            if ($Order->getSubTotal() >= $deliveryFreeAmount) {
                $Order->setDeliveryFeeTotal(0);
                // お届け先情報の配送料も0にセット
                $shippings = $Order->getShippings();
                foreach ($shippings as $Shipping) {
                    $Shipping->setShippingDeliveryFee(0);
                }
            }
        }
    }

    /**
     * 配送料無料条件(合計数量)の条件を満たしていれば配送料金を0に設定
     *
     * @param Order $Order
     */
    public function setDeliveryFreeQuantity(Order $Order)
    {
        // 配送料無料条件(合計数量)
        $deliveryFreeQuantity = $this->BaseInfo->getDeliveryFreeQuantity();
        if (!is_null($deliveryFreeQuantity)) {
            // 合計数量が設定数量以上であれば送料無料
            if ($this->orderService->getTotalQuantity($Order) >= $deliveryFreeQuantity) {
                $Order->setDeliveryFeeTotal(0);
                // お届け先情報の配送料も0にセット
                $shippings = $Order->getShippings();
                foreach ($shippings as $Shipping) {
                    $Shipping->setShippingDeliveryFee(0);
                }
            }
        }
    }


    /**
     * 商品公開ステータスチェック、在庫チェック、購入制限数チェックを行い、在庫情報をロックする
     *
     * @param $em トランザクション制御されているEntityManager
     * @param Order $Order 受注情報
     * @return bool true : 成功、false : 失敗
     */
    public function isOrderProduct($em, \Eccube\Entity\Order $Order)
    {
        $orderDetails = $Order->getOrderDetails();

        foreach ($orderDetails as $orderDetail) {

            if (is_null($orderDetail->getProduct())) {
                // FIXME 配送明細を考慮する必要がある
                continue;
            }

            // 商品削除チェック
            if ($orderDetail->getProductClass()->getDelFlg()) {
                // @deprecated 3.1以降ではexceptionをthrowする
                // throw new ShoppingException('cart.product.delete');
                return false;
            }

            // 商品公開ステータスチェック
            if ($orderDetail->getProduct()->getStatus()->getId() != \Eccube\Entity\Master\Disp::DISPLAY_SHOW) {
                // 商品が非公開ならエラー

                // @deprecated 3.1以降ではexceptionをthrowする
                // throw new ShoppingException('cart.product.not.status');
                return false;
            }

            // 購入制限数チェック
            if (!is_null($orderDetail->getProductClass()->getSaleLimit())) {
                if ($orderDetail->getQuantity() > $orderDetail->getProductClass()->getSaleLimit()) {
                    // @deprecated 3.1以降ではexceptionをthrowする
                    // throw new ShoppingException('cart.over.sale_limit');
                    return false;
                }
            }

            // 購入数チェック
            if ($orderDetail->getQuantity() < 1) {
                // 購入数量が1未満ならエラー

                // @deprecated 3.1以降ではexceptionをthrowする
                // throw new ShoppingException('???');
                return false;
            }

        }

        // 在庫チェック
        foreach ($orderDetails as $orderDetail) {
            if (is_null($orderDetail->getProductClass())) {
                // FIXME 配送明細を考慮する必要がある
                continue;
            }
            // 在庫が無制限かチェックし、制限ありなら在庫数をチェック
            if ($orderDetail->getProductClass()->getStockUnlimited() == Constant::DISABLED) {
                // 在庫チェックあり
                // 在庫に対してロック(select ... for update)を実行
                $productStock = $em->getRepository('Eccube\Entity\ProductStock')->find(
                    $orderDetail->getProductClass()->getProductStock()->getId(), LockMode::PESSIMISTIC_WRITE
                );
                // 購入数量と在庫数をチェックして在庫がなければエラー
                if ($productStock->getStock() < 1) {
                    // @deprecated 3.1以降ではexceptionをthrowする
                    // throw new ShoppingException('cart.over.stock');
                    return false;
                } elseif ($orderDetail->getQuantity() > $productStock->getStock()) {
                    // @deprecated 3.1以降ではexceptionをthrowする
                    // throw new ShoppingException('cart.over.stock');
                    return false;
                }
            }
        }

        return true;

    }

    /**
     * 受注情報、お届け先情報の更新
     *
     * @param Order $Order 受注情報
     * @param $data フォームデータ
     *
     * @deprecated since 3.0.5, to be removed in 3.1
     */
    public function setOrderUpdate(Order $Order, $data)
    {
        // 受注情報を更新
        $Order->setOrderDate(new \DateTime());
        $Order->setOrderStatus($this->app['eccube.repository.order_status']->find($this->app['config']['order_new']));
        $Order->setMessage($data['message']);
        // お届け先情報を更新
        $shippings = $data['shippings'];
        foreach ($shippings as $Shipping) {
            $Delivery = $Shipping->getDelivery();
            $deliveryFee = $this->app['eccube.repository.delivery_fee']->findOneBy(array(
                'Delivery' => $Delivery,
                'Pref' => $Shipping->getPref()
            ));
            $deliveryTime = $Shipping->getDeliveryTime();
            if (!empty($deliveryTime)) {
                $Shipping->setShippingDeliveryTime($deliveryTime->getDeliveryTime());
            }
            $Shipping->setDeliveryFee($deliveryFee);
            // 商品ごとの配送料合計
            $productDeliveryFeeTotal = 0;
            if (!is_null($this->BaseInfo->getOptionProductDeliveryFee())) {
                $productDeliveryFeeTotal += $this->getProductDeliveryFee($Shipping);
            }
            $Shipping->setShippingDeliveryFee($deliveryFee->getFee() + $productDeliveryFeeTotal);
            $Shipping->setShippingDeliveryName($Delivery->getName());
        }
        // 配送料無料条件(合計金額)
        $this->setDeliveryFreeAmount($Order);
        // 配送料無料条件(合計数量)
        $this->setDeliveryFreeQuantity($Order);
    }


    /**
     * 受注情報の更新
     *
     * @param Order $Order 受注情報
     */
    public function setOrderUpdateData(Order $Order)
    {
        // 受注情報を更新
        $Order->setOrderDate(new \DateTime()); // XXX 後続の setOrderStatus でも時刻を更新している
        $OrderStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_new']);
        $this->setOrderStatus($Order, $OrderStatus);

    }


    /**
     * 在庫情報の更新
     *
     * @param $em トランザクション制御されているEntityManager
     * @param Order $Order 受注情報
     */
    public function setStockUpdate($em, Order $Order)
    {

        $orderDetails = $Order->getOrderDetails();

        // 在庫情報更新
        foreach ($orderDetails as $orderDetail) {
            if (is_null($orderDetail->getProductClass())) {
                // FIXME 配送明細を考慮する必要がある
                continue;
            }
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
     * @param Order $Order 受注情報
     * @param Customer $user ログインユーザ
     */
    public function setCustomerUpdate(Order $Order, Customer $user)
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
     * @param $payments 支払選択肢情報
     * @param $subTotal 小計
     * @return array
     */
    public function getPayments($payments, $subTotal)
    {
        $pays = array();
        foreach ($payments as $payment) {
            // 支払方法の制限値内であれば表示
            if (!is_null($payment)) {
                $pay = $this->app['eccube.repository.payment']->find($payment['id']);
                if (intval($pay->getRuleMin()) <= $subTotal) {
                    if (is_null($pay->getRuleMax()) || $pay->getRuleMax() >= $subTotal) {
                        $pays[] = $pay;
                    }
                }
            }
        }

        return $pays;

    }

    /**
     * お届け日を取得
     *
     * @param Order $Order
     * @return array
     */
    public function getFormDeliveryDates(Order $Order)
    {

        // お届け日の設定
        $minDate = 0;
        $deliveryDateFlag = false;

        // 配送時に最大となる商品日数を取得
        foreach ($Order->getOrderDetails() as $detail) {
            $deliveryDate = $detail->getProductClass()->getDeliveryDate();
            if (!is_null($deliveryDate)) {
                if ($deliveryDate->getValue() < 0) {
                    // 配送日数がマイナスの場合はお取り寄せなのでスキップする
                    $deliveryDateFlag = false;
                    break;
                }

                if ($minDate < $deliveryDate->getValue()) {
                    $minDate = $deliveryDate->getValue();
                }
                // 配送日数が設定されている
                $deliveryDateFlag = true;
            }
        }

        // 配達最大日数期間を設定
        $deliveryDates = array();

        // 配送日数が設定されている
        if ($deliveryDateFlag) {
            $period = new \DatePeriod (
                new \DateTime($minDate.' day'),
                new \DateInterval('P1D'),
                new \DateTime($minDate + $this->app['config']['deliv_date_end_max'].' day')
            );

            foreach ($period as $day) {
                $deliveryDates[$day->format('Y/m/d')] = $day->format('Y/m/d');
            }
        }

        return $deliveryDates;

    }

    /**
     * 支払方法を取得
     *
     * @param $deliveries
     * @param Order $Order
     * @return array
     */
    public function getFormPayments($deliveries, Order $Order)
    {

        $productTypes = $this->orderService->getProductTypes($Order);

        if ($this->BaseInfo->getOptionMultipleShipping() == Constant::ENABLED && count($productTypes) > 1) {
            // 複数配送時の支払方法

            $payments = $this->app['eccube.repository.payment']->findAllowedPayments($deliveries);
        } else {

            // 配送業者をセット
            $shippings = $Order->getShippings();
            $Shipping = $shippings[0];
            $payments = $this->app['eccube.repository.payment']->findPayments($Shipping->getDelivery(), true);

        }
        $payments = $this->getPayments($payments, $Order->getSubTotal());

        return $payments;

    }

    /**
     * お届け先ごとにFormを作成
     *
     * @param Order $Order
     * @return \Symfony\Component\Form\Form
     * @deprecated since 3.0, to be removed in 3.1
     */
    public function getShippingForm(Order $Order)
    {
        $message = $Order->getMessage();

        $deliveries = $this->getDeliveriesOrder($Order);

        // 配送業者の支払方法を取得
        $payments = $this->getFormPayments($deliveries, $Order);

        $builder = $this->app['form.factory']->createBuilder('shopping', null, array(
            'payments' => $payments,
            'payment' => $Order->getPayment(),
            'message' => $message,
        ));

        $builder
            ->add('shippings', CollectionType::class, array(
                'entry_type' => ShippingItemType::class,
                'data' => $Order->getShippings(),
            ));

        $form = $builder->getForm();

        return $form;

    }

    /**
     * お届け先ごとにFormBuilderを作成
     *
     * @param Order $Order
     * @return \Symfony\Component\Form\FormBuilderInterface
     *
     * @deprecated 利用している箇所なし
     */
    public function getShippingFormBuilder(Order $Order)
    {
        $message = $Order->getMessage();

        $deliveries = $this->getDeliveriesOrder($Order);

        // 配送業者の支払方法を取得
        $payments = $this->getFormPayments($deliveries, $Order);

        $builder = $this->app['form.factory']->createBuilder('shopping', null, array(
            'payments' => $payments,
            'payment' => $Order->getPayment(),
            'message' => $message,
        ));

        $builder
            ->add('shippings', CollectionType::class, array(
                'entry_type' => ShippingItemType::class,
                'data' => $Order->getShippings(),
            ));

        return $builder;

    }


    /**
     * フォームデータを更新
     *
     * @param Order $Order
     * @param array $data
     *
     * @deprecated
     */
    public function setFormData(Order $Order, array $data)
    {

        // お問い合わせ
        $Order->setMessage($data['message']);

        // お届け先情報を更新
        $shippings = $data['shippings'];
        foreach ($shippings as $Shipping) {

            $deliveryTime = $Shipping->getDeliveryTime();
            if (!empty($deliveryTime)) {
                $Shipping->setShippingDeliveryTime($deliveryTime->getDeliveryTime());
            }

        }

    }

    /**
     * 配送料の合計金額を計算
     *
     * @param Order $Order
     * @return Order
     */
    public function calculateDeliveryFee(Order $Order)
    {

        // 配送業者を取得
        $shippings = $Order->getShippings();

        // 配送料合計金額
        // TODO CalculateDeliveryFeeStrategy でセットする
        // $Order->setDeliveryFeeTotal($this->getShippingDeliveryFeeTotal($shippings));

        // 配送料無料条件(合計金額)
        $this->setDeliveryFreeAmount($Order);

        // 配送料無料条件(合計数量)
        $this->setDeliveryFreeQuantity($Order);

        return $Order;

    }


    /**
     * 購入処理を行う
     *
     * @param Order $Order
     * @throws ShoppingException
     */
    public function processPurchase(Order $Order)
    {

        $em = $this->app['orm.em'];

        // TODO PurchaseFlowでやる
//        // 合計金額の再計算
//        $this->calculatePrice($Order);
//
//        // 商品公開ステータスチェック、商品制限数チェック、在庫チェック
//        $check = $this->isOrderProduct($em, $Order);
//        if (!$check) {
//            throw new ShoppingException('front.shopping.stock.error');
//        }

        // 受注情報、配送情報を更新
        $Order = $this->calculateDeliveryFee($Order);
        $this->setOrderUpdateData($Order);
        // 在庫情報を更新
        $this->setStockUpdate($em, $Order);

        if ($this->app->isGranted('ROLE_USER')) {
            // 会員の場合、購入金額を更新
            $this->setCustomerUpdate($Order, $this->app->user());
        }

    }


    /**
     * 値引き可能かチェック
     *
     * @param Order $Order
     * @param       $discount
     * @return bool
     */
    public function isDiscount(Order $Order, $discount)
    {

        if ($Order->getTotal() < $discount) {
            return false;
        }

        return true;
    }


    /**
     * 値引き金額をセット
     *
     * @param Order $Order
     * @param $discount
     */
    public function setDiscount(Order $Order, $discount)
    {

        $Order->setDiscount($Order->getDiscount() + $discount);

    }


    /**
     * 合計金額を計算
     *
     * @param Order $Order
     * @return Order
     */
    public function calculatePrice(Order $Order)
    {

        $total = $Order->getTotalPrice();

        if ($total < 0) {
            // 合計金額がマイナスの場合、0を設定し、discountは値引きされた額のみセット
            $total = 0;
        }

        $Order->setTotal($total);
        $Order->setPaymentTotal($total);

        return $Order;

    }

    /**
     * 受注ステータスをセット
     *
     * @param Order $Order
     * @param $status
     * @return Order
     */
    public function setOrderStatus(Order $Order, $status)
    {

        $Order->setOrderDate(new \DateTime());
        $Order->setOrderStatus($this->app['eccube.repository.order_status']->find($status));

        $event = new EventArgs(
            array(
                'Order' => $Order,
            ),
            null
        );
        $this->app['eccube.event.dispatcher']->dispatch(EccubeEvents::SERVICE_SHOPPING_ORDER_STATUS, $event);

        return $Order;

    }

    /**
     * 受注メール送信を行う
     *
     * @param Order $Order
     * @return MailHistory
     */
    public function sendOrderMail(Order $Order)
    {

        // メール送信
        $message = $this->app['eccube.service.mail']->sendOrderMail($Order);

        // 送信履歴を保存.
        $MailTemplate = $this->app['eccube.repository.mail_template']->find(1);

        $MailHistory = new MailHistory();
        $MailHistory
            ->setSubject($message->getSubject())
            ->setMailBody($message->getBody())
            ->setMailTemplate($MailTemplate)
            ->setSendDate(new \DateTime())
            ->setOrder($Order);

        $this->app['orm.em']->persist($MailHistory);
        $this->app['orm.em']->flush($MailHistory);

        return $MailHistory;

    }


    /**
     * 受注処理完了通知
     *
     * @param Order $Order
     */
    public function notifyComplete(Order $Order)
    {

        $event = new EventArgs(
            array(
                'Order' => $Order,
            ),
            null
        );
        $this->app['eccube.event.dispatcher']->dispatch(EccubeEvents::SERVICE_SHOPPING_NOTIFY_COMPLETE, $event);

    }


}
