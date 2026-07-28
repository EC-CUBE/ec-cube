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
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Eccube\Entity\Master\AgentProtocol;
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\CustomerOrderStatus;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Master\Job;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\OrderStatusColor;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Master\Sex;
use Eccube\Entity\Master\TaxType;
use Eccube\Repository\OrderRepository;
use Eccube\Service\Calculator\OrderItemCollection;
use Eccube\Service\PurchaseFlow\ItemCollection;
use Eccube\Service\TaxRuleService;

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
            if (!array_key_exists($rate, $roundingTypes) || null === $roundingTypes[$rate]) {
                continue;
            }

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

        return array_filter($items, fn (OrderItem $Item) => $Item->isDiscount());
    }

    /**
     * 課税対象の値引き金額合計を返す.
     */
    public function getTaxableDiscount(): string
    {
        return array_reduce($this->getTaxableDiscountItems(), fn ($sum, OrderItem $Item) => bcadd($sum, $Item->getTotalPrice(), 2), '0');
    }

    /**
     * 非課税・不課税の値引き明細を返す.
     *
     * @return array<int, OrderItem>
     */
    public function getTaxFreeDiscountItems(): array
    {
        /** @var OrderItem[] $items */
        $items = (new ItemCollection($this->getOrderItems()))->sort()->toArray();

        return array_filter($items, fn (OrderItem $Item) => $Item->isPoint() || ($Item->isDiscount() && $Item->getTaxType()->getId() != TaxType::TAXATION));
    }

    /**
     * 非課税・不課税の値引き額を返す.
     */
    public function getTaxFreeDiscount(): string
    {
        return array_reduce($this->getTaxFreeDiscountItems(), fn ($sum, OrderItem $Item) => bcadd($sum, $Item->getTotalPrice(), 2), '0');
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
     */
    public function findShipping(int $shippingId): ?Shipping
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

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'pre_order_id', type: Types::STRING, length: 255, nullable: true)]
    private ?string $pre_order_id = null;

    #[ORM\Column(name: 'order_no', type: Types::STRING, length: 255, nullable: true)]
    private ?string $order_no = null;

    #[ORM\Column(name: 'message', type: Types::STRING, length: 4000, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(name: 'name01', type: Types::STRING, length: 255)]
    private ?string $name01 = null;

    #[ORM\Column(name: 'name02', type: Types::STRING, length: 255)]
    private ?string $name02 = null;

    #[ORM\Column(name: 'kana01', type: Types::STRING, length: 255, nullable: true)]
    private ?string $kana01 = null;

    #[ORM\Column(name: 'kana02', type: Types::STRING, length: 255, nullable: true)]
    private ?string $kana02 = null;

    #[ORM\Column(name: 'company_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $company_name = null;

    #[ORM\Column(name: 'email', type: Types::STRING, length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'phone_number', type: Types::STRING, length: 14, nullable: true)]
    private ?string $phone_number = null;

    #[ORM\Column(name: 'postal_code', type: Types::STRING, length: 8, nullable: true)]
    private ?string $postal_code = null;

    #[ORM\Column(name: 'addr01', type: Types::STRING, length: 255, nullable: true)]
    private ?string $addr01 = null;

    #[ORM\Column(name: 'addr02', type: Types::STRING, length: 255, nullable: true)]
    private ?string $addr02 = null;

    #[ORM\Column(name: 'birth', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    private ?\DateTime $birth = null;

    #[ORM\Column(name: 'subtotal', type: Types::DECIMAL, precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
    private ?string $subtotal = '0';

    #[ORM\Column(name: 'discount', type: Types::DECIMAL, precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
    private ?string $discount = '0';

    #[ORM\Column(name: 'delivery_fee_total', type: Types::DECIMAL, precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
    private ?string $delivery_fee_total = '0';

    #[ORM\Column(name: 'charge', type: Types::DECIMAL, precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
    private ?string $charge = '0';

    /**
     * @deprecated 明細ごとに集計した税額と差異が発生する場合があるため非推奨
     */
    #[ORM\Column(name: 'tax', type: Types::DECIMAL, precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
    private ?string $tax = '0';

    #[ORM\Column(name: 'total', type: Types::DECIMAL, precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
    private ?string $total = '0';

    #[ORM\Column(name: 'payment_total', type: Types::DECIMAL, precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
    private ?string $payment_total = '0';

    #[ORM\Column(name: 'payment_method', type: Types::STRING, length: 255, nullable: true)]
    private ?string $payment_method = null;

    #[ORM\Column(name: 'note', type: Types::STRING, length: 4000, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $create_date = null;

    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $update_date = null;

    #[ORM\Column(name: 'order_date', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    private ?\DateTime $order_date = null;

    #[ORM\Column(name: 'payment_date', type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    private ?\DateTime $payment_date = null;

    #[ORM\Column(name: 'currency_code', type: Types::STRING, nullable: true)]
    private ?string $currency_code = null;

    /**
     * 注文完了画面に表示するメッセージ
     *
     * プラグインから注文完了時にメッセージを表示したい場合, このフィールドにセットすることで, 注文完了画面で表示されます。
     * 複数のプラグインから利用されるため, appendCompleteMesssage()で追加してください.
     * 表示する際にHTMLは利用可能です。
     */
    #[ORM\Column(name: 'complete_message', type: Types::TEXT, nullable: true)]
    private ?string $complete_message = null;

    /**
     * 注文完了メールに表示するメッセージ
     *
     * プラグインから注文完了メールにメッセージを表示したい場合, このフィールドにセットすることで, 注文完了メールで表示されます。
     * 複数のプラグインから利用されるため, appendCompleteMailMesssage()で追加してください.
     */
    #[ORM\Column(name: 'complete_mail_message', type: Types::TEXT, nullable: true)]
    private ?string $complete_mail_message = null;

    /**
     * エージェントコマース (ACP/UCP) 経由の注文のプロトコル種別マスタへの参照.
     *
     * 通常購入では null。エージェント経由の注文でのみ ACP / UCP がセットされる。
     */
    #[ORM\ManyToOne(targetEntity: AgentProtocol::class)]
    #[ORM\JoinColumn(name: 'agent_protocol_id', referencedColumnName: 'id')]
    private ?AgentProtocol $AgentProtocol = null;

    /**
     * エージェントコマース経由の注文を発行したエージェントの識別子.
     *
     * 通常購入では null。
     */
    #[ORM\Column(name: 'agent_id', type: Types::STRING, length: 255, nullable: true)]
    private ?string $agent_id = null;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'Order', cascade: ['persist', 'remove'])]
    private Collection $OrderItems;

    /**
     * @var Collection<int, Shipping>
     */
    #[ORM\OneToMany(targetEntity: Shipping::class, mappedBy: 'Order', cascade: ['persist', 'remove'])]
    private Collection $Shippings;

    /**
     * @var Collection<int, MailHistory>
     */
    #[ORM\OneToMany(targetEntity: MailHistory::class, mappedBy: 'Order', cascade: ['remove'])]
    #[ORM\OrderBy(['send_date' => 'DESC'])]
    private Collection $MailHistories;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'Orders')]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
    private ?Customer $Customer = null;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
    private ?Country $Country = null;

    #[ORM\ManyToOne(targetEntity: Pref::class)]
    #[ORM\JoinColumn(name: 'pref_id', referencedColumnName: 'id')]
    private ?Pref $Pref = null;

    #[ORM\ManyToOne(targetEntity: Sex::class)]
    #[ORM\JoinColumn(name: 'sex_id', referencedColumnName: 'id')]
    private ?Sex $Sex = null;

    #[ORM\ManyToOne(targetEntity: Job::class)]
    #[ORM\JoinColumn(name: 'job_id', referencedColumnName: 'id')]
    private ?Job $Job = null;

    #[ORM\ManyToOne(targetEntity: Payment::class)]
    #[ORM\JoinColumn(name: 'payment_id', referencedColumnName: 'id')]
    private ?Payment $Payment = null;

    #[ORM\ManyToOne(targetEntity: DeviceType::class)]
    #[ORM\JoinColumn(name: 'device_type_id', referencedColumnName: 'id')]
    private ?DeviceType $DeviceType = null;

    /**
     * OrderStatusより先にプロパティを定義しておかないとセットされなくなる
     */
    #[ORM\ManyToOne(targetEntity: CustomerOrderStatus::class)]
    #[ORM\JoinColumn(name: 'order_status_id', referencedColumnName: 'id')]
    private ?CustomerOrderStatus $CustomerOrderStatus = null;

    /**
     * OrderStatusより先にプロパティを定義しておかないとセットされなくなる
     */
    #[ORM\ManyToOne(targetEntity: OrderStatusColor::class)]
    #[ORM\JoinColumn(name: 'order_status_id', referencedColumnName: 'id')]
    private ?OrderStatusColor $OrderStatusColor = null;

    #[ORM\ManyToOne(targetEntity: OrderStatus::class)]
    #[ORM\JoinColumn(name: 'order_status_id', referencedColumnName: 'id')]
    private ?OrderStatus $OrderStatus = null;

    /**
     * Constructor
     */
    public function __construct(?OrderStatus $OrderStatus = null)
    {
        $this->setDiscount('0')
            ->setSubtotal('0')
            ->setTotal('0')
            ->setPaymentTotal('0')
            ->setCharge('0')
            ->setTax('0')
            ->setDeliveryFeeTotal('0');

        $this->OrderItems = new ArrayCollection();
        $this->Shippings = new ArrayCollection();
        $this->MailHistories = new ArrayCollection();

        if ($OrderStatus !== null) {
            $this->setOrderStatus($OrderStatus);
        }
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
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set preOrderId.
     */
    public function setPreOrderId(?string $preOrderId = null): Order
    {
        $this->pre_order_id = $preOrderId;

        return $this;
    }

    /**
     * Get preOrderId.
     */
    public function getPreOrderId(): ?string
    {
        return $this->pre_order_id;
    }

    /**
     * Set orderNo
     */
    public function setOrderNo(?string $orderNo = null): Order
    {
        $this->order_no = $orderNo;

        return $this;
    }

    /**
     * Get orderNo
     */
    public function getOrderNo(): ?string
    {
        return $this->order_no;
    }

    /**
     * Set message.
     */
    public function setMessage(?string $message = null): Order
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Set name01.
     */
    public function setName01(?string $name01 = null): Order
    {
        $this->name01 = $name01;

        return $this;
    }

    /**
     * Get name01.
     */
    public function getName01(): ?string
    {
        return $this->name01;
    }

    /**
     * Set name02.
     */
    public function setName02(?string $name02 = null): Order
    {
        $this->name02 = $name02;

        return $this;
    }

    /**
     * Get name02.
     */
    public function getName02(): ?string
    {
        return $this->name02;
    }

    /**
     * Set kana01.
     */
    public function setKana01(?string $kana01 = null): Order
    {
        $this->kana01 = $kana01;

        return $this;
    }

    /**
     * Get kana01.
     */
    public function getKana01(): ?string
    {
        return $this->kana01;
    }

    /**
     * Set kana02.
     */
    public function setKana02(?string $kana02 = null): Order
    {
        $this->kana02 = $kana02;

        return $this;
    }

    /**
     * Get kana02.
     */
    public function getKana02(): ?string
    {
        return $this->kana02;
    }

    /**
     * Set companyName.
     */
    public function setCompanyName(?string $companyName = null): Order
    {
        $this->company_name = $companyName;

        return $this;
    }

    /**
     * Get companyName.
     */
    public function getCompanyName(): ?string
    {
        return $this->company_name;
    }

    /**
     * Set email.
     */
    public function setEmail(?string $email = null): Order
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set phone_number.
     */
    public function setPhoneNumber(?string $phone_number = null): Order
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    /**
     * Get phone_number.
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    /**
     * Set postal_code.
     */
    public function setPostalCode(?string $postal_code = null): Order
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    /**
     * Get postal_code.
     */
    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    /**
     * Set addr01.
     */
    public function setAddr01(?string $addr01 = null): Order
    {
        $this->addr01 = $addr01;

        return $this;
    }

    /**
     * Get addr01.
     */
    public function getAddr01(): ?string
    {
        return $this->addr01;
    }

    /**
     * Set addr02.
     */
    public function setAddr02(?string $addr02 = null): Order
    {
        $this->addr02 = $addr02;

        return $this;
    }

    /**
     * Get addr02.
     */
    public function getAddr02(): ?string
    {
        return $this->addr02;
    }

    /**
     * Set birth.
     */
    public function setBirth(?\DateTime $birth = null): Order
    {
        $this->birth = $birth;

        return $this;
    }

    /**
     * Get birth.
     */
    public function getBirth(): ?\DateTime
    {
        return $this->birth;
    }

    /**
     * Set subtotal.
     */
    public function setSubtotal(string $subtotal): Order
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal.
     */
    public function getSubtotal(): string
    {
        return $this->subtotal;
    }

    /**
     * Set discount.
     *
     * @param string $discount
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
     */
    #[\Override]
    public function setTotal($total): static
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     */
    #[\Override]
    public function getTotal(): string
    {
        return $this->total;
    }

    /**
     * Set paymentTotal.
     */
    public function setPaymentTotal(string $paymentTotal): Order
    {
        $this->payment_total = $paymentTotal;

        return $this;
    }

    /**
     * Get paymentTotal.
     */
    public function getPaymentTotal(): string
    {
        return $this->payment_total;
    }

    /**
     * Set paymentMethod.
     */
    public function setPaymentMethod(?string $paymentMethod = null): Order
    {
        $this->payment_method = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod.
     */
    public function getPaymentMethod(): ?string
    {
        return $this->payment_method;
    }

    /**
     * Set note.
     */
    public function setNote(?string $note = null): Order
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note.
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * Set createDate.
     */
    public function setCreateDate(\DateTime $createDate): Order
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     */
    public function getCreateDate(): ?\DateTime
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     */
    public function setUpdateDate(\DateTime $updateDate): Order
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     */
    public function getUpdateDate(): ?\DateTime
    {
        return $this->update_date;
    }

    /**
     * Set orderDate.
     */
    public function setOrderDate(?\DateTime $orderDate = null): Order
    {
        $this->order_date = $orderDate;

        return $this;
    }

    /**
     * Get orderDate.
     */
    public function getOrderDate(): ?\DateTime
    {
        return $this->order_date;
    }

    /**
     * Set paymentDate.
     */
    public function setPaymentDate(?\DateTime $paymentDate = null): Order
    {
        $this->payment_date = $paymentDate;

        return $this;
    }

    /**
     * Get paymentDate.
     */
    public function getPaymentDate(): ?\DateTime
    {
        return $this->payment_date;
    }

    /**
     * Get currencyCode.
     */
    public function getCurrencyCode(): string
    {
        return $this->currency_code;
    }

    /**
     * Set currencyCode.
     *
     * @return $this
     */
    public function setCurrencyCode(?string $currencyCode = null): static
    {
        $this->currency_code = $currencyCode;

        return $this;
    }

    public function getCompleteMessage(): ?string
    {
        return $this->complete_message;
    }

    /**
     * @return $this
     */
    public function setCompleteMessage(?string $complete_message = null): static
    {
        $this->complete_message = $complete_message;

        return $this;
    }

    /**
     * @return $this
     */
    public function appendCompleteMessage(?string $complete_message = null): static
    {
        $this->complete_message .= $complete_message;

        return $this;
    }

    public function getCompleteMailMessage(): ?string
    {
        return $this->complete_mail_message;
    }

    public function setCompleteMailMessage(?string $complete_mail_message = null): Order
    {
        $this->complete_mail_message = $complete_mail_message;

        return $this;
    }

    public function appendCompleteMailMessage(?string $complete_mail_message = null): Order
    {
        $this->complete_mail_message .= $complete_mail_message;

        return $this;
    }

    public function getAgentProtocol(): ?AgentProtocol
    {
        return $this->AgentProtocol;
    }

    public function setAgentProtocol(?AgentProtocol $AgentProtocol = null): Order
    {
        $this->AgentProtocol = $AgentProtocol;

        return $this;
    }

    public function getAgentId(): ?string
    {
        return $this->agent_id;
    }

    public function setAgentId(?string $agent_id = null): Order
    {
        $this->agent_id = $agent_id;

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
     */
    public function addOrderItem(OrderItem $OrderItem): Order
    {
        $this->OrderItems[] = $OrderItem;

        return $this;
    }

    /**
     * Remove orderItem.
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
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->OrderItems;
    }

    /**
     * Sorted to getOrderItems()
     */
    #[\Override]
    public function getItems(): ItemCollection
    {
        return (new ItemCollection($this->getOrderItems()))->sort();
    }

    /**
     * Add shipping.
     */
    public function addShipping(Shipping $Shipping): Order
    {
        $this->Shippings[] = $Shipping;

        return $this;
    }

    /**
     * Remove shipping.
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
     * @return Collection<int, Shipping>
     */
    public function getShippings(): Collection
    {
        $criteria = Criteria::create()
            ->orderBy(['name01' => Criteria::ASC, 'name02' => Criteria::ASC, 'id' => Criteria::ASC]);

        /** @var PersistentCollection<int,Shipping> $Shippings */
        $Shippings = $this->Shippings;

        return $Shippings->matching($criteria);
    }

    /**
     * Add mailHistory.
     */
    public function addMailHistory(MailHistory $mailHistory): Order
    {
        $this->MailHistories[] = $mailHistory;

        return $this;
    }

    /**
     * Remove mailHistory.
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
     * @return Collection<int, MailHistory>
     */
    public function getMailHistories(): Collection
    {
        return $this->MailHistories;
    }

    /**
     * Set customer.
     */
    public function setCustomer(?Customer $customer = null): Order
    {
        $this->Customer = $customer;

        return $this;
    }

    /**
     * Get customer.
     */
    public function getCustomer(): ?Customer
    {
        return $this->Customer;
    }

    /**
     * Set country.
     */
    public function setCountry(?Country $country = null): Order
    {
        $this->Country = $country;

        return $this;
    }

    /**
     * Get country.
     */
    public function getCountry(): ?Country
    {
        return $this->Country;
    }

    /**
     * Set pref.
     */
    public function setPref(?Pref $pref = null): Order
    {
        $this->Pref = $pref;

        return $this;
    }

    /**
     * Get pref.
     */
    public function getPref(): ?Pref
    {
        return $this->Pref;
    }

    /**
     * Set sex.
     */
    public function setSex(?Sex $sex = null): Order
    {
        $this->Sex = $sex;

        return $this;
    }

    /**
     * Get sex.
     */
    public function getSex(): ?Sex
    {
        return $this->Sex;
    }

    /**
     * Set job.
     */
    public function setJob(?Job $job = null): Order
    {
        $this->Job = $job;

        return $this;
    }

    /**
     * Get job.
     */
    public function getJob(): ?Job
    {
        return $this->Job;
    }

    /**
     * Set payment.
     */
    public function setPayment(?Payment $payment = null): Order
    {
        $this->Payment = $payment;

        return $this;
    }

    /**
     * Get payment.
     */
    public function getPayment(): ?Payment
    {
        return $this->Payment;
    }

    /**
     * Set deviceType.
     */
    public function setDeviceType(?DeviceType $deviceType = null): Order
    {
        $this->DeviceType = $deviceType;

        return $this;
    }

    /**
     * Get deviceType.
     */
    public function getDeviceType(): ?DeviceType
    {
        return $this->DeviceType;
    }

    /**
     * Set customerOrderStatus.
     */
    public function setCustomerOrderStatus(?CustomerOrderStatus $customerOrderStatus = null): Order
    {
        $this->CustomerOrderStatus = $customerOrderStatus;

        return $this;
    }

    /**
     * Get customerOrderStatus.
     */
    public function getCustomerOrderStatus(): ?CustomerOrderStatus
    {
        return $this->CustomerOrderStatus;
    }

    /**
     * Set orderStatusColor.
     */
    public function setOrderStatusColor(?OrderStatusColor $orderStatusColor = null): Order
    {
        $this->OrderStatusColor = $orderStatusColor;

        return $this;
    }

    /**
     * Get orderStatusColor.
     */
    public function getOrderStatusColor(): ?OrderStatusColor
    {
        return $this->OrderStatusColor;
    }

    /**
     * Set orderStatus.
     */
    public function setOrderStatus(?OrderStatus $orderStatus = null): Order
    {
        $this->OrderStatus = $orderStatus;

        return $this;
    }

    /**
     * Get orderStatus.
     */
    public function getOrderStatus(): ?OrderStatus
    {
        return $this->OrderStatus;
    }

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
