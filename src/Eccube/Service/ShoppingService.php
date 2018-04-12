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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Service;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\Delivery;
use Eccube\Entity\MailHistory;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Form\Type\ShippingItemType;
use Eccube\Repository\CustomerAddressRepository;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\DeliveryTimeRepository;
use Eccube\Repository\MailTemplateRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Util\StringUtil;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Service
 */
class ShoppingService
{
    /**
     * @var MailTemplateRepository
     */
    protected $mailTemplateRepository;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var DeliveryFeeRepository
     */
    protected $deliveryFeeRepository;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepository;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @var DeliveryTimeRepository
     */
    protected $deliveryTimeRepository;

    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var PrefRepository
     */
    protected $prefRepository;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var \Eccube\Service\CartService
     */
    protected $cartService;

    /**
     * @var \Eccube\Service\OrderService
     *
     * @deprecated
     */
    protected $orderService;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * ShoppingService constructor.
     * @param MailTemplateRepository $mailTemplateRepository
     * @param MailService $mailService
     * @param EventDispatcher $eventDispatcher
     * @param FormFactory $formFactory
     * @param DeliveryFeeRepository $deliveryFeeRepository
     * @param TaxRuleRepository $taxRuleRepository
     * @param CustomerAddressRepository $customerAddressRepository
     * @param DeliveryRepository $deliveryRepository
     * @param DeliveryTimeRepository $deliveryTimeRepository
     * @param OrderStatusRepository $orderStatusRepository
     * @param PaymentRepository $paymentRepository
     * @param DeviceTypeRepository $deviceTypeRepository
     * @param EntityManager $entityManager
     * @param EccubeConfig $eccubeConfig
     * @param PrefRepository $prefRepository
     * @param Session $session
     * @param OrderRepository $orderRepository
     * @param CartService $cartService
     * @param OrderService $orderService
     * @param BaseInfo $BaseInfo
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        MailTemplateRepository $mailTemplateRepository,
        MailService $mailService,
        EventDispatcherInterface $eventDispatcher,
        FormFactoryInterface $formFactory,
        DeliveryFeeRepository $deliveryFeeRepository,
        TaxRuleRepository $taxRuleRepository,
        CustomerAddressRepository $customerAddressRepository,
        DeliveryRepository $deliveryRepository,
        DeliveryTimeRepository $deliveryTimeRepository,
        OrderStatusRepository $orderStatusRepository,
        PaymentRepository $paymentRepository,
        DeviceTypeRepository $deviceTypeRepository,
        EntityManagerInterface $entityManager,
        EccubeConfig $eccubeConfig,
        PrefRepository $prefRepository,
        SessionInterface $session,
        OrderRepository $orderRepository,
        CartService $cartService,
        OrderService $orderService,
        BaseInfo $BaseInfo,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->mailService = $mailService;
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->deliveryFeeRepository = $deliveryFeeRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->deliveryTimeRepository = $deliveryTimeRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->paymentRepository = $paymentRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
        $this->entityManager = $entityManager;
        $this->eccubeConfig = $eccubeConfig;
        $this->prefRepository = $prefRepository;
        $this->session = $session;
        $this->orderRepository = $orderRepository;
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->BaseInfo = $BaseInfo;
        $this->authorizationChecker = $authorizationChecker;
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

        $Order = $this->orderRepository->findOneBy($condition);

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
        $nonMember = $this->session->get($sesisonKey);
        if (is_null($nonMember)) {
            return null;
        }
        if (!array_key_exists('customer', $nonMember) || !array_key_exists('pref', $nonMember)) {
            return null;
        }

        $Customer = $nonMember['customer'];
        $Customer->setPref($this->prefRepository->find($nonMember['pref']));

        foreach ($Customer->getCustomerAddresses() as $CustomerAddress) {
            $Pref = $CustomerAddress->getPref();
            if ($Pref) {
                $CustomerAddress->setPref($this->prefRepository->find($Pref->getId()));
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
            $preOrderId = sha1(StringUtil::random(32));
            $Order = $this->orderRepository->findOneBy(array(
                'pre_order_id' => $preOrderId,
                'OrderStatus' => OrderStatus::PROCESSING,
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

        $this->em = $this->entityManager;

        // 受注情報を作成
        $Order = $this->getNewOrder($Customer);
        $Order->setPreOrderId($preOrderId);

        $mobileDetect = new \Mobile_Detect();
        $DeviceType = $this->deviceTypeRepository->find($mobileDetect->isMobile() ? DeviceType::DEVICE_TYPE_SP : DeviceType::DEVICE_TYPE_PC);
        $Order->setDeviceType($DeviceType);

        $this->entityManager->persist($Order);

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
        $payments = $this->paymentRepository->findAllowedPayments($deliveries);
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

        $this->entityManager->flush();

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
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::PROCESSING);
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

        // カートに保持されている販売種別を取得
        $saleTypes = $this->cartService->getSaleTypes();

        return $this->getDeliveries($saleTypes);

    }

    /**
     * 配送業者情報を取得
     *
     * @param Order $Order
     * @return array
     */
    public function getDeliveriesOrder(Order $Order)
    {

        // 受注情報から販売種別を取得
        $saleTypes = $this->orderService->getSaleTypes($Order);

        return $this->getDeliveries($saleTypes);

    }

    /**
     * 配送業者情報を取得
     *
     * @param $saleTypes
     * @return array
     */
    public function getDeliveries($saleTypes)
    {

        // 販売種別に紐づく配送業者を取得
        $deliveries = $this->deliveryRepository->getDeliveries($saleTypes);

        if ($this->BaseInfo->isOptionMultipleShipping()) {
            // 複数配送対応

            // 支払方法を取得
            $payments = $this->paymentRepository->findAllowedPayments($deliveries);

            if (count($saleTypes) > 1) {
                // 販売種別が複数ある場合、配送対象となる配送業者を取得
                $deliveries = $this->deliveryRepository->findAllowedDeliveries($saleTypes, $payments);
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
        $saleTypes = array();
        foreach ($deliveries as $Delivery) {
            if (!in_array($Delivery->getSaleType()->getId(), $saleTypes)) {
                $Shipping = new Shipping();

                $this->copyToShippingFromCustomer($Shipping, $Customer)
                    ->setOrder($Order);

                // 配送料金の設定
                $this->setShippingDeliveryFee($Shipping, $Delivery);

                $this->entityManager->persist($Shipping);

                $Order->addShipping($Shipping);

                $saleTypes[] = $Delivery->getProductType()->getId();
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

        $CustomerAddress = $this->customerAddressRepository->findOneBy(
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
            $ProductClass = $item->getProductClass();
            /* @var $Product \Eccube\Entity\Product */
            $Product = $ProductClass->getProduct();

            $quantity = $item->getQuantity();

            // 配送商品情報を作成
            $this->getNewOrderItem($Order, $Product, $ProductClass, $quantity);
        }

        return $Order;

    }

    /**
     * 配送商品情報を作成
     *
     * @param Order $Order
     * @param Product $Product
     * @param ProductClass $ProductClass
     * @param $quantity
     * @return \Eccube\Entity\OrderItem
     */
    public function getNewOrderItem(Order $Order, Product $Product, ProductClass $ProductClass, $quantity)
    {

        $OrderItem = new OrderItem();
        $shippings = $Order->getShippings();

        // 選択された商品がどのお届け先情報と関連するかチェック
        $Shipping = null;
        foreach ($shippings as $s) {
            if ($s->getDelivery()->getSaleType()->getId() == $ProductClass->getSaleType()->getId()) {
                // 販売種別が同一のお届け先情報と関連させる
                $Shipping = $s;
                break;
            }
        }

        if (is_null($Shipping)) {
            // お届け先情報と関連していない場合、エラー
            throw new CartException('shopping.delivery.not.saletype');
        }

        // 商品ごとの配送料合計
        $productDeliveryFeeTotal = 0;
        if ($this->BaseInfo->isOptionProductDeliveryFee()) {
            $productDeliveryFeeTotal = $ProductClass->getDeliveryFee() * $quantity;
        }

        $Shipping->setShippingDeliveryFee($Shipping->getShippingDeliveryFee() + $productDeliveryFeeTotal);

        $OrderItem->setShipping($Shipping)
            ->setOrder($Order)
            ->setProductClass($ProductClass)
            ->setProduct($Product)
            ->setProductName($Product->getName())
            ->setProductCode($ProductClass->getCode())
            ->setPrice($ProductClass->getPrice02())
            ->setQuantity($quantity);

        $ClassCategory1 = $ProductClass->getClassCategory1();
        if (!is_null($ClassCategory1)) {
            $OrderItem->setClasscategoryName1($ClassCategory1->getName());
            $OrderItem->setClassName1($ClassCategory1->getClassName()->getDisplayName());
        }
        $ClassCategory2 = $ProductClass->getClassCategory2();
        if (!is_null($ClassCategory2)) {
            $OrderItem->setClasscategoryName2($ClassCategory2->getName());
            $OrderItem->setClassName2($ClassCategory2->getClassName()->getDisplayName());
        }
        $Shipping->addOrderItem($OrderItem);
        $this->entityManager->persist($OrderItem);

        return $OrderItem;

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
        $OrderItems = $Shipping->getOrderItems();
        foreach ($OrderItems as $OrderItem) {
            $productDeliveryFeeTotal += $OrderItem->getProductClass()->getDeliveryFee() * $OrderItem->getQuantity();
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
        $deliveryFee = $this->deliveryFeeRepository->findOneBy(array('Delivery' => $Delivery, 'Pref' => $Shipping->getPref()));
        if ($deliveryFee) {
            $Shipping->setFeeId($deliveryFee->getId());
        }
        $Shipping->setDelivery($Delivery);

        // 商品ごとの配送料合計
        $productDeliveryFeeTotal = 0;
        if ($this->BaseInfo->isOptionProductDeliveryFee()) {
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
        $Order->setOrderStatus($this->orderStatusRepository->find(OrderStatus::NEW));
        $Order->setMessage($data['message']);
        // お届け先情報を更新
        $shippings = $data['shippings'];
        foreach ($shippings as $Shipping) {
            $Delivery = $Shipping->getDelivery();
            $deliveryFee = $this->deliveryFeeRepository->findOneBy(array(
                'Delivery' => $Delivery,
                'Pref' => $Shipping->getPref()
            ));
            $deliveryTime = $Shipping->getDeliveryTime();
            if (!empty($deliveryTime)) {
                $Shipping->setShippingDeliveryTime($deliveryTime->getDeliveryTime());
                $Shipping->setTimeId($deliveryTime->getId());
            }
            $Shipping->setDeliveryFee($deliveryFee);
            // 商品ごとの配送料合計
            $productDeliveryFeeTotal = 0;
            if ($this->BaseInfo->isOptionProductDeliveryFee()) {
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
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::NEW);
        $this->setOrderStatus($Order, $OrderStatus);

    }


    /**
     * 会員情報の更新
     *
     * @param Order $Order 受注情報
     * @param Customer $user ログインユーザ
     */
    public function setCustomerUpdate(Order $Order, Customer $user)
    {
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
                $pay = $this->paymentRepository->find($payment['id']);
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
    public function getFormDeliveryDurations(Order $Order)
    {

        // お届け日の設定
        $minDate = 0;
        $deliveryDurationFlag = false;

        // 配送時に最大となる商品日数を取得
        foreach ($Order->getOrderItems() as $item) {
            if (!$item->isProduct()) {
                continue;
            }
            $ProductClass = $item->getProductClass();
            $deliveryDuration = $ProductClass->getDeliveryDuration();
            if (!is_null($deliveryDuration)) {
                if ($deliveryDuration->getDuration() < 0) {
                    // 配送日数がマイナスの場合はお取り寄せなのでスキップする
                    $deliveryDurationFlag = false;
                    break;
                }

                if ($minDate < $deliveryDuration->getDuration()) {
                    $minDate = $deliveryDuration->getDuration();
                }
                // 配送日数が設定されている
                $deliveryDurationFlag = true;
            }
        }

        // 配達最大日数期間を設定
        $deliveryDurations = array();

        // 配送日数が設定されている
        if ($deliveryDurationFlag) {
            $period = new \DatePeriod (
                new \DateTime($minDate.' day'),
                new \DateInterval('P1D'),
                new \DateTime($minDate + $this->eccubeConfig['eccube_deliv_date_end_max'].' day')
            );

            foreach ($period as $day) {
                $deliveryDurations[$day->format('Y/m/d')] = $day->format('Y/m/d');
            }
        }

        return $deliveryDurations;
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

        $saleTypes = $this->orderService->getSaleTypes($Order);
        if ($this->BaseInfo->isOptionMultipleShipping() && count($saleTypes) > 1) {
            // 複数配送時の支払方法

            $payments = $this->paymentRepository->findAllowedPayments($deliveries);
        } else {
            // 配送業者をセット
            $shippings = $Order->getShippings();
            $Shipping = $shippings[0];
            $payments = $this->paymentRepository->findPayments($Shipping->getDelivery(), true);
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

        $builder = $this->formFactory->createBuilder('shopping', null, array(
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

        $builder = $this->formFactory->createBuilder('shopping', null, array(
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

            $timeId = $Shipping->getTimeId();
            $deliveryTime = null;
            if ($timeId) {
                $deliveryTime = $this->deliveryTimeRepository->find($timeId);
            }
            if ($deliveryTime) {
                $Shipping->setShippingDeliveryTime($deliveryTime->getDeliveryTime());
                $Shipping->setTimeId($timeId);
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
     * @deprecated PurchaseFlow::purchase() を使用してください
     */
    public function processPurchase(Order $Order)
    {
        // 受注情報、配送情報を更新
        $Order = $this->calculateDeliveryFee($Order);
        $this->setOrderUpdateData($Order);

        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            $this->setCustomerUpdate($Order, $Order->getCustomer());
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
        $Order->setOrderStatus($this->orderStatusRepository->find($status));

        $event = new EventArgs(
            array(
                'Order' => $Order,
            ),
            null
        );
        $this->eventDispatcher->dispatch(EccubeEvents::SERVICE_SHOPPING_ORDER_STATUS, $event);

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
        $message = $this->mailService->sendOrderMail($Order);

        // 送信履歴を保存.
        $MailHistory = new MailHistory();
        $MailHistory
            ->setMailSubject($message->getSubject())
            ->setMailBody($message->getBody())
            ->setSendDate(new \DateTime())
            ->setOrder($Order);

        $this->entityManager->persist($MailHistory);
        $this->entityManager->flush($MailHistory);

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
        $this->eventDispatcher->dispatch(EccubeEvents::SERVICE_SHOPPING_NOTIFY_COMPLETE, $event);

    }


}
