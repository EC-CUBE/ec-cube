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
use Doctrine\ORM\PersistentCollection;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Master\TaxType;
use Eccube\Repository\OrderRepository;
use Eccube\Service\Calculator\OrderItemCollection;
use Eccube\Service\PurchaseFlow\ItemCollection;
use Eccube\Service\TaxRuleService;

if (!class_exists(Order::class)) {
    /**
     * Order
     */
    #[ORM\Table(name: 'dtb_order')]
    #[ORM\Index(columns: ['email'], name: 'dtb_order_email_idx')]
    #[ORM\Index(columns: ['order_date'], name: 'dtb_order_order_date_idx')]
    #[ORM\Index(columns: ['payment_date'], name: 'dtb_order_payment_date_idx')]
    #[ORM\Index(columns: ['update_date'], name: 'dtb_order_update_date_idx')]
    #[ORM\Index(columns: ['order_no'], name: 'dtb_order_order_no_idx')]
    #[ORM\UniqueConstraint(name: 'dtb_order_pre_order_id_idx', columns: ['pre_order_id'])]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: OrderRepository::class)]
    class Order extends AbstractEntity implements PurchaseInterface, ItemHolderInterface
    {
        use NameTrait;
        use PointTrait;

        /**
         * 課税対象の明細を返す.
         *
         * @return OrderItem[]
         */
        public function getTaxableItems(): array
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
         *
         * @return string
         */
        public function getTaxableTotal(): string
        {
            $total = '0';

            foreach ($this->getTaxableItems() as $Item) {
                $total = bcadd($total, $Item->getTotalPrice(), 2);
            }

            return $total;
        }

        /**
         * 課税対象の明細の合計金額を、税率ごとに集計する.
         *
         * @return array<string, string>  [税率 => 合計金額]
         */
        public function getTaxableTotalByTaxRate(): array
        {
            $total = [];

            foreach ($this->getTaxableItems() as $Item) {
                $totalPrice = $Item->getTotalPrice();
                $taxRate = $Item->getTaxRate();
                $total[$taxRate] = isset($total[$taxRate])
                    ? bcadd($total[$taxRate], $totalPrice, 2)
                    : $totalPrice;
            }

            krsort($total, SORT_NUMERIC);

            return $total;
        }

        /**
         * 明細の合計額を税率ごとに集計する.
         *
         * 不課税, 非課税の値引明細は税率ごとに按分する.
         *
         * @return array<string, string>
         */
        public function getTotalByTaxRate(): array
        {
            $roundingTypes = $this->getRoundingTypeByTaxRate();
            $total = [];
            $taxableTotal = $this->getTaxableTotal();
            $taxFreeDiscount = $this->getTaxFreeDiscount();

            foreach ($this->getTaxableTotalByTaxRate() as $rate => $totalPrice) {
                if (bccomp($taxableTotal, '0', 2) !== 0) {
                    // 按分計算: totalPrice - (abs(taxFreeDiscount) * totalPrice / taxableTotal)
                    $absDiscount = ltrim($taxFreeDiscount, '-');
                    $discountPortion = bcdiv(bcmul($absDiscount, $totalPrice, 6), $taxableTotal, 6);
                    $value = bcsub($totalPrice, $discountPortion, 6);
                } else {
                    $value = '0';
                }

                $total[$rate] = TaxRuleService::roundByRoundingType(
                    $value,
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
         * @return array<string, string>
         */
        public function getTaxByTaxRate(): array
        {
            $roundingTypes = $this->getRoundingTypeByTaxRate();
            $tax = [];
            $taxableTotal = $this->getTaxableTotal();
            $taxFreeDiscount = $this->getTaxFreeDiscount();

            foreach ($this->getTaxableTotalByTaxRate() as $rate => $totalPrice) {
                if (!array_key_exists($rate, $roundingTypes) || null === $roundingTypes[$rate]) {
                    continue;
                }

                if (bccomp($taxableTotal, '0', 2) !== 0) {
                    // (totalPrice - abs(taxFreeDiscount) * totalPrice / taxableTotal) * (rate / (100 + rate))
                    $absDiscount = ltrim($taxFreeDiscount, '-');

                    // abs(taxFreeDiscount) * totalPrice / taxableTotal
                    $discountPortion = bcdiv(bcmul($absDiscount, $totalPrice, 6), $taxableTotal, 6);

                    // totalPrice - discountPortion
                    $afterDiscount = bcsub($totalPrice, $discountPortion, 6);

                    // rate / (100 + rate)
                    $rateStr = $rate;
                    $taxRate = bcdiv($rateStr, bcadd('100', $rateStr, 6), 6);

                    // 最終計算
                    $value = bcmul($afterDiscount, $taxRate, 6);
                } else {
                    $value = '0';
                }

                $tax[$rate] = TaxRuleService::roundByRoundingType(
                    $value,
                    $roundingTypes[$rate]->getId()
                );
            }

            ksort($tax);

            return $tax;
        }

        /**
         * 課税対象の値引き明細を返す.
         *
         * @return array<int, OrderItem>
         */
        public function getTaxableDiscountItems(): array
        {
            /** @var OrderItem[] $items */
            $items = (new ItemCollection($this->getTaxableItems()))->sort()->toArray();

            return array_filter($items, function (OrderItem $Item) {
                return $Item->isDiscount();
            });
        }

        /**
         * 課税対象の値引き金額合計を返す.
         *
         * @return string
         */
        public function getTaxableDiscount(): string
        {
            return array_reduce($this->getTaxableDiscountItems(), function ($sum, OrderItem $Item) {
                return bcadd($sum, $Item->getTotalPrice(), 2);
            }, '0');
        }

        /**
         * 非課税・不課税の値引き明細を返す.
         *
         * @return array<int,OrderItem>
         */
        public function getTaxFreeDiscountItems(): array
        {
            /** @var OrderItem[] $items */
            $items = (new ItemCollection($this->getOrderItems()))->sort()->toArray();

            return array_filter($items, function (OrderItem $Item) {
                return $Item->isPoint() || ($Item->isDiscount() && $Item->getTaxType()->getId() != TaxType::TAXATION);
            });
        }

        /**
         * 非課税・不課税の値引き額を返す.
         *
         * @return string
         */
        public function getTaxFreeDiscount(): string
        {
            return array_reduce($this->getTaxFreeDiscountItems(), function ($sum, OrderItem $Item) {
                return bcadd($sum, $Item->getTotalPrice(), 2);
            }, '0');
        }

        /**
         * 税率ごとの丸め規則を取得する.
         *
         * @return array<string, RoundingType|null>
         */
        public function getRoundingTypeByTaxRate(): array
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
         * @return bool
         */
        public function isMultiple(): bool
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
         * @param int $shippingId
         *
         * @return Shipping|null
         */
        public function findShipping($shippingId): ?Shipping
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
         * @return Master\SaleType[] 一意な販売種別の配列
         */
        public function getSaleTypes(): array
        {
            $saleTypes = [];
            foreach ($this->getOrderItems() as $OrderItem) {
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
        public function getMergedProductOrderItems(): array
        {
            $ProductOrderItems = $this->getProductOrderItems();
            $orderItemArray = [];
            /** @var OrderItem $ProductOrderItem */
            foreach ($ProductOrderItems as $ProductOrderItem) {
                $productClassId = $ProductOrderItem->getProductClass()->getId();
                if (array_key_exists($productClassId, $orderItemArray)) {
                    // 同じ規格の商品がある場合は個数をまとめる
                    /** @var OrderItem $OrderItem */
                    $OrderItem = $orderItemArray[$productClassId];
                    $quantity = bcadd($OrderItem->getQuantity(), $ProductOrderItem->getQuantity());
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
        public function getTotalPrice(): string
        {
            @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);

            return $this->getPaymentTotal();
        }

        /**
         * @var int|null
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'pre_order_id', type: 'string', length: 255, nullable: true)]
        private $pre_order_id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'order_no', type: 'string', length: 255, nullable: true)]
        private $order_no;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'message', type: 'string', length: 4000, nullable: true)]
        private $message;

        /**
         * @var string
         */
        #[ORM\Column(name: 'name01', type: 'string', length: 255)]
        private $name01;

        /**
         * @var string
         */
        #[ORM\Column(name: 'name02', type: 'string', length: 255)]
        private $name02;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'kana01', type: 'string', length: 255, nullable: true)]
        private $kana01;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'kana02', type: 'string', length: 255, nullable: true)]
        private $kana02;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'company_name', type: 'string', length: 255, nullable: true)]
        private $company_name;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'email', type: 'string', length: 255, nullable: true)]
        private $email;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'phone_number', type: 'string', length: 14, nullable: true)]
        private $phone_number;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'postal_code', type: 'string', length: 8, nullable: true)]
        private $postal_code;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'addr01', type: 'string', length: 255, nullable: true)]
        private $addr01;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'addr02', type: 'string', length: 255, nullable: true)]
        private $addr02;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'birth', type: 'datetimetz', nullable: true)]
        private $birth;

        /**
         * @var string
         */
        #[ORM\Column(name: 'subtotal', type: 'decimal', precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
        private $subtotal = '0';

        /**
         * @var string
         */
        #[ORM\Column(name: 'discount', type: 'decimal', precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
        private $discount = '0';

        /**
         * @var string
         */
        #[ORM\Column(name: 'delivery_fee_total', type: 'decimal', precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
        private $delivery_fee_total = '0';

        /**
         * @var string
         */
        #[ORM\Column(name: 'charge', type: 'decimal', precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
        private $charge = '0';

        /**
         * @var string
         *
         * @deprecated 明細ごとに集計した税額と差異が発生する場合があるため非推奨
         */
        #[ORM\Column(name: 'tax', type: 'decimal', precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
        private $tax = '0';

        /**
         * @var string
         */
        #[ORM\Column(name: 'total', type: 'decimal', precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
        private $total = '0';

        /**
         * @var string
         */
        #[ORM\Column(name: 'payment_total', type: 'decimal', precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
        private $payment_total = '0';

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'payment_method', type: 'string', length: 255, nullable: true)]
        private $payment_method;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'note', type: 'string', length: 4000, nullable: true)]
        private $note;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: 'datetimetz')]
        private $create_date;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'update_date', type: 'datetimetz')]
        private $update_date;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'order_date', type: 'datetimetz', nullable: true)]
        private $order_date;

        /**
         * @var \DateTime|null
         */
        #[ORM\Column(name: 'payment_date', type: 'datetimetz', nullable: true)]
        private $payment_date;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'currency_code', type: 'string', nullable: true)]
        private $currency_code;

        /**
         * 注文完了画面に表示するメッセージ
         *
         * プラグインから注文完了時にメッセージを表示したい場合, このフィールドにセットすることで, 注文完了画面で表示されます。
         * 複数のプラグインから利用されるため, appendCompleteMesssage()で追加してください.
         * 表示する際にHTMLは利用可能です。
         *
         * @var string|null
         */
        #[ORM\Column(name: 'complete_message', type: 'text', nullable: true)]
        private $complete_message;

        /**
         * 注文完了メールに表示するメッセージ
         *
         * プラグインから注文完了メールにメッセージを表示したい場合, このフィールドにセットすることで, 注文完了メールで表示されます。
         * 複数のプラグインから利用されるため, appendCompleteMailMesssage()で追加してください.
         *
         * @var string|null
         */
        #[ORM\Column(name: 'complete_mail_message', type: 'text', nullable: true)]
        private $complete_mail_message;

        /**
         * @var \Doctrine\Common\Collections\Collection<int,OrderItem>
         */
        #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'Order', cascade: ['persist', 'remove'])]
        private $OrderItems;

        /**
         * @var \Doctrine\Common\Collections\Collection<int,Shipping>
         */
        #[ORM\OneToMany(targetEntity: Shipping::class, mappedBy: 'Order', cascade: ['persist', 'remove'])]
        private $Shippings;

        /**
         * @var \Doctrine\Common\Collections\Collection<int,MailHistory>
         */
        #[ORM\OneToMany(targetEntity: MailHistory::class, mappedBy: 'Order', cascade: ['remove'])]
        #[ORM\OrderBy(['send_date' => 'DESC'])]
        private $MailHistories;

        /**
         * @var Customer|null
         */
        #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'Orders')]
        #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
        private $Customer;

        /**
         * @var Master\Country|null
         */
        #[ORM\ManyToOne(targetEntity: Master\Country::class)]
        #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
        private $Country;

        /**
         * @var Master\Pref|null
         */
        #[ORM\ManyToOne(targetEntity: Master\Pref::class)]
        #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
        private $Pref;

        /**
         * @var Master\Sex|null
         */
        #[ORM\ManyToOne(targetEntity: Master\Sex::class)]
        #[ORM\JoinColumn(name: 'sex_id', referencedColumnName: 'id')]
        private $Sex;

        /**
         * @var Master\Job|null
         */
        #[ORM\ManyToOne(targetEntity: Master\Job::class)]
        #[ORM\JoinColumn(name: 'job_id', referencedColumnName: 'id')]
        private $Job;

        /**
         * @var Payment|null
         */
        #[ORM\ManyToOne(targetEntity: Payment::class)]
        #[ORM\JoinColumn(name: 'payment_id', referencedColumnName: 'id')]
        private $Payment;

        /**
         * @var Master\DeviceType|null
         */
        #[ORM\ManyToOne(targetEntity: Master\DeviceType::class)]
        #[ORM\JoinColumn(name: 'device_type_id', referencedColumnName: 'id')]
        private $DeviceType;

        /**
         * OrderStatusより先にプロパティを定義しておかないとセットされなくなる
         *
         * @var Master\CustomerOrderStatus|null
         */
        #[ORM\ManyToOne(targetEntity: Master\CustomerOrderStatus::class)]
        #[ORM\JoinColumn(name: 'order_status_id', referencedColumnName: 'id')]
        private $CustomerOrderStatus;

        /**
         * OrderStatusより先にプロパティを定義しておかないとセットされなくなる
         *
         * @var Master\OrderStatusColor|null
         */
        #[ORM\ManyToOne(targetEntity: Master\OrderStatusColor::class)]
        #[ORM\JoinColumn(name: 'order_status_id', referencedColumnName: 'id')]
        private $OrderStatusColor;

        /**
         * @var Master\OrderStatus|null
         */
        #[ORM\ManyToOne(targetEntity: Master\OrderStatus::class)]
        #[ORM\JoinColumn(name: 'order_status_id', referencedColumnName: 'id')]
        private $OrderStatus;

        /**
         * Constructor
         */
        public function __construct(?Master\OrderStatus $orderStatus = null)
        {
            $this->setDiscount('0')
                ->setSubtotal('0')
                ->setTotal('0')
                ->setPaymentTotal('0')
                ->setCharge('0')
                ->setTax('0')
                ->setDeliveryFeeTotal('0')
                ->setOrderStatus($orderStatus);

            $this->OrderItems = new ArrayCollection();
            $this->Shippings = new ArrayCollection();
            $this->MailHistories = new ArrayCollection();
        }

        /**
         * Clone
         */
        public function __clone()
        {
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
         * @return int|null
         */
        public function getId(): ?int
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
        public function setPreOrderId($preOrderId = null): Order
        {
            $this->pre_order_id = $preOrderId;

            return $this;
        }

        /**
         * Get preOrderId.
         *
         * @return string|null
         */
        public function getPreOrderId(): ?string
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
        public function setOrderNo($orderNo = null): Order
        {
            $this->order_no = $orderNo;

            return $this;
        }

        /**
         * Get orderNo
         *
         * @return string|null
         */
        public function getOrderNo(): ?string
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
        public function setMessage($message = null): Order
        {
            $this->message = $message;

            return $this;
        }

        /**
         * Get message.
         *
         * @return string|null
         */
        public function getMessage(): ?string
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
        public function setName01($name01 = null): Order
        {
            $this->name01 = $name01;

            return $this;
        }

        /**
         * Get name01.
         *
         * @return string|null
         */
        public function getName01(): ?string
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
        public function setName02($name02 = null): Order
        {
            $this->name02 = $name02;

            return $this;
        }

        /**
         * Get name02.
         *
         * @return string|null
         */
        public function getName02(): ?string
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
        public function setKana01($kana01 = null): Order
        {
            $this->kana01 = $kana01;

            return $this;
        }

        /**
         * Get kana01.
         *
         * @return string|null
         */
        public function getKana01(): ?string
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
        public function setKana02($kana02 = null): Order
        {
            $this->kana02 = $kana02;

            return $this;
        }

        /**
         * Get kana02.
         *
         * @return string|null
         */
        public function getKana02(): ?string
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
        public function setCompanyName($companyName = null): Order
        {
            $this->company_name = $companyName;

            return $this;
        }

        /**
         * Get companyName.
         *
         * @return string|null
         */
        public function getCompanyName(): ?string
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
        public function setEmail($email = null): Order
        {
            $this->email = $email;

            return $this;
        }

        /**
         * Get email.
         *
         * @return string|null
         */
        public function getEmail(): ?string
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
        public function setPhoneNumber($phone_number = null): Order
        {
            $this->phone_number = $phone_number;

            return $this;
        }

        /**
         * Get phone_number.
         *
         * @return string|null
         */
        public function getPhoneNumber(): ?string
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
        public function setPostalCode($postal_code = null): Order
        {
            $this->postal_code = $postal_code;

            return $this;
        }

        /**
         * Get postal_code.
         *
         * @return string|null
         */
        public function getPostalCode(): ?string
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
        public function setAddr01($addr01 = null): Order
        {
            $this->addr01 = $addr01;

            return $this;
        }

        /**
         * Get addr01.
         *
         * @return string|null
         */
        public function getAddr01(): ?string
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
        public function setAddr02($addr02 = null): Order
        {
            $this->addr02 = $addr02;

            return $this;
        }

        /**
         * Get addr02.
         *
         * @return string|null
         */
        public function getAddr02(): ?string
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
        public function setBirth($birth = null): Order
        {
            $this->birth = $birth;

            return $this;
        }

        /**
         * Get birth.
         *
         * @return \DateTime|null
         */
        public function getBirth(): ?\DateTime
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
        public function setSubtotal($subtotal): Order
        {
            $this->subtotal = $subtotal;

            return $this;
        }

        /**
         * Get subtotal.
         *
         * @return string
         */
        public function getSubtotal(): string
        {
            return $this->subtotal;
        }

        /**
         * Set discount.
         *
         * @param string $discount
         *
         * @return static
         */
        #[\Override]
        public function setDiscount($discount): static
        {
            $this->discount = $discount;

            return $this;
        }

        /**
         * Get discount.
         *
         * @return string
         *
         * @deprecated 4.0.3 から値引きは課税値引きと 非課税・不課税の値引きの2種に分かれる. 課税値引きについてはgetTaxableDiscountを利用してください.
         */
        public function getDiscount(): string
        {
            return $this->discount;
        }

        /**
         * Set deliveryFeeTotal.
         *
         * @param string $deliveryFeeTotal
         *
         * @return $this
         */
        #[\Override]
        public function setDeliveryFeeTotal($deliveryFeeTotal): static
        {
            $this->delivery_fee_total = $deliveryFeeTotal;

            return $this;
        }

        /**
         * Get deliveryFeeTotal.
         *
         * @return string
         */
        #[\Override]
        public function getDeliveryFeeTotal(): string
        {
            return $this->delivery_fee_total;
        }

        /**
         * Set charge.
         *
         * @param string $charge
         *
         * @return $this
         */
        #[\Override]
        public function setCharge($charge): static
        {
            $this->charge = $charge;

            return $this;
        }

        /**
         * Get charge.
         *
         * @return string
         */
        public function getCharge(): string
        {
            return $this->charge;
        }

        /**
         * Set tax.
         *
         * @param string $tax
         *
         * @return $this
         *
         * @deprecated 明細ごとに集計した税額と差異が発生する場合があるため非推奨
         */
        #[\Override]
        public function setTax($tax): static
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
        public function getTax(): string
        {
            return $this->tax;
        }

        /**
         * Set total.
         *
         * @param string $total
         *
         * @return static
         */
        #[\Override]
        public function setTotal($total): static
        {
            $this->total = $total;

            return $this;
        }

        /**
         * Get total.
         *
         * @return string
         */
        #[\Override]
        public function getTotal(): string
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
        public function setPaymentTotal($paymentTotal): Order
        {
            $this->payment_total = $paymentTotal;

            return $this;
        }

        /**
         * Get paymentTotal.
         *
         * @return string
         */
        public function getPaymentTotal(): string
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
        public function setPaymentMethod($paymentMethod = null): Order
        {
            $this->payment_method = $paymentMethod;

            return $this;
        }

        /**
         * Get paymentMethod.
         *
         * @return string|null
         */
        public function getPaymentMethod(): ?string
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
        public function setNote($note = null): Order
        {
            $this->note = $note;

            return $this;
        }

        /**
         * Get note.
         *
         * @return string|null
         */
        public function getNote(): ?string
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
        public function setCreateDate($createDate): Order
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime|null
         */
        public function getCreateDate(): ?\DateTime
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
        public function setUpdateDate($updateDate): Order
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime|null
         */
        public function getUpdateDate(): ?\DateTime
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
        public function setOrderDate($orderDate = null): Order
        {
            $this->order_date = $orderDate;

            return $this;
        }

        /**
         * Get orderDate.
         *
         * @return \DateTime|null
         */
        public function getOrderDate(): ?\DateTime
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
        public function setPaymentDate($paymentDate = null): Order
        {
            $this->payment_date = $paymentDate;

            return $this;
        }

        /**
         * Get paymentDate.
         *
         * @return \DateTime|null
         */
        public function getPaymentDate(): ?\DateTime
        {
            return $this->payment_date;
        }

        /**
         * Get currencyCode.
         *
         * @return string
         */
        public function getCurrencyCode(): string
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
        public function setCurrencyCode($currencyCode = null): static
        {
            $this->currency_code = $currencyCode;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getCompleteMessage(): ?string
        {
            return $this->complete_message;
        }

        /**
         * @param string|null $complete_message
         *
         * @return $this
         */
        public function setCompleteMessage($complete_message = null): static
        {
            $this->complete_message = $complete_message;

            return $this;
        }

        /**
         * @param string|null $complete_message
         *
         * @return $this
         */
        public function appendCompleteMessage($complete_message = null): static
        {
            $this->complete_message .= $complete_message;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getCompleteMailMessage(): ?string
        {
            return $this->complete_mail_message;
        }

        /**
         * @param string|null $complete_mail_message
         *
         * @return self
         */
        public function setCompleteMailMessage($complete_mail_message = null): Order
        {
            $this->complete_mail_message = $complete_mail_message;

            return $this;
        }

        /**
         * @param string|null $complete_mail_message
         *
         * @return self
         */
        public function appendCompleteMailMessage($complete_mail_message = null): Order
        {
            $this->complete_mail_message .= $complete_mail_message;

            return $this;
        }

        /**
         * 商品の受注明細を取得
         *
         * @return OrderItem[]
         */
        public function getProductOrderItems(): array
        {
            $sio = new OrderItemCollection($this->OrderItems->toArray());

            return array_values($sio->getProductClasses()->toArray());
        }

        /**
         * Add orderItem.
         *
         * @param OrderItem $OrderItem
         *
         * @return Order
         */
        public function addOrderItem(OrderItem $OrderItem): Order
        {
            $this->OrderItems[] = $OrderItem;

            return $this;
        }

        /**
         * Remove orderItem.
         *
         * @param OrderItem $OrderItem
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeOrderItem(OrderItem $OrderItem): bool
        {
            return $this->OrderItems->removeElement($OrderItem);
        }

        /**
         * Get orderItems.
         *
         * @return \Doctrine\Common\Collections\Collection<int,OrderItem>
         */
        public function getOrderItems(): \Doctrine\Common\Collections\Collection
        {
            return $this->OrderItems;
        }

        /**
         * Sorted to getOrderItems()
         *
         * @return ItemCollection
         */
        #[\Override]
        public function getItems(): ItemCollection
        {
            return (new ItemCollection($this->getOrderItems()))->sort();
        }

        /**
         * Add shipping.
         *
         * @param Shipping $Shipping
         *
         * @return Order
         */
        public function addShipping(Shipping $Shipping): Order
        {
            $this->Shippings[] = $Shipping;

            return $this;
        }

        /**
         * Remove shipping.
         *
         * @param Shipping $Shipping
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeShipping(Shipping $Shipping): bool
        {
            return $this->Shippings->removeElement($Shipping);
        }

        /**
         * Get shippings.
         *
         * @return \Doctrine\Common\Collections\Collection<int,Shipping>
         */
        public function getShippings(): \Doctrine\Common\Collections\Collection
        {
            $criteria = Criteria::create()
                ->orderBy(['name01' => Criteria::ASC, 'name02' => Criteria::ASC, 'id' => Criteria::ASC]);

            /** @var PersistentCollection<int,Shipping> $Shippings */
            $Shippings = $this->Shippings;

            return $Shippings->matching($criteria);
        }

        /**
         * Add mailHistory.
         *
         * @param MailHistory $mailHistory
         *
         * @return Order
         */
        public function addMailHistory(MailHistory $mailHistory): Order
        {
            $this->MailHistories[] = $mailHistory;

            return $this;
        }

        /**
         * Remove mailHistory.
         *
         * @param MailHistory $mailHistory
         *
         * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeMailHistory(MailHistory $mailHistory): bool
        {
            return $this->MailHistories->removeElement($mailHistory);
        }

        /**
         * Get mailHistories.
         *
         * @return \Doctrine\Common\Collections\Collection<int,MailHistory>
         */
        public function getMailHistories(): \Doctrine\Common\Collections\Collection
        {
            return $this->MailHistories;
        }

        /**
         * Set customer.
         *
         * @param Customer|null $customer
         *
         * @return Order
         */
        public function setCustomer(?Customer $customer = null): Order
        {
            $this->Customer = $customer;

            return $this;
        }

        /**
         * Get customer.
         *
         * @return Customer|null
         */
        public function getCustomer(): ?Customer
        {
            return $this->Customer;
        }

        /**
         * Set country.
         *
         * @param Master\Country|null $country
         *
         * @return Order
         */
        public function setCountry(?Master\Country $country = null): Order
        {
            $this->Country = $country;

            return $this;
        }

        /**
         * Get country.
         *
         * @return Master\Country|null
         */
        public function getCountry(): ?Master\Country
        {
            return $this->Country;
        }

        /**
         * Set pref.
         *
         * @param Master\Pref|null $pref
         *
         * @return Order
         */
        public function setPref(?Master\Pref $pref = null): Order
        {
            $this->Pref = $pref;

            return $this;
        }

        /**
         * Get pref.
         *
         * @return Master\Pref|null
         */
        public function getPref(): ?Master\Pref
        {
            return $this->Pref;
        }

        /**
         * Set sex.
         *
         * @param Master\Sex|null $sex
         *
         * @return Order
         */
        public function setSex(?Master\Sex $sex = null): Order
        {
            $this->Sex = $sex;

            return $this;
        }

        /**
         * Get sex.
         *
         * @return Master\Sex|null
         */
        public function getSex(): ?Master\Sex
        {
            return $this->Sex;
        }

        /**
         * Set job.
         *
         * @param Master\Job|null $job
         *
         * @return Order
         */
        public function setJob(?Master\Job $job = null): Order
        {
            $this->Job = $job;

            return $this;
        }

        /**
         * Get job.
         *
         * @return Master\Job|null
         */
        public function getJob(): ?Master\Job
        {
            return $this->Job;
        }

        /**
         * Set payment.
         *
         * @param Payment|null $payment
         *
         * @return Order
         */
        public function setPayment(?Payment $payment = null): Order
        {
            $this->Payment = $payment;

            return $this;
        }

        /**
         * Get payment.
         *
         * @return Payment|null
         */
        public function getPayment(): ?Payment
        {
            return $this->Payment;
        }

        /**
         * Set deviceType.
         *
         * @param Master\DeviceType|null $deviceType
         *
         * @return Order
         */
        public function setDeviceType(?Master\DeviceType $deviceType = null): Order
        {
            $this->DeviceType = $deviceType;

            return $this;
        }

        /**
         * Get deviceType.
         *
         * @return Master\DeviceType|null
         */
        public function getDeviceType(): ?Master\DeviceType
        {
            return $this->DeviceType;
        }

        /**
         * Set customerOrderStatus.
         *
         * @param Master\CustomerOrderStatus|null $customerOrderStatus
         *
         * @return Order
         */
        public function setCustomerOrderStatus(?Master\CustomerOrderStatus $customerOrderStatus = null): Order
        {
            $this->CustomerOrderStatus = $customerOrderStatus;

            return $this;
        }

        /**
         * Get customerOrderStatus.
         *
         * @return Master\CustomerOrderStatus|null
         */
        public function getCustomerOrderStatus(): ?Master\CustomerOrderStatus
        {
            return $this->CustomerOrderStatus;
        }

        /**
         * Set orderStatusColor.
         *
         * @param Master\OrderStatusColor|null $orderStatusColor
         *
         * @return Order
         */
        public function setOrderStatusColor(?Master\OrderStatusColor $orderStatusColor = null): Order
        {
            $this->OrderStatusColor = $orderStatusColor;

            return $this;
        }

        /**
         * Get orderStatusColor.
         *
         * @return Master\OrderStatusColor|null
         */
        public function getOrderStatusColor(): ?Master\OrderStatusColor
        {
            return $this->OrderStatusColor;
        }

        /**
         * Set orderStatus.
         *
         * @param Master\OrderStatus|null $orderStatus
         *
         * @return self
         */
        public function setOrderStatus(?Master\OrderStatus $orderStatus = null): Order
        {
            $this->OrderStatus = $orderStatus;

            return $this;
        }

        /**
         * Get orderStatus.
         *
         * @return Master\OrderStatus|null
         */
        public function getOrderStatus(): ?Master\OrderStatus
        {
            return $this->OrderStatus;
        }

        /**
         * @param ItemInterface $item
         *
         * @return void
         */
        #[\Override]
        public function addItem(ItemInterface $item): void
        {
            if ($item instanceof OrderItem) {
                $this->OrderItems->add($item);
            }
        }

        #[\Override]
        public function getQuantity(): string
        {
            $quantity = '0';
            foreach ($this->getItems() as $item) {
                $quantity = bcadd($quantity, $item->getQuantity());
            }

            return $quantity;
        }
    }
}
