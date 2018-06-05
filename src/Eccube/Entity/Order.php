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

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Service\Calculator\OrderItemCollection;
use Eccube\Service\PurchaseFlow\ItemCollection;

/**
 * Order
 *
 * @ORM\Table(name="dtb_order", indexes={@ORM\Index(name="dtb_order_pre_order_id_idx", columns={"pre_order_id"}), @ORM\Index(name="dtb_order_email_idx", columns={"email"}), @ORM\Index(name="dtb_order_order_date_idx", columns={"order_date"}), @ORM\Index(name="dtb_order_payment_date_idx", columns={"payment_date"}), @ORM\Index(name="dtb_order_shipping_date_idx", columns={"shipping_date"}), @ORM\Index(name="dtb_order_update_date_idx", columns={"update_date"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\OrderRepository")
 */
class Order extends \Eccube\Entity\AbstractEntity implements PurchaseInterface, ItemHolderInterface
{
    use PointTrait;

    /**
     * isMultiple
     *
     * @return boolean
     */
    public function isMultiple()
    {
        return count($this->getShippings()) > 1 ? true : false;
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
     * 合計金額を計算
     *
     * @return string
     *
     * @deprecated
     */
    public function getTotalPrice()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);

        return $this->getSubtotal() + $this->getCharge() + $this->getDeliveryFeeTotal() - $this->getDiscount();
//        return $this->getSubtotal() + $this->getCharge() - $this->getDiscount();
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
     * @ORM\Column(name="order_code", type="string", length=255, nullable=true)
     */
    private $order_code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="message", type="string", length=4000, nullable=true)
     */
    private $message;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name01", type="string", length=255, nullable=true)
     */
    private $name01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name02", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="tel01", type="string", length=5, nullable=true)
     */
    private $tel01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tel02", type="string", length=4, nullable=true)
     */
    private $tel02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tel03", type="string", length=4, nullable=true)
     */
    private $tel03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fax01", type="string", length=5, nullable=true)
     */
    private $fax01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fax02", type="string", length=4, nullable=true)
     */
    private $fax02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fax03", type="string", length=4, nullable=true)
     */
    private $fax03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zip01", type="string", length=3, nullable=true)
     */
    private $zip01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zip02", type="string", length=4, nullable=true)
     */
    private $zip02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zipcode", type="string", length=7, nullable=true)
     */
    private $zipcode;

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
     * @ORM\Column(name="shipping_date", type="datetimetz", nullable=true)
     */
    private $shipping_date;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\OrderItem", mappedBy="Order", cascade={"persist","remove"})
     */
    private $OrderItems;

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
     * @var \Eccube\Entity\Master\CustomerOrderStatus
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\CustomerOrderStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_status_id", referencedColumnName="id")
     * })
     */
    private $CustomerOrderStatus;

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
    public function __construct(\Eccube\Entity\Master\OrderStatus $orderStatus = null)
    {
        $this->setDiscount(0)
            ->setSubtotal(0)
            ->setTotal(0)
            ->setPaymentTotal(0)
            ->setCharge(0)
            ->setTax(0)
            ->setDeliveryFeeTotal(0)
            ->setOrderStatus($orderStatus)
        ;

        $this->OrderItems = new \Doctrine\Common\Collections\ArrayCollection();
        $this->MailHistories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set orderCode
     *
     * @param string|null $orderCode
     *
     * @return Order
     */
    public function setOrderCode($orderCode = null)
    {
        $this->order_code = $orderCode;

        return $this;
    }

    /**
     * Get orderCode
     *
     * @return string|null
     */
    public function getOrderCode()
    {
        return $this->order_code;
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
     * Set tel01.
     *
     * @param string|null $tel01
     *
     * @return Order
     */
    public function setTel01($tel01 = null)
    {
        $this->tel01 = $tel01;

        return $this;
    }

    /**
     * Get tel01.
     *
     * @return string|null
     */
    public function getTel01()
    {
        return $this->tel01;
    }

    /**
     * Set tel02.
     *
     * @param string|null $tel02
     *
     * @return Order
     */
    public function setTel02($tel02 = null)
    {
        $this->tel02 = $tel02;

        return $this;
    }

    /**
     * Get tel02.
     *
     * @return string|null
     */
    public function getTel02()
    {
        return $this->tel02;
    }

    /**
     * Set tel03.
     *
     * @param string|null $tel03
     *
     * @return Order
     */
    public function setTel03($tel03 = null)
    {
        $this->tel03 = $tel03;

        return $this;
    }

    /**
     * Get tel03.
     *
     * @return string|null
     */
    public function getTel03()
    {
        return $this->tel03;
    }

    /**
     * Set fax01.
     *
     * @param string|null $fax01
     *
     * @return Order
     */
    public function setFax01($fax01 = null)
    {
        $this->fax01 = $fax01;

        return $this;
    }

    /**
     * Get fax01.
     *
     * @return string|null
     */
    public function getFax01()
    {
        return $this->fax01;
    }

    /**
     * Set fax02.
     *
     * @param string|null $fax02
     *
     * @return Order
     */
    public function setFax02($fax02 = null)
    {
        $this->fax02 = $fax02;

        return $this;
    }

    /**
     * Get fax02.
     *
     * @return string|null
     */
    public function getFax02()
    {
        return $this->fax02;
    }

    /**
     * Set fax03.
     *
     * @param string|null $fax03
     *
     * @return Order
     */
    public function setFax03($fax03 = null)
    {
        $this->fax03 = $fax03;

        return $this;
    }

    /**
     * Get fax03.
     *
     * @return string|null
     */
    public function getFax03()
    {
        return $this->fax03;
    }

    /**
     * Set zip01.
     *
     * @param string|null $zip01
     *
     * @return Order
     */
    public function setZip01($zip01 = null)
    {
        $this->zip01 = $zip01;

        return $this;
    }

    /**
     * Get zip01.
     *
     * @return string|null
     */
    public function getZip01()
    {
        return $this->zip01;
    }

    /**
     * Set zip02.
     *
     * @param string|null $zip02
     *
     * @return Order
     */
    public function setZip02($zip02 = null)
    {
        $this->zip02 = $zip02;

        return $this;
    }

    /**
     * Get zip02.
     *
     * @return string|null
     */
    public function getZip02()
    {
        return $this->zip02;
    }

    /**
     * Set zipcode.
     *
     * @param string|null $zipcode
     *
     * @return Order
     */
    public function setZipcode($zipcode = null)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode.
     *
     * @return string|null
     */
    public function getZipcode()
    {
        return $this->zipcode;
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
     * Set shippingDate.
     *
     * @param \DateTime|null $shippingDate
     *
     * @return Order
     */
    public function setShippingDate($shippingDate = null)
    {
        $this->shipping_date = $shippingDate;

        return $this;
    }

    /**
     * Get shippingDate.
     *
     * @return \DateTime|null
     */
    public function getShippingDate()
    {
        return $this->shipping_date;
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
                $OrderItem
                    ->setProduct($ProductOrderItem->getProduct())
                    ->setProductName($ProductOrderItem->getProductName())
                    ->setClassCategoryName1($ProductOrderItem->getClassCategoryName1())
                    ->setClassCategoryName2($ProductOrderItem->getClassCategoryName2())
                    ->setPriceIncTax($ProductOrderItem->getPriceIncTax())
                    ->setQuantity($ProductOrderItem->getQuantity());
                $orderItemArray[$productClassId] = $OrderItem;
            }
        }

        return array_values($orderItemArray);
    }

    /**
     * Add orderItem.
     *
     * @param \Eccube\Entity\OrderItem $OrderItem
     *
     * @return Shipping
     */
    public function addOrderItem(\Eccube\Entity\OrderItem $OrderItem)
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
    public function removeOrderItem(\Eccube\Entity\OrderItem $OrderItem)
    {
        return $this->OrderItems->removeElement($OrderItem);
    }

    /**
     * Get orderItems.
     *
     * @return \Doctrine\Common\Collections\Collection
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
     * Get shippings.
     *
     * 明細に紐づくShippingを, 重複をのぞいて取得する
     *
     * @return \Doctrine\Common\Collections\Collection|Shipping[]
     */
    public function getShippings()
    {
        $Shippings = [];
        foreach ($this->getOrderItems() as $OrderItem) {
            if ($Shipping = $OrderItem->getShipping()) {
                // 永続化される前のShippingが渡ってくる場合もあるため,
                // Shipping::id()ではなくspl_object_id()を使用している
                $id = \spl_object_id($Shipping);
                if (!isset($Shippings[$id])) {
                    $Shippings[$id] = $Shipping;
                }
            }
        }

        usort($Shippings, function (Shipping $a, Shipping $b) {
            $result = strnatcmp($a->getName01(), $b->getName01());
            if ($result === 0) {
                return strnatcmp($a->getName02(), $b->getName02());
            } else {
                return $result;
            }
        });

        $Result = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($Shippings as $Shipping) {
            $Result->add($Shipping);
        }

        return $Result;
        // XXX 以下のロジックだと何故か空の Collection になってしまう場合がある
        // return new \Doctrine\Common\Collections\ArrayCollection(array_values($Shippings));
    }

    public function setShippings($dummy)
    {
        // XXX これが無いと Eccube\Form\Type\Shopping\OrderType がエラーになる
        return $this;
    }

    /**
     * Add mailHistory.
     *
     * @param \Eccube\Entity\MailHistory $mailHistory
     *
     * @return Order
     */
    public function addMailHistory(\Eccube\Entity\MailHistory $mailHistory)
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
    public function removeMailHistory(\Eccube\Entity\MailHistory $mailHistory)
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
    public function setCustomer(\Eccube\Entity\Customer $customer = null)
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
    public function setCountry(\Eccube\Entity\Master\Country $country = null)
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
    public function setPref(\Eccube\Entity\Master\Pref $pref = null)
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
    public function setSex(\Eccube\Entity\Master\Sex $sex = null)
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
    public function setJob(\Eccube\Entity\Master\Job $job = null)
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
    public function setPayment(\Eccube\Entity\Payment $payment = null)
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
    public function setDeviceType(\Eccube\Entity\Master\DeviceType $deviceType = null)
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
    public function setCustomerOrderStatus(\Eccube\Entity\Master\CustomerOrderStatus $customerOrderStatus = null)
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
     * Set orderStatus.
     *
     * @param \Eccube\Entity\Master\OrderStatus|null $orderStatus
     *
     * @return Order
     */
    public function setOrderStatus(\Eccube\Entity\Master\OrderStatus $orderStatus = null)
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
