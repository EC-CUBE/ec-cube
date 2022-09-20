<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Master\TaxType;
use Eccube\Service\Calculator\OrderItemCollection;
use Eccube\Service\PurchaseFlow\ItemCollection;
use Eccube\Service\TaxRuleService;

if (!class_exists('\Eccube\Entity\Order')) {
    /**
     * Order
     *
     * @ORM\Table(name="dtb_order", indexes={
     *     @ORM\Index(name="dtb_order_email_idx", columns={"email"}),
     *     @ORM\Index(name="dtb_order_order_date_idx", columns={"order_date"}),
     *     @ORM\Index(name="dtb_order_payment_date_idx", columns={"payment_date"}),
     *     @ORM\Index(name="dtb_order_update_date_idx", columns={"update_date"}),
     *     @ORM\Index(name="dtb_order_order_no_idx", columns={"order_no"})
     *  },
     *  uniqueConstraints={
     *     @ORM\UniqueConstraint(name="dtb_order_pre_order_id_idx", columns={"pre_order_id"})
     *  })
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\OrderRepository")
     */
    class Order extends \Eccube\Entity\AbstractEntity implements PurchaseInterface, ItemHolderInterface
    {
        use NameTrait;
        use PointTrait;

        /**
         * 課税対象の明細を返す.
         *
         * @return OrderItem[]
         */
        public function getTaxableItems()
        {
            $Items = [];

            foreach ($this->OrderItems as $Item) {
                if (null === $Item->getTaxType()) {
                    continue;
                }

                if ($Item->getTaxType()->getId() == TaxType::TAXATION) {
                    $Items[] = $Item;
                }
            }

            return $Items;
        }

        /**
         * 課税対象の明細の合計金額を返す.
         * 商品合計 + 送料 + 手数料 + 値引き(課税).
         */
        public function getTaxableTotal()
        {
            $total = 0;

            foreach ($this->getTaxableItems() as $Item) {
                $total += $Item->getTotalPrice();
            }

            return $total;
        }

        /**
         * 課税対象の明細の合計金額を、税率ごとに集計する.
         *
         * @return array
         */
        public function getTaxableTotalByTaxRate()
        {
            $total = [];

            foreach ($this->getTaxableItems() as $Item) {
                $totalPrice = $Item->getTotalPrice();
                $taxRate = $Item->getTaxRate();
                $total[$taxRate] = isset($total[$taxRate])
                    ? $total[$taxRate] + $totalPrice
                    : $totalPrice;
            }

            krsort($total);

            return $total;
        }

        /**
         * 明細の合計額を税率ごとに集計する.
         *
         * 不課税, 非課税の値引明細は税率ごとに按分する.
         *
         * @return int[]
         */
        public function getTotalByTaxRate()
        {
            $roundingTypes = $this->getRoundingTypeByTaxRate();
            $total = [];
            foreach ($this->getTaxableTotalByTaxRate() as $rate => $totalPrice) {
                $total[$rate] = TaxRuleService::roundByRoundingType(
                    $this->getTaxableTotal() ?
                        $totalPrice - abs($this->getTaxFreeDiscount()) * $totalPrice / $this->getTaxableTotal() : 0,
                    $roundingTypes[$rate]->getId()
                );
            }

            ksort($total);

            return $total;
        }

        /**
         * 税額を税率ごとに集計する.
         *
         * 不課税, 非課税の値引明細は税率ごとに按分する.
         *
         * @return int[]
         */
        public function getTaxByTaxRate()
        {
            $roundingTypes = $this->getRoundingTypeByTaxRate();
            $tax = [];
            foreach ($this->getTaxableTotalByTaxRate() as $rate => $totalPrice) {
                $tax[$rate] = TaxRuleService::roundByRoundingType(
                    $this->getTaxableTotal() ?
                        ($totalPrice - abs($this->getTaxFreeDiscount()) * $totalPrice / $this->getTaxableTotal()) * ($rate / (100 + $rate)) : 0,
                    $roundingTypes[$rate]->getId()
                );
            }

            ksort($tax);

            return $tax;
        }

        /**
         * 課税対象の値引き明細を返す.
         *
         * @return array
         */
        public function getTaxableDiscountItems()
        {
            $items = (new ItemCollection($this->getTaxableItems()))->sort()->toArray();
            return array_filter($items, function (OrderItem $Item) {
                return $Item->isDiscount();
            });
        }

        /**
         * 課税対象の値引き金額合計を返す.
         *
         * @return mixed
         */
        public function getTaxableDiscount()
        {
            return array_reduce($this->getTaxableDiscountItems(), function ($sum, OrderItem $Item) {
                return $sum += $Item->getTotalPrice();
            }, 0);
        }

        /**
         * 非課税・不課税の値引き明細を返す.
         *
         * @return array
         */
        public function getTaxFreeDiscountItems()
        {
            $items = (new ItemCollection($this->getOrderItems()))->sort()->toArray();
            return array_filter($items, function (OrderItem $Item) {
                return $Item->isPoint() || ($Item->isDiscount() && $Item->getTaxType()->getId() != TaxType::TAXATION);
            });
        }

        /**
         * 非課税・不課税の値引き額を返す.
         *
         * @return int|float
         */
        public function getTaxFreeDiscount()
        {
            return array_reduce($this->getTaxFreeDiscountItems(), function ($sum, OrderItem $Item) {
                return $sum += $Item->getTotalPrice();
            }, 0);
        }

        /**
         * 税率ごとの丸め規則を取得する.
         *
         * @return array<string, RoundingType>
         */
        public function getRoundingTypeByTaxRate()
        {
            $roundingTypes = [];
            foreach ($this->getTaxableItems() as $Item) {
                $roundingTypes[$Item->getTaxRate()] = $Item->getRoundingType();
            }

            return $roundingTypes;
        }

        /**
         * 複数配送かどうかの判定を行う.
         *
         * @return boolean
         */
        public function isMultiple()
        {
            $Shippings = [];
            // クエリビルダ使用時に絞り込まれる場合があるため,
            // getShippingsではなくOrderItem経由でShippingを取得する.
            foreach ($this->getOrderItems() as $OrderItem) {
                if ($Shipping = $OrderItem->getShipping()) {
                    $id = $Shipping->getId();
                    if (isset($Shippings[$id])) {
                        continue;
                    }
                    $Shippings[$id] = $Shipping;
                }
            }

            return count($Shippings) > 1 ? true : false;
        }

        /**
         * 対象となるお届け先情報を取得
         *
         * @param integer $shippingId
         *
         * @return \Eccube\Entity\Shipping|null
         */
        public function findShipping($shippingId)
        {
            foreach ($this->getShippings() as $Shipping) {
                if ($Shipping->getId() == $shippingId) {
                    return $Shipping;
                }
            }

            return null;
        }

        /**
         * この注文の保持する販売種別を取得します.
         *
         * @return \Eccube\Entity\Master\SaleType[] 一意な販売種別の配列
         */
        public function getSaleTypes()
        {
            $saleTypes = [];
            foreach ($this->getOrderItems() as $OrderItem) {
                /* @var $ProductClass \Eccube\Entity\ProductClass */
                $ProductClass = $OrderItem->getProductClass();
                if ($ProductClass) {
                    $saleTypes[] = $ProductClass->getSaleType();
                }
            }

            return array_unique($saleTypes);
        }

        /**
         * 同じ規格の商品の個数をまとめた受注明細を取得
         *
         * @return OrderItem[]
         */
        public function getMergedProductOrderItems()
        {
            $ProductOrderItems = $this->getProductOrderItems();
            $orderItemArray = [];
            /** @var OrderItem $ProductOrderItem */
            foreach ($ProductOrderItems as $ProductOrderItem) {
                $productClassId = $ProductOrderItem->getProductClass()->getId();
                if (array_key_exists($productClassId, $orderItemArray)) {
                    // 同じ規格の商品がある場合は個数をまとめる
                    /** @var ItemInterface $OrderItem */
                    $OrderItem = $orderItemArray[$productClassId];
                    $quantity = $OrderItem->getQuantity() + $ProductOrderItem->getQuantity();
                    $OrderItem->setQuantity($quantity);
                } else {
                    // 新規規格の商品は新しく追加する
                    $OrderItem = new OrderItem();
                    $OrderItem->copyProperties($ProductOrderItem, ['id']);
                    $orderItemArray[$productClassId] = $OrderItem;
                }
            }

            return array_values($orderItemArray);
        }

        /**
         * 合計金額を計算
         *
         * @return string
         *
         * @deprecated
         */
        public function getTotalPrice()
        {
            @trigger_error('The ' . __METHOD__ . ' method is deprecated.', E_USER_DEPRECATED);

            return $this->getPaymentTotal();
        }

        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string|null
         *
         * @ORM\Column(name="pre_order_id", type="string", length=255, nullable=true)
         */
        private $pre_order_id;

        /**
         * @var string|null
         *
         * @ORM\Column(name="order_no", type="string", length=255, nullable=true)
         */
        private $order_no;

        /**
         * @var string|null
         *
         * @ORM\Column(name="message", type="string", length=4000, nullable=true)
         */
        private $message;

        /**
         * @var string|null
         *
         * @ORM\Column(name="name01", type="string", length=255)
         */
        private $name01;

        /**
         * @var string|null
         *
         * @ORM\Column(name="name02", type="string", length=255)
         */
        private $name02;

        /**
         * @var string|null
         *
         * @ORM\Column(name="kana01", type="string", length=255, nullable=true)
         */
        private $kana01;

        /**
         * @var string|null
         *
         * @ORM\Column(name="kana02", type="string", length=255, nullable=true)
         */
        private $kana02;

        /**
         * @var string|null
         *
         * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
         */
        private $company_name;

        /**
         * @var string|null
         *
         * @ORM\Column(name="email", type="string", length=255, nullable=true)
         */
        private $email;

        /**
         * @var string|null
         *
         * @ORM\Column(name="phone_number", type="string", length=14, nullable=true)
         */
        private $phone_number;

        /**
         * @var string|null
         *
         * @ORM\Column(name="postal_code", type="string", length=8, nullable=true)
         */
        private $postal_code;

        /**
         * @var string|null
         *
         * @ORM\Column(name="addr01", type="string", length=255, nullable=true)
         */
        private $addr01;

        /**
         * @var string|null
         *
         * @ORM\Column(name="addr02", type="string", length=255, nullable=true)
         */
        private $addr02;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="birth", type="datetimetz", nullable=true)
         */
        private $birth;

        /**
         * @var string
         *
         * @ORM\Column(name="subtotal", type="decimal", precision=12, scale=2, options={"unsigned":true,"default":0})
         */
        private $subtotal = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="discount", type="decimal", precision=12, scale=2, options={"unsigned":true,"default":0})
         */
        private $discount = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="delivery_fee_total", type="decimal", precision=12, scale=2, options={"unsigned":true,"default":0})
         */
        private $delivery_fee_total = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="charge", type="decimal", precision=12, scale=2, options={"unsigned":true,"default":0})
         */
        private $charge = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="tax", type="decimal", precision=12, scale=2, options={"unsigned":true,"default":0})
         *
         * @deprecated 明細ごとに集計した税額と差異が発生する場合があるため非推奨
         */
        private $tax = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="total", type="decimal", precision=12, scale=2, options={"unsigned":true,"default":0})
         */
        private $total = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="payment_total", type="decimal", precision=12, scale=2, options={"unsigned":true,"default":0})
         */
        private $payment_total = 0;

        /**
         * @var string|null
         *
         * @ORM\Column(name="payment_method", type="string", length=255, nullable=true)
         */
        private $payment_method;

        /**
         * @var string|null
         *
         * @ORM\Column(name="note", type="string", length=4000, nullable=true)
         */
        private $note;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="update_date", type="datetimetz")
         */
        private $update_date;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="order_date", type="datetimetz", nullable=true)
         */
        private $order_date;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="payment_date", type="datetimetz", nullable=true)
         */
        private $payment_date;

        /**
         * @var string|null
         *
         * @ORM\Column(name="currency_code", type="string", nullable=true)
         */
        private $currency_code;

        /**
         * 注文完了画面に表示するメッセージ
         *
         * プラグインから注文完了時にメッセージを表示したい場合, このフィールドにセットすることで, 注文完了画面で表示されます。
         * 複数のプラグインから利用されるため, appendCompleteMesssage()で追加してください.
         * 表示する際にHTMLは利用可能です。
         *
         * @var string|null
         *
         * @ORM\Column(name="complete_message", type="text", nullable=true)
         */
        private $complete_message;

        /**
         * 注文完了メールに表示するメッセージ
         *
         * プラグインから注文完了メールにメッセージを表示したい場合, このフィールドにセットすることで, 注文完了メールで表示されます。
         * 複数のプラグインから利用されるため, appendCompleteMailMesssage()で追加してください.
         *
         * @var string|null
         *
         * @ORM\Column(name="complete_mail_message", type="text", nullable=true)
         */
        private $complete_mail_message;

        /**
         * @var \Doctrine\Common\Collections\Collection|OrderItem[]
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\OrderItem", mappedBy="Order", cascade={"persist","remove"})
         */
        private $OrderItems;

        /**
         * @var \Doctrine\Common\Collections\Collection|Shipping[]
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\Shipping", mappedBy="Order", cascade={"persist","remove"})
         */
        private $Shippings;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\MailHistory", mappedBy="Order", cascade={"remove"})
         * @ORM\OrderBy({
         *     "send_date"="DESC"
         * })
         */
        private $MailHistories;

        /**
         * @var \Eccube\Entity\Customer
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Customer", inversedBy="Orders")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
         * })
         */
        private $Customer;

        /**
         * @var \Eccube\Entity\Master\Country
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Country")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
         * })
         */
        private $Country;

        /**
         * @var \Eccube\Entity\Master\Pref
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Pref")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="pref_id", referencedColumnName="id")
         * })
         */
        private $Pref;

        /**
         * @var \Eccube\Entity\Master\Sex
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="sex_id", referencedColumnName="id")
         * })
         */
        private $Sex;

        /**
         * @var \Eccube\Entity\Master\Job
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Job")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="job_id", referencedColumnName="id")
         * })
         */
        private $Job;

        /**
         * @var \Eccube\Entity\Payment
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Payment")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
         * })
         */
        private $Payment;

        /**
         * @var \Eccube\Entity\Master\DeviceType
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\DeviceType")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="device_type_id", referencedColumnName="id")
         * })
         */
        private $DeviceType;

        /**
         * OrderStatusより先にプロパティを定義しておかないとセットされなくなる
         *
         * @var \Eccube\Entity\Master\CustomerOrderStatus
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\CustomerOrderStatus")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="order_status_id", referencedColumnName="id")
         * })
         */
        private $CustomerOrderStatus;

        /**
         * OrderStatusより先にプロパティを定義しておかないとセットされなくなる
         *
         * @var \Eccube\Entity\Master\OrderStatusColor
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\OrderStatusColor")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="order_status_id", referencedColumnName="id")
         * })
         */
        private $OrderStatusColor;

        /**
         * @var \Eccube\Entity\Master\OrderStatus
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\OrderStatus")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="order_status_id", referencedColumnName="id")
         * })
         */
        private $OrderStatus;

        /**
         * Constructor
         */
        public function __construct(Master\OrderStatus $orderStatus = null)
        {
            $this->setDiscount(0)
                ->setSubtotal(0)
                ->setTotal(0)
                ->setPaymentTotal(0)
                ->setCharge(0)
                ->setTax(0)
                ->setDeliveryFeeTotal(0)
                ->setOrderStatus($orderStatus);

            $this->OrderItems = new \Doctrine\Common\Collections\ArrayCollection();
            $this->Shippings = new \Doctrine\Common\Collections\ArrayCollection();
            $this->MailHistories = new \Doctrine\Common\Collections\ArrayCollection();
        }

        /**
         * Clone
         */
        public function __clone()
        {
            $OriginOrderItems = $this->OrderItems;
            $OrderItems = new ArrayCollection();
            foreach ($this->OrderItems as $OrderItem) {
                $OrderItems->add(clone $OrderItem);
            }
            $this->OrderItems = $OrderItems;

//            // ShippingとOrderItemが循環参照するため, 手動でヒモ付を変更する.
//            $Shippings = new ArrayCollection();
//            foreach ($this->Shippings as $Shipping) {
//                $CloneShipping = clone $Shipping;
//                foreach ($OriginOrderItems as $OrderItem) {
//                    //$CloneShipping->removeOrderItem($OrderItem);
//                }
//                foreach ($this->OrderItems as $OrderItem) {
//                    if ($OrderItem->getShipping() && $OrderItem->getShipping()->getId() == $Shipping->getId()) {
//                        $OrderItem->setShipping($CloneShipping);
//                    }
//                    $CloneShipping->addOrderItem($OrderItem);
//                }
//                $Shippings->add($CloneShipping);
//            }
//            $this->Shippings = $Shippings;
        }

        /**
         * Get id.
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set preOrderId.
         *
         * @param string|null $preOrderId
         *
         * @return Order
         */
        public function setPreOrderId($preOrderId = null)
        {
            $this->pre_order_id = $preOrderId;

            return $this;
        }

        /**
         * Get preOrderId.
         *
         * @return string|null
         */
        public function getPreOrderId()
        {
            return $this->pre_order_id;
        }

        /**
         * Set orderNo
         *
         * @param string|null $orderNo
         *
         * @return Order
         */
        public function setOrderNo($orderNo = null)
        {
            $this->order_no = $orderNo;

            return $this;
        }

        /**
         * Get orderNo
         *
         * @return string|null
         */
        public function getOrderNo()
        {
            return $this->order_no;
        }

        /**
         * Set message.
         *
         * @param string|null $message
         *
         * @return Order
         */
        public function setMessage($message = null)
        {
            $this->message = $message;

            return $this;
        }

        /**
         * Get message.
         *
         * @return string|null
         */
        public function getMessage()
        {
            return $this->message;
        }

        /**
         * Set name01.
         *
         * @param string|null $name01
         *
         * @return Order
         */
        public function setName01($name01 = null)
        {
            $this->name01 = $name01;

            return $this;
        }

        /**
         * Get name01.
         *
         * @return string|null
         */
        public function getName01()
        {
            return $this->name01;
        }

        /**
         * Set name02.
         *
         * @param string|null $name02
         *
         * @return Order
         */
        public function setName02($name02 = null)
        {
            $this->name02 = $name02;

            return $this;
        }

        /**
         * Get name02.
         *
         * @return string|null
         */
        public function getName02()
        {
            return $this->name02;
        }

        /**
         * Set kana01.
         *
         * @param string|null $kana01
         *
         * @return Order
         */
        public function setKana01($kana01 = null)
        {
            $this->kana01 = $kana01;

            return $this;
        }

        /**
         * Get kana01.
         *
         * @return string|null
         */
        public function getKana01()
        {
            return $this->kana01;
        }

        /**
         * Set kana02.
         *
         * @param string|null $kana02
         *
         * @return Order
         */
        public function setKana02($kana02 = null)
        {
            $this->kana02 = $kana02;

            return $this;
        }

        /**
         * Get kana02.
         *
         * @return string|null
         */
        public function getKana02()
        {
            return $this->kana02;
        }

        /**
         * Set companyName.
         *
         * @param string|null $companyName
         *
         * @return Order
         */
        public function setCompanyName($companyName = null)
        {
            $this->company_name = $companyName;

            return $this;
        }

        /**
         * Get companyName.
         *
         * @return string|null
         */
        public function getCompanyName()
        {
            return $this->company_name;
        }

        /**
         * Set email.
         *
         * @param string|null $email
         *
         * @return Order
         */
        public function setEmail($email = null)
        {
            $this->email = $email;

            return $this;
        }

        /**
         * Get email.
         *
         * @return string|null
         */
        public function getEmail()
        {
            return $this->email;
        }

        /**
         * Set phone_number.
         *
         * @param string|null $phone_number
         *
         * @return Order
         */
        public function setPhoneNumber($phone_number = null)
        {
            $this->phone_number = $phone_number;

            return $this;
        }

        /**
         * Get phone_number.
         *
         * @return string|null
         */
        public function getPhoneNumber()
        {
            return $this->phone_number;
        }

        /**
         * Set postal_code.
         *
         * @param string|null $postal_code
         *
         * @return Order
         */
        public function setPostalCode($postal_code = null)
        {
            $this->postal_code = $postal_code;

            return $this;
        }

        /**
         * Get postal_code.
         *
         * @return string|null
         */
        public function getPostalCode()
        {
            return $this->postal_code;
        }

        /**
         * Set addr01.
         *
         * @param string|null $addr01
         *
         * @return Order
         */
        public function setAddr01($addr01 = null)
        {
            $this->addr01 = $addr01;

            return $this;
        }

        /**
         * Get addr01.
         *
         * @return string|null
         */
        public function getAddr01()
        {
            return $this->addr01;
        }

        /**
         * Set addr02.
         *
         * @param string|null $addr02
         *
         * @return Order
         */
        public function setAddr02($addr02 = null)
        {
            $this->addr02 = $addr02;

            return $this;
        }

        /**
         * Get addr02.
         *
         * @return string|null
         */
        public function getAddr02()
        {
            return $this->addr02;
        }

        /**
         * Set birth.
         *
         * @param \DateTime|null $birth
         *
         * @return Order
         */
        public function setBirth($birth = null)
        {
            $this->birth = $birth;

            return $this;
        }

        /**
         * Get birth.
         *
         * @return \DateTime|null
         */
        public function getBirth()
        {
            return $this->birth;
        }

        /**
         * Set subtotal.
         *
         * @param string $subtotal
         *
         * @return Order
         */
        public function setSubtotal($subtotal)
        {
            $this->subtotal = $subtotal;

            return $this;
        }

        /**
         * Get subtotal.
         *
         * @return string
         */
        public function getSubtotal()
        {
            return $this->subtotal;
        }

        /**
         * Set discount.
         *
         * @param string $discount
         *
         * @return Order
         */
        public function setDiscount($discount)
        {
            $this->discount = $discount;

            return $this;
        }

        /**
         * Get discount.
         *
         * @return string
         * @deprecated 4.0.3 から値引きは課税値引きと 非課税・不課税の値引きの2種に分かれる. 課税値引きについてはgetTaxableDiscountを利用してください.
         *
         */
        public function getDiscount()
        {
            return $this->discount;
        }

        /**
         * Set deliveryFeeTotal.
         *
         * @param string $deliveryFeeTotal
         *
         * @return Order
         */
        public function setDeliveryFeeTotal($deliveryFeeTotal)
        {
            $this->delivery_fee_total = $deliveryFeeTotal;

            return $this;
        }

        /**
         * Get deliveryFeeTotal.
         *
         * @return string
         */
        public function getDeliveryFeeTotal()
        {
            return $this->delivery_fee_total;
        }

        /**
         * Set charge.
         *
         * @param string $charge
         *
         * @return Order
         */
        public function setCharge($charge)
        {
            $this->charge = $charge;

            return $this;
        }

        /**
         * Get charge.
         *
         * @return string
         */
        public function getCharge()
        {
            return $this->charge;
        }

        /**
         * Set tax.
         *
         * @param string $tax
         *
         * @return Order
         *
         * @deprecated 明細ごとに集計した税額と差異が発生する場合があるため非推奨
         */
        public function setTax($tax)
        {
            $this->tax = $tax;

            return $this;
        }

        /**
         * Get tax.
         *
         * @return string
         *
         * @deprecated 明細ごとに集計した税額と差異が発生する場合があるため非推奨
         */
        public function getTax()
        {
            return $this->tax;
        }

        /**
         * Set total.
         *
         * @param string $total
         *
         * @return Order
         */
        public function setTotal($total)
        {
            $this->total = $total;

            return $this;
        }

        /**
         * Get total.
         *
         * @return string
         */
        public function getTotal()
        {
            return $this->total;
        }

        /**
         * Set paymentTotal.
         *
         * @param string $paymentTotal
         *
         * @return Order
         */
        public function setPaymentTotal($paymentTotal)
        {
            $this->payment_total = $paymentTotal;

            return $this;
        }

        /**
         * Get paymentTotal.
         *
         * @return string
         */
        public function getPaymentTotal()
        {
            return $this->payment_total;
        }

        /**
         * Set paymentMethod.
         *
         * @param string|null $paymentMethod
         *
         * @return Order
         */
        public function setPaymentMethod($paymentMethod = null)
        {
            $this->payment_method = $paymentMethod;

            return $this;
        }

        /**
         * Get paymentMethod.
         *
         * @return string|null
         */
        public function getPaymentMethod()
        {
            return $this->payment_method;
        }

        /**
         * Set note.
         *
         * @param string|null $note
         *
         * @return Order
         */
        public function setNote($note = null)
        {
            $this->note = $note;

            return $this;
        }

        /**
         * Get note.
         *
         * @return string|null
         */
        public function getNote()
        {
            return $this->note;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Order
         */
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime
         */
        public function getCreateDate()
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return Order
         */
        public function setUpdateDate($updateDate)
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime
         */
        public function getUpdateDate()
        {
            return $this->update_date;
        }

        /**
         * Set orderDate.
         *
         * @param \DateTime|null $orderDate
         *
         * @return Order
         */
        public function setOrderDate($orderDate = null)
        {
            $this->order_date = $orderDate;

            return $this;
        }

        /**
         * Get orderDate.
         *
         * @return \DateTime|null
         */
        public function getOrderDate()
        {
            return $this->order_date;
        }

        /**
         * Set paymentDate.
         *
         * @param \DateTime|null $paymentDate
         *
         * @return Order
         */
        public function setPaymentDate($paymentDate = null)
        {
            $this->payment_date = $paymentDate;

            return $this;
        }

        /**
         * Get paymentDate.
         *
         * @return \DateTime|null
         */
        public function getPaymentDate()
        {
            return $this->payment_date;
        }

        /**
         * Get currencyCode.
         *
         * @return string
         */
        public function getCurrencyCode()
        {
            return $this->currency_code;
        }

        /**
         * Set currencyCode.
         *
         * @param string|null $currencyCode
         *
         * @return $this
         */
        public function setCurrencyCode($currencyCode = null)
        {
            $this->currency_code = $currencyCode;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getCompleteMessage()
        {
            return $this->complete_message;
        }

        /**
         * @param string|null $complete_message
         *
         * @return $this
         */
        public function setCompleteMessage($complete_message = null)
        {
            $this->complete_message = $complete_message;

            return $this;
        }

        /**
         * @param string|null $complete_message
         *
         * @return $this
         */
        public function appendCompleteMessage($complete_message = null)
        {
            $this->complete_message .= $complete_message;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getCompleteMailMessage()
        {
            return $this->complete_mail_message;
        }

        /**
         * @param string|null $complete_mail_message
         *
         * @return
         */
        public function setCompleteMailMessage($complete_mail_message = null)
        {
            $this->complete_mail_message = $complete_mail_message;

            return $this;
        }

        /**
         * @param string|null $complete_mail_message
         *
         * @return
         */
        public function appendCompleteMailMessage($complete_mail_message = null)
        {
            $this->complete_mail_message .= $complete_mail_message;

            return $this;
        }

        /**
         * 商品の受注明細を取得
         *
         * @return OrderItem[]
         */
        public function getProductOrderItems()
        {
            $sio = new OrderItemCollection($this->OrderItems->toArray());

            return array_values($sio->getProductClasses()->toArray());
        }

        /**
         * Add orderItem.
         *
         * @param \Eccube\Entity\OrderItem $OrderItem
         *
         * @return Order
         */
        public function addOrderItem(OrderItem $OrderItem)
        {
            $this->OrderItems[] = $OrderItem;

            return $this;
        }

        /**
         * Remove orderItem.
         *
         * @param \Eccube\Entity\OrderItem $OrderItem
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeOrderItem(OrderItem $OrderItem)
        {
            return $this->OrderItems->removeElement($OrderItem);
        }

        /**
         * Get orderItems.
         *
         * @return \Doctrine\Common\Collections\Collection|OrderItem[]
         */
        public function getOrderItems()
        {
            return $this->OrderItems;
        }

        /**
         * Sorted to getOrderItems()
         *
         * @return ItemCollection
         */
        public function getItems()
        {
            return (new ItemCollection($this->getOrderItems()))->sort();
        }

        /**
         * Add shipping.
         *
         * @param \Eccube\Entity\Shipping $Shipping
         *
         * @return Order
         */
        public function addShipping(Shipping $Shipping)
        {
            $this->Shippings[] = $Shipping;

            return $this;
        }

        /**
         * Remove shipping.
         *
         * @param \Eccube\Entity\Shipping $Shipping
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeShipping(Shipping $Shipping)
        {
            return $this->Shippings->removeElement($Shipping);
        }

        /**
         * Get shippings.
         *
         * @return \Doctrine\Common\Collections\Collection|\Eccube\Entity\Shipping[]
         */
        public function getShippings()
        {
            $criteria = Criteria::create()
                ->orderBy(['name01' => Criteria::ASC, 'name02' => Criteria::ASC, 'id' => Criteria::ASC]);

            return $this->Shippings->matching($criteria);
        }

        /**
         * Add mailHistory.
         *
         * @param \Eccube\Entity\MailHistory $mailHistory
         *
         * @return Order
         */
        public function addMailHistory(MailHistory $mailHistory)
        {
            $this->MailHistories[] = $mailHistory;

            return $this;
        }

        /**
         * Remove mailHistory.
         *
         * @param \Eccube\Entity\MailHistory $mailHistory
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeMailHistory(MailHistory $mailHistory)
        {
            return $this->MailHistories->removeElement($mailHistory);
        }

        /**
         * Get mailHistories.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getMailHistories()
        {
            return $this->MailHistories;
        }

        /**
         * Set customer.
         *
         * @param \Eccube\Entity\Customer|null $customer
         *
         * @return Order
         */
        public function setCustomer(Customer $customer = null)
        {
            $this->Customer = $customer;

            return $this;
        }

        /**
         * Get customer.
         *
         * @return \Eccube\Entity\Customer|null
         */
        public function getCustomer()
        {
            return $this->Customer;
        }

        /**
         * Set country.
         *
         * @param \Eccube\Entity\Master\Country|null $country
         *
         * @return Order
         */
        public function setCountry(Master\Country $country = null)
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         *
         * @return \Eccube\Entity\Master\Country|null
         */
        public function getCountry()
        {
            return $this->Country;
        }

        /**
         * Set pref.
         *
         * @param \Eccube\Entity\Master\Pref|null $pref
         *
         * @return Order
         */
        public function setPref(Master\Pref $pref = null)
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         *
         * @return \Eccube\Entity\Master\Pref|null
         */
        public function getPref()
        {
            return $this->Pref;
        }

        /**
         * Set sex.
         *
         * @param \Eccube\Entity\Master\Sex|null $sex
         *
         * @return Order
         */
        public function setSex(Master\Sex $sex = null)
        {
            $this->Sex = $sex;

            return $this;
        }

        /**
         * Get sex.
         *
         * @return \Eccube\Entity\Master\Sex|null
         */
        public function getSex()
        {
            return $this->Sex;
        }

        /**
         * Set job.
         *
         * @param \Eccube\Entity\Master\Job|null $job
         *
         * @return Order
         */
        public function setJob(Master\Job $job = null)
        {
            $this->Job = $job;

            return $this;
        }

        /**
         * Get job.
         *
         * @return \Eccube\Entity\Master\Job|null
         */
        public function getJob()
        {
            return $this->Job;
        }

        /**
         * Set payment.
         *
         * @param \Eccube\Entity\Payment|null $payment
         *
         * @return Order
         */
        public function setPayment(Payment $payment = null)
        {
            $this->Payment = $payment;

            return $this;
        }

        /**
         * Get payment.
         *
         * @return \Eccube\Entity\Payment|null
         */
        public function getPayment()
        {
            return $this->Payment;
        }

        /**
         * Set deviceType.
         *
         * @param \Eccube\Entity\Master\DeviceType|null $deviceType
         *
         * @return Order
         */
        public function setDeviceType(Master\DeviceType $deviceType = null)
        {
            $this->DeviceType = $deviceType;

            return $this;
        }

        /**
         * Get deviceType.
         *
         * @return \Eccube\Entity\Master\DeviceType|null
         */
        public function getDeviceType()
        {
            return $this->DeviceType;
        }

        /**
         * Set customerOrderStatus.
         *
         * @param \Eccube\Entity\Master\CustomerOrderStatus|null $customerOrderStatus
         *
         * @return Order
         */
        public function setCustomerOrderStatus(Master\CustomerOrderStatus $customerOrderStatus = null)
        {
            $this->CustomerOrderStatus = $customerOrderStatus;

            return $this;
        }

        /**
         * Get customerOrderStatus.
         *
         * @return \Eccube\Entity\Master\CustomerOrderStatus|null
         */
        public function getCustomerOrderStatus()
        {
            return $this->CustomerOrderStatus;
        }

        /**
         * Set orderStatusColor.
         *
         * @param \Eccube\Entity\Master\OrderStatusColor|null $orderStatusColor
         *
         * @return Order
         */
        public function setOrderStatusColor(Master\OrderStatusColor $orderStatusColor = null)
        {
            $this->OrderStatusColor = $orderStatusColor;

            return $this;
        }

        /**
         * Get orderStatusColor.
         *
         * @return \Eccube\Entity\Master\OrderStatusColor|null
         */
        public function getOrderStatusColor()
        {
            return $this->OrderStatusColor;
        }

        /**
         * Set orderStatus.
         *
         * @param \Eccube\Entity\Master\OrderStatus|object|null $orderStatus
         *
         * @return Order
         */
        public function setOrderStatus(Master\OrderStatus $orderStatus = null)
        {
            $this->OrderStatus = $orderStatus;

            return $this;
        }

        /**
         * Get orderStatus.
         *
         * @return \Eccube\Entity\Master\OrderStatus|null
         */
        public function getOrderStatus()
        {
            return $this->OrderStatus;
        }

        /**
         * @param ItemInterface $item
         */
        public function addItem(ItemInterface $item)
        {
            $this->OrderItems->add($item);
        }

        public function getQuantity()
        {
            $quantity = 0;
            foreach ($this->getItems() as $item) {
                $quantity += $item->getQuantity();
            }

            return $quantity;
        }
    }
}
