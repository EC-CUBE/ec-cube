<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\ShippingStatus;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\ShippingStatusRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Util\StringUtil;

/**
 * OrderやOrderに関連するエンティティを構築するクラス
 * namespaceやクラス名は要検討
 */
class OrderHelper
{
    /**
     * @var OrderItemTypeRepository
     */
    protected $orderItemTypeRepository;

    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * @var DeliveryFeeRepository
     */
    protected $deliveryFeeRepository;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var ShippingStatusRepository
     */
    protected $shippingStatusRepository;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * OrderHelper constructor.
     *
     * @param OrderItemTypeRepository $orderItemTypeRepository
     * @param OrderStatusRepository $orderStatusRepository
     * @param TaxRuleRepository $taxRuleRepository
     * @param DeliveryFeeRepository $deliveryFeeRepository
     * @param DeliveryRepository $deliveryRepository
     * @param PaymentRepository $paymentRepository
     * @param OrderRepository $orderRepository
     * @param ShippingStatusRepository $shippingStatusRepository
     * @param EntityManager $entityManager
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        OrderItemTypeRepository $orderItemTypeRepository,
        OrderStatusRepository $orderStatusRepository,
        TaxRuleRepository $taxRuleRepository,
        DeliveryFeeRepository $deliveryFeeRepository,
        DeliveryRepository $deliveryRepository,
        PaymentRepository $paymentRepository,
        OrderRepository $orderRepository,
        ShippingStatusRepository $shippingStatusRepository,
        EntityManagerInterface $entityManager,
        EccubeConfig $eccubeConfig
    ) {
        $this->orderItemTypeRepository = $orderItemTypeRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->deliveryFeeRepository = $deliveryFeeRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->paymentRepository = $paymentRepository;
        $this->orderRepository = $orderRepository;
        $this->shippingStatusRepository = $shippingStatusRepository;
        $this->entityManager = $entityManager;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * 購入処理中の受注データを生成する.
     *
     * @param Customer $Customer
     * @param CustomerAddress $CustomerAddress
     * @param array $CartItems
     *
     * @return Order
     */
    public function createProcessingOrder(Customer $Customer, CustomerAddress $CustomerAddress, $CartItems, $preOrderId = null)
    {
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::PROCESSING);
        $Order = new Order($OrderStatus);

        if (!$preOrderId) {
            // pre_order_idを生成
            $Order->setPreOrderId($this->createPreOrderId());
        }

        // 顧客情報の設定
        $this->setCustomer($Order, $Customer);

        // 明細情報の設定
        $OrderItems = $this->createOrderItemsFromCartItems($CartItems);
        $OrderItemsGroupBySaleType = array_reduce($OrderItems, function ($result, $item) {
            /* @var OrderItem $item */
            $saleTypeId = $item->getProductClass()->getSaleType()->getId();
            $result[$saleTypeId][] = $item;

            return $result;
        }, []);

        foreach ($OrderItemsGroupBySaleType as $OrderItems) {
            $Shipping = $this->createShippingFromCustomerAddress($CustomerAddress);
            $this->addOrderItems($Order, $Shipping, $OrderItems);
            $this->setDefaultDelivery($Shipping);
            $this->entityManager->persist($Shipping);
        }

        $this->setDefaultPayment($Order);

        $this->entityManager->persist($Order);
        $this->entityManager->flush();

        return $Order;
    }

    /**
     * OrderをCartに変換します.
     *
     * @param Order $Order
     *
     * @return Cart
     */
    public function convertToCart(Order $Order)
    {
        $Cart = new Cart();
        $Cart->setPreOrderId($Order->getPreOrderId());
        /** @var OrderItem $OrderItem */
        foreach ($Order->getProductOrderItems() as $OrderItem) {
            $CartItem = new CartItem();
            $ProductClass = $OrderItem->getProductClass();
            $this->entityManager->refresh($ProductClass);
            $CartItem->setProductClass($ProductClass);
            $CartItem->setPrice($OrderItem->getPriceIncTax());
            $CartItem->setQuantity($OrderItem->getQuantity());
            $Cart->addCartItem($CartItem);
        }

        return $Cart;
    }

    private function createPreOrderId()
    {
        // ランダムなpre_order_idを作成
        do {
            $preOrderId = sha1(StringUtil::random(32));

            $Order = $this->orderRepository->findOneBy(
                [
                    'pre_order_id' => $preOrderId,
                    'OrderStatus' => OrderStatus::PROCESSING,
                ]
            );
        } while ($Order);

        return $preOrderId;
    }

    private function setCustomer(Order $Order, Customer $Customer)
    {
        if ($Customer->getId()) {
            $Order->setCustomer($Customer);
        }

        $Order->copyProperties(
            $Customer,
            [
                'id',
                'create_date',
                'update_date',
                'del_flg',
            ]
        );
    }

    /**
     * @param ArrayCollection $CartItems
     *
     * @return OrderItem[]
     */
    private function createOrderItemsFromCartItems($CartItems)
    {
        $ProductItemType = $this->orderItemTypeRepository->find(OrderItemType::PRODUCT);
        // TODO
        $TaxExclude = $this->entityManager->getRepository(TaxDisplayType::class)->find(TaxDisplayType::EXCLUDED);
        $Taxion = $this->entityManager->getRepository(TaxType::class)->find(TaxType::TAXATION);

        return array_map(function ($item) use ($ProductItemType, $TaxExclude, $Taxion) {
            /* @var $item CartItem */
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $item->getProductClass();
            /* @var $Product \Eccube\Entity\Product */
            $Product = $ProductClass->getProduct();
            $TaxRule = $this->taxRuleRepository->getByRule($Product, $ProductClass);

            $OrderItem = new OrderItem();
            $OrderItem
                ->setProduct($Product)
                ->setProductClass($ProductClass)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($item->getQuantity())
                ->setTaxRule($TaxRule->getId())
                ->setTaxRate($TaxRule->getTaxRate())
                ->setOrderItemType($ProductItemType)
                ->setTaxDisplayType($TaxExclude)
                ->setTaxType($Taxion);

            $ClassCategory1 = $ProductClass->getClassCategory1();
            if (!is_null($ClassCategory1)) {
                $OrderItem->setClasscategoryName1($ClassCategory1->getName());
                $OrderItem->setClassName1($ClassCategory1->getClassName()->getName());
            }
            $ClassCategory2 = $ProductClass->getClassCategory2();
            if (!is_null($ClassCategory2)) {
                $OrderItem->setClasscategoryName2($ClassCategory2->getName());
                $OrderItem->setClassName2($ClassCategory2->getClassName()->getName());
            }

            return $OrderItem;
        }, $CartItems->toArray());
    }

    private function createShippingFromCustomerAddress(CustomerAddress $CustomerAddress)
    {
        $Shipping = new Shipping();
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

        $ShippingStatus = $this->shippingStatusRepository->find(ShippingStatus::PREPARED);
        $Shipping->setShippingStatus($ShippingStatus);

        return $Shipping;
    }

    private function setDefaultDelivery(Shipping $Shipping)
    {
        // 配送商品に含まれる販売種別を抽出.
        $OrderItems = $Shipping->getOrderItems();
        $SaleTypes = [];
        /** @var OrderItem $OrderItem */
        foreach ($OrderItems as $OrderItem) {
            $ProductClass = $OrderItem->getProductClass();
            $SaleType = $ProductClass->getSaleType();
            $SaleTypes[$SaleType->getId()] = $SaleType;
        }

        // 販売種別に紐づく配送業者を取得.
        $Deliveries = $this->deliveryRepository->getDeliveries($SaleTypes);

        // 初期の配送業者を設定
        $Delivery = current($Deliveries);
        $Shipping->setDelivery($Delivery);
        $Shipping->setShippingDeliveryName($Delivery->getName());
    }

    private function setDefaultPayment(Order $Order)
    {
        $OrderItems = $Order->getOrderItems();

        // 受注明細に含まれる販売種別を抽出.
        $SaleTypes = [];
        /** @var OrderItem $OrderItem */
        foreach ($OrderItems as $OrderItem) {
            $ProductClass = $OrderItem->getProductClass();
            if (is_null($ProductClass)) {
                // 商品明細のみ対象とする. 送料明細等はスキップする.
                continue;
            }
            $SaleType = $ProductClass->getSaleType();
            $SaleTypes[$SaleType->getId()] = $SaleType;
        }

        // 販売種別に紐づく配送業者を抽出
        $Deliveries = $this->deliveryRepository->getDeliveries($SaleTypes);

        // 利用可能な支払い方法を抽出.
        $Payments = $this->paymentRepository->findAllowedPayments($Deliveries, true);

        // 初期の支払い方法を設定.
        $Payment = current($Payments);
        if ($Payment) {
            $Order->setPayment($Payment);
            $Order->setPaymentMethod($Payment->getMethod());
        }
        // TODO CalculateChargeStrategy でセットする
        // $Order->setCharge($Payment->getCharge());
    }

    private function addOrderItems(Order $Order, Shipping $Shipping, array $OrderItems)
    {
        foreach ($OrderItems as $OrderItem) {
            $Shipping->addOrderItem($OrderItem);
            $Order->addOrderItem($OrderItem);
            $OrderItem->setOrder($Order);
            $OrderItem->setShipping($Shipping);
        }
    }
}
