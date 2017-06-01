<?php

namespace Eccube\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\CartItem;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Shipping;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\Master\TaxruleRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Util\Str;

/**
 * OrderやOrderに関連するエンティティを構築するクラス
 * namespaceやクラス名は要検討
 *
 * @package Eccube\Service
 */
class OrderHelper
{
    /** @var array */
    protected $config;

    /** @var EntityManager */
    protected $em;

    /** @var OrderRepository */
    protected $orderRepository;

    /** @var DeliveryRepository */
    protected $deliveryRepository;

    /** @var DeliveryFeeRepository */
    protected $deliveryFeeRepository;

    /** @var PaymentRepository */
    protected $paymentRepository;

    /** @var TaxruleRepository */
    protected $taxRuleRepository;

    /** @var  OrderStatusRepository */
    protected $orderStatusRepository;

    /** @var OrderItemTypeRepository */
    protected $orderItemTypeRepository;

    public function __construct(Application $app)
    {
        $this->config = $app['config'];
        $this->em = $app['orm.em'];
        $this->orderRepository = $app['eccube.repository.order'];
        $this->paymentRepository = $app['eccube.repository.payment'];
        $this->deliveryRepository = $app['eccube.repository.delivery'];
        $this->deliveryFeeRepository = $app['eccube.repository.delivery_fee'];
        $this->taxRuleRepository = $app['eccube.repository.tax_rule'];
        $this->orderStatusRepository = $app['eccube.repository.order_status'];
        $this->orderItemTypeRepository = $app['eccube.repository.master.order_item_type'];
    }

    /**
     * 購入処理中の受注データを生成する.
     *
     * @param Customer $Customer
     * @param CustomerAddress $CustomerAddress
     * @param array $CartItems
     * @return Order
     */
    public function createProcessingOrder(Customer $Customer, CustomerAddress $CustomerAddress, $CartItems)
    {
        $OrderStatus = $this->orderStatusRepository->find($this->config['order_processing']);
        $Order = new Order($OrderStatus);

        // pre_order_idを生成
        $Order->setPreOrderId($this->createPreOrderId());

        // 顧客情報の設定
        $this->setCustomer($Order, $Customer);

        // 明細情報の設定
        $ShipmentItems = $this->createShipmentItemsFromCartItems($CartItems);
        $ShipmentItemsGroupByProductType = array_reduce($ShipmentItems, function($result, $item) {
            /* @var ShipmentItem $item */
            $productTypeId = $item->getProductClass()->getProductType()->getId();
            $result[$productTypeId][] = $item;
            return $result;
        }, []);

        foreach ($ShipmentItemsGroupByProductType as $ShipmentItems) {
            $Shipping = $this->createShippingFromCustomerAddress($CustomerAddress);
            $this->addShipmentItems($Order, $Shipping, $ShipmentItems);
            $this->setDefaultDelivery($Shipping);
            $this->em->persist($Shipping);
        }

        $this->setDefaultPayment($Order);

        $this->em->persist($Order);
        $this->em->flush();

        return $Order;
    }

    public function createPreOrderId()
    {
        // ランダムなpre_order_idを作成
        do {
            $preOrderId = sha1(Str::random(32));

            $Order = $this->orderRepository->findOneBy(
                [
                    'pre_order_id' => $preOrderId,
                    'OrderStatus' => $this->config['order_processing'],
                ]
            );
        } while ($Order);

        return $preOrderId;
    }

    public function setCustomer(Order $Order, Customer $Customer)
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
     * @return ShipmentItem[]
     */
    private function createShipmentItemsFromCartItems($CartItems)
    {
        $ProductItemType = $this->orderItemTypeRepository->find(OrderItemType::PRODUCT);
        // TODO
        $TaxExclude = $this->em->getRepository(TaxDisplayType::class)->find(TaxDisplayType::EXCLUDED);
        $Taxion = $this->em->getRepository(TaxType::class)->find(TaxType::TAXATION);

        return array_map(function($item) use ($ProductItemType, $TaxExclude, $Taxion) {
            /* @var $item CartItem */
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $item->getObject();
            /* @var $Product \Eccube\Entity\Product */
            $Product = $ProductClass->getProduct();
            $TaxRule = $this->taxRuleRepository->getByRule($Product, $ProductClass);

            $ShipmentItem = new ShipmentItem();
            $ShipmentItem
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
                $ShipmentItem->setClasscategoryName1($ClassCategory1->getName());
                $ShipmentItem->setClassName1($ClassCategory1->getClassName()->getName());
            }
            $ClassCategory2 = $ProductClass->getClassCategory2();
            if (!is_null($ClassCategory2)) {
                $ShipmentItem->setClasscategoryName2($ClassCategory2->getName());
                $ShipmentItem->setClassName2($ClassCategory2->getClassName()->getName());
            }

            return $ShipmentItem;
        }, $CartItems->toArray());
    }

    /**
     * @deprecated
     */
    public function createOrderDetailsFromCartItems($CartItems)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
        $OrderDetails = [];

        foreach ($CartItems as $item) {
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $item->getObject();
            /* @var $Product \Eccube\Entity\Product */
            $Product = $ProductClass->getProduct();

            $OrderDetail = new OrderDetail();
            $TaxRule = $this->taxRuleRepository->getByRule($Product, $ProductClass);
            $OrderDetail
                ->setProduct($Product)
                ->setProductClass($ProductClass)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($item->getQuantity())
                ->setTaxRule($TaxRule->getId())
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

            $OrderDetails[] = $OrderDetail;
        }

        return $OrderDetails;
    }

    /**
     * @deprecated
     */
    public function addOrderDetails(Order $Order, array $OrderDetails)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
        foreach ($OrderDetails as $OrderDetail) {
            $Order->addOrderDetail($OrderDetail);
            $OrderDetail->setOrder($Order);
        }
    }

    public function createShippingFromCustomerAddress(CustomerAddress $CustomerAddress)
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
            ->setAddr02($CustomerAddress->getAddr02())
            ->setDelFlg(Constant::DISABLED);

        return $Shipping;
    }

    /**
     * @deprecated
     */
    public function addShipping(Order $Order, Shipping $Shipping)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
        $Order->addShipping($Shipping);
        $Shipping->setOrder($Order);
    }

    public function setDefaultDelivery(Shipping $Shipping)
    {
        // 配送商品に含まれる商品種別を抽出.
        $ShipmentItems = $Shipping->getShipmentItems();
        $ProductTypes = [];
        foreach ($ShipmentItems as $ShipmentItem) {
            $ProductClass = $ShipmentItem->getProductClass();
            $ProductType = $ProductClass->getProductType();
            $ProductTypes[$ProductType->getId()] = $ProductType;
        }

        // 商品種別に紐づく配送業者を取得.
        $Deliveries = $this->deliveryRepository->getDeliveries($ProductTypes);

        // 初期の配送業者を設定
        $Delivery = current($Deliveries);
        $Shipping->setDelivery($Delivery);
        $Shipping->setShippingDeliveryName($Delivery->getName());

        // TODO 配送料の取得方法はこれで良いか要検討
        $deliveryFee = $this->deliveryFeeRepository->findOneBy(array('Delivery' => $Delivery, 'Pref' => $Shipping->getPref()));
        $Shipping->setShippingDeliveryFee($deliveryFee->getFee());
    }

    public function setDefaultPayment(Order $Order)
    {
        $ShipmentItems = $Order->getShipmentItems();

        // 受注明細に含まれる商品種別を抽出.
        $ProductTypes = [];
        /** @var ShipmentItem $ShipmentItem */
        foreach ($ShipmentItems as $ShipmentItem) {
            $ProductClass = $ShipmentItem->getProductClass();
            if (is_null($ProductClass)) {
                // 商品明細のみ対象とする. 送料明細等はスキップする.
                continue;
            }
            $ProductType = $ProductClass->getProductType();
            $ProductTypes[$ProductType->getId()] = $ProductType;
        }

        // 商品種別に紐づく配送業者を抽出
        $Deliveries = $this->deliveryRepository->getDeliveries($ProductTypes);

        // 利用可能な支払い方法を抽出.
        $Payments = $this->paymentRepository->findAllowedPayments($Deliveries, true);

        // 初期の支払い方法を設定.
        $Payment = current($Payments);
        $Order->setPayment($Payment);
        $Order->setPaymentMethod($Payment->getMethod());
        $Order->setCharge($Payment->getCharge());
    }

    /**
     * @deprecated
     */
    public function createShipmentItemsFromOrderDetails($OrderDetails, $groupByProductType = true)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
        $ShipmentItems = [];

        foreach ($OrderDetails as $OrderDetail) {
            $Product = $OrderDetail->getProduct();
            $ProductClass = $OrderDetail->getProductClass();
            $ProductType = $ProductClass->getProductType();

            $ShipmentItem = new ShipmentItem();
            $ShipmentItem
                ->setProductClass($ProductClass)
                ->setProduct($Product)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($OrderDetail->getQuantity());

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
            if ($groupByProductType) {
                $ShipmentItems[$ProductType->getId()][] = $ShipmentItem;
            } else {
                $ShipmentItems[] = $ShipmentItem;
            }
        }

        return $ShipmentItems;
    }

    public function addShipmentItems(Order $Order, Shipping $Shipping, array $ShipmentItems)
    {
        foreach ($ShipmentItems as $ShipmentItem) {
            $Shipping->addShipmentItem($ShipmentItem);
            $Order->addShipmentItem($ShipmentItem);
            $ShipmentItem->setOrder($Order);
            $ShipmentItem->setShipping($Shipping);
        }
    }

}
