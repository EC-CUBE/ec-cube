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


namespace Eccube\Entity;

use Eccube\Common\Constant;
use Eccube\Service\Calculator\ShipmentItemCollection;
use Eccube\Service\ItemValidateException;
use Eccube\Util\EntityUtil;
use Eccube\Entity\Master\OrderItemType;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 *
 * @ORM\Table(name="dtb_order", indexes={@ORM\Index(name="dtb_order_pre_order_id_idx", columns={"pre_order_id"}), @ORM\Index(name="dtb_order_order_email_idx", columns={"order_email"}), @ORM\Index(name="dtb_order_order_date_idx", columns={"order_date"}), @ORM\Index(name="dtb_order_payment_date_idx", columns={"payment_date"}), @ORM\Index(name="dtb_order_commit_date_idx", columns={"commit_date"}), @ORM\Index(name="dtb_order_update_date_idx", columns={"update_date"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\OrderRepository")
 */
class Order extends \Eccube\Entity\AbstractEntity implements PurchaseInterface, ItemHolderInterface
{
    /**
     * @var ItemValidateException[]
     */
    private $errors = [];

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
     * isPriceChange
     * 
     * @return boolean
     */
    public function isPriceChange()
    {
        foreach ($this->getOrderDetails() as $OrderDetail) {
            if ($OrderDetail->isPriceChange()) {
                return true;
            }
        }

        return false;
    }

    /**
     * 対象となるお届け先情報を取得
     * 
     * @param integer $shippingId
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
     * Calculate quantity of total.
     *
     * @return integer
     * @deprecated
     */
    public function calculateTotalQuantity()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
        $totalQuantity = 0;
        foreach ($this->getOrderDetails() as $OrderDetail) {
            $totalQuantity += $OrderDetail->getQuantity();
        }

        return $totalQuantity;
    }

    /**
     * Calculate SubTotal.
     *
     * @return integer
     * @deprecated
     */
    public function calculateSubTotal()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
        return array_reduce($this->getProductOrderItems(), function($total, $ShipmentItem) {
            return $total + $ShipmentItem->getPriceIncTax() * $ShipmentItem->getQuantity();
        }, 0);
    }

    /**
     * Calculate tax of total.
     *
     * @return integer
     * @deprecated
     */
    public function calculateTotalTax()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
        $tax = 0;
        foreach ($this->getOrderDetails() as $OrderDetail) {
            $tax += ($OrderDetail->getPriceIncTax() - $OrderDetail->getPrice()) * $OrderDetail->getQuantity();
        }

        return $tax;
    }

    /**
     * この注文にかかる送料の合計を返す.
     *
     * @return integer
     * @deprecated \Eccube\Service\Calculator\Strategy\CalculateDeliveryFeeStrategy を使用してください
     */
    public function calculateDeliveryFeeTotal()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
        // TODO filter を外出ししたい
        return array_reduce(
            array_filter($this->getShipmentItems()->toArray(),
                         function($ShipmentItem) {
                             return $ShipmentItem->isDeliveryFee();
                         }),
            function($total, $ShipmentItem) {
                return $total + $ShipmentItem->getPriceIncTax() * $ShipmentItem->getQuantity();
            }, 0);
    }

    /**
     * この注文にかかる値引きの合計を返す.
     *
     * @return integer
     * @deprecated
     */
    public function calculateDiscountTotal()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
        // TODO filter を外出ししたい
        return array_reduce(
            array_filter($this->getShipmentItems()->toArray(),
                         function($ShipmentItem) {
                             return $ShipmentItem->isDiscount();
                         }),
            function($total, $ShipmentItem) {
                return $total + $ShipmentItem->getPriceIncTax() * $ShipmentItem->getQuantity();
            }, 0);
    }

    /**
     * この注文にかかる手数料の合計を返す.
     *
     * @return integer
     * @deprecated
     */
    public function calculateChargeTotal()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
        // TODO filter を外出ししたい
        return array_reduce(
            array_filter($this->getShipmentItems()->toArray(),
                         function($ShipmentItem) {
                             return $ShipmentItem->isCharge();
                         }),
            function($total, $ShipmentItem) {
                return $total + $ShipmentItem->getPriceIncTax() * $ShipmentItem->getQuantity();
            }, 0);
    }

    /**
     * この注文の保持する商品種別を取得します.
     *
     * @return \Eccube\Entity\Master\ProductType[] 一意な商品種別の配列
     */
    public function getProductTypes()
    {
        $productTypes = array();
        foreach ($this->getOrderDetails() as $OrderDetail) {
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $OrderDetail->getProductClass();
            $productTypes[] = $ProductClass->getProductType();
        }

        return array_unique($productTypes);
    }


    /**
     * 合計金額を計算
     *
     * @return string
     * @deprecated
     */
    public function getTotalPrice() {

        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);
         return $this->getSubtotal() + $this->getCharge() + $this->getDeliveryFeeTotal() - $this->getDiscount();
//        return $this->getSubtotal() + $this->getCharge() - $this->getDiscount();
    }


    /**
     * @var integer
     *
     * @ORM\Column(name="order_id", type="integer", options={"unsigned":true})
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
     * @ORM\Column(name="message", type="string", length=4000, nullable=true)
     */
    private $message;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_name01", type="string", length=255, nullable=true)
     */
    private $name01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_name02", type="string", length=255, nullable=true)
     */
    private $name02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_kana01", type="string", length=255, nullable=true)
     */
    private $kana01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_kana02", type="string", length=255, nullable=true)
     */
    private $kana02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_company_name", type="string", length=255, nullable=true)
     */
    private $company_name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_tel01", type="string", length=5, nullable=true)
     */
    private $tel01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_tel02", type="string", length=4, nullable=true)
     */
    private $tel02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_tel03", type="string", length=4, nullable=true)
     */
    private $tel03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_fax01", type="string", length=5, nullable=true)
     */
    private $fax01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_fax02", type="string", length=4, nullable=true)
     */
    private $fax02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_fax03", type="string", length=4, nullable=true)
     */
    private $fax03;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_zip01", type="string", length=3, nullable=true)
     */
    private $zip01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_zip02", type="string", length=4, nullable=true)
     */
    private $zip02;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_zipcode", type="string", length=7, nullable=true)
     */
    private $zipcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_addr01", type="string", length=255, nullable=true)
     */
    private $addr01;

    /**
     * @var string|null
     *
     * @ORM\Column(name="order_addr02", type="string", length=255, nullable=true)
     */
    private $addr02;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="order_birth", type="datetimetz", nullable=true)
     */
    private $birth;

    /**
     * @var string
     *
     * @ORM\Column(name="subtotal", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
     */
    private $subtotal = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="discount", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
     */
    private $discount = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_fee_total", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
     */
    private $delivery_fee_total = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="charge", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
     */
    private $charge = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="tax", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
     */
    private $tax = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
     */
    private $total = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_total", type="decimal", precision=10, scale=0, options={"unsigned":true,"default":0})
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
     * @ORM\Column(name="commit_date", type="datetimetz", nullable=true)
     */
    private $commit_date;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="payment_date", type="datetimetz", nullable=true)
     */
    private $payment_date;

    /**
     * @var int
     *
     * @ORM\Column(name="del_flg", type="smallint", options={"unsigned":true,"default":0})
     */
    private $del_flg = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\OrderDetail", mappedBy="Order", cascade={"persist"})
     * @ORM\OrderBy({
     *     "id"="ASC"
     * })
     */
    private $OrderDetails;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\ShipmentItem", mappedBy="Order", cascade={"persist","remove"})
     */
    private $ShipmentItems;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\MailHistory", mappedBy="Order")
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
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="customer_id")
     * })
     */
    private $Customer;

    /**
     * @var \Eccube\Entity\Master\Country
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_country_id", referencedColumnName="id")
     * })
     */
    private $Country;

    /**
     * @var \Eccube\Entity\Master\Pref
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Pref")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_pref", referencedColumnName="id")
     * })
     */
    private $Pref;

    /**
     * @var \Eccube\Entity\Master\Sex
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Sex")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_sex", referencedColumnName="id")
     * })
     */
    private $Sex;

    /**
     * @var \Eccube\Entity\Master\Job
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\Job")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="order_job", referencedColumnName="id")
     * })
     */
    private $Job;

    /**
     * @var \Eccube\Entity\Payment
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Payment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payment_id", referencedColumnName="payment_id")
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
     *   @ORM\JoinColumn(name="status", referencedColumnName="id")
     * })
     */
    private $CustomerOrderStatus;

    /**
     * @var \Eccube\Entity\Master\OrderStatusColor
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\OrderStatusColor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status", referencedColumnName="id")
     * })
     */
    private $OrderStatusColor;

    /**
     * @var \Eccube\Entity\Master\OrderStatus
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\OrderStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status", referencedColumnName="id")
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
            ->setDelFlg(Constant::DISABLED);

        $this->OrderDetails = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ShipmentItems = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set commitDate.
     *
     * @param \DateTime|null $commitDate
     *
     * @return Order
     */
    public function setCommitDate($commitDate = null)
    {
        $this->commit_date = $commitDate;

        return $this;
    }

    /**
     * Get commitDate.
     *
     * @return \DateTime|null
     */
    public function getCommitDate()
    {
        return $this->commit_date;
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
     * Set delFlg.
     *
     * @param int $delFlg
     *
     * @return Order
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get delFlg.
     *
     * @return int
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Add orderDetail.
     *
     * @param \Eccube\Entity\OrderDetail $orderDetail
     *
     * @return Order
     */
    public function addOrderDetail(\Eccube\Entity\OrderDetail $orderDetail)
    {
        $this->OrderDetails[] = $orderDetail;

        return $this;
    }

    /**
     * Remove orderDetail.
     *
     * @param \Eccube\Entity\OrderDetail $orderDetail
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeOrderDetail(\Eccube\Entity\OrderDetail $orderDetail)
    {
        return $this->OrderDetails->removeElement($orderDetail);
    }

    /**
     * Get orderDetails.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderDetails()
    {
        return $this->OrderDetails;
    }

    /**
     * 商品の受注明細を取得
     * @return ShipmentItem[]
     */
    public function getProductOrderItems()
    {
        $sio = new ShipmentItemCollection($this->ShipmentItems->toArray());
        return $sio->getProductClasses()->toArray();
    }

    /**
     * Add shipmentItem.
     *
     * @param \Eccube\Entity\ShipmentItem $shipmentItem
     *
     * @return Shipping
     */
    public function addShipmentItem(\Eccube\Entity\ShipmentItem $shipmentItem)
    {
        $this->ShipmentItems[] = $shipmentItem;

        return $this;
    }

    /**
     * Remove shipmentItem.
     *
     * @param \Eccube\Entity\ShipmentItem $shipmentItem
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeShipmentItem(\Eccube\Entity\ShipmentItem $shipmentItem)
    {
        return $this->ShipmentItems->removeElement($shipmentItem);
    }

    /**
     * Get shipmentItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShipmentItems()
    {
        return $this->ShipmentItems;
    }

    /**
     * Alias of getShipmentItems()
     */
    public function getItems()
    {
        return $this->getShipmentItems();
    }

    /**
     * Get shippings.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShippings()
    {
        $Shippings = [];
        foreach ($this->getShipmentItems() as $ShipmentItem) {
            $Shipping = $ShipmentItem->getShipping();
            if (is_object($Shipping)) {
                $name = $Shipping->getName01(); // XXX lazy loading
                $Shippings[$Shipping->getId()] = $Shipping;
            }
        }
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
     * Set orderStatusColor.
     *
     * @param \Eccube\Entity\Master\OrderStatusColor|null $orderStatusColor
     *
     * @return Order
     */
    public function setOrderStatusColor(\Eccube\Entity\Master\OrderStatusColor $orderStatusColor = null)
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
     * @param string $error
     * @return void
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return ItemValidateException[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param ItemInterface $item
     */
    public function addItem(ItemInterface $item)
    {
        $this->ShipmentItems->add($item);
    }

    public function getQuantity()
    {
        $quantity = 0;
        foreach($this->getItems() as $item) {
            $quantity += $item->getQuantity();
        }

        return $quantity;
    }
}
