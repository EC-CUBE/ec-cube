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

/**
 * Order
 */
class Order extends \Eccube\Entity\AbstractEntity
{
    /**
     * @return bool
     */
    public function isMultiple()
    {
        return count($this->getShippings()) > 1 ? true : false;
    }

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
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $pre_order_id;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $name01;

    /**
     * @var string
     */
    private $name02;

    /**
     * @var string
     */
    private $kana01;

    /**
     * @var string
     */
    private $kana02;

    /**
     * @var string
     */
    private $company_name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $tel01;

    /**
     * @var string
     */
    private $tel02;

    /**
     * @var string
     */
    private $tel03;

    /**
     * @var string
     */
    private $fax01;

    /**
     * @var string
     */
    private $fax02;

    /**
     * @var string
     */
    private $fax03;

    /**
     * @var string
     */
    private $zip01;

    /**
     * @var string
     */
    private $zip02;

    /**
     * @var string
     */
    private $zipcode;

    /**
     * @var string
     */
    private $addr01;

    /**
     * @var string
     */
    private $addr02;

    /**
     * @var \DateTime
     */
    private $birth;

    /**
     * @var string
     */
    private $subtotal;

    /**
     * @var string
     */
    private $discount;

    /**
     * @var string
     */
    private $delivery_fee_total;

    /**
     * @var string
     */
    private $charge;

    /**
     * @var string
     */
    private $tax;

    /**
     * @var string
     */
    private $total;

    /**
     * @var string
     */
    private $payment_total;

    /**
     * @var string
     */
    private $payment_method;

    /**
     * @var string
     */
    private $note;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var \DateTime
     */
    private $order_date;

    /**
     * @var \DateTime
     */
    private $commit_date;

    /**
     * @var \DateTime
     */
    private $payment_date;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $OrderDetails;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Shippings;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $MailHistories;

    /**
     * @var \Eccube\Entity\Customer
     */
    private $Customer;

    /**
     * @var \Eccube\Entity\Master\Country
     */
    private $Country;

    /**
     * @var \Eccube\Entity\Master\Pref
     */
    private $Pref;

    /**
     * @var \Eccube\Entity\Master\Sex
     */
    private $Sex;

    /**
     * @var \Eccube\Entity\Master\Job
     */
    private $Job;

    /**
     * @var \Eccube\Entity\Payment
     */
    private $Payment;

    /**
     * @var \Eccube\Entity\Master\DeviceType
     */
    private $DeviceType;

    /**
     * @var \Eccube\Entity\Master\CustomerOrderStatus
     */
    private $CustomerOrderStatus;

    /**
     * @var \Eccube\Entity\Master\OrderStatus
     */
    private $OrderStatus;

    /**
     * @var \Eccube\Entity\Master\OrderStatusColor
     */
    private $OrderStatusColor;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->OrderDetails = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Shippings = new \Doctrine\Common\Collections\ArrayCollection();
        $this->MailHistories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pre_order_id
     *
     * @param  string $preOrderId
     * @return Order
     */
    public function setPreOrderId($preOrderId)
    {
        $this->pre_order_id = $preOrderId;

        return $this;
    }

    /**
     * Get pre_order_id
     *
     * @return string
     */
    public function getPreOrderId()
    {
        return $this->pre_order_id;
    }

    /**
     * Set message
     *
     * @param  string $message
     * @return Order
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set name01
     *
     * @param  string $name01
     * @return Order
     */
    public function setName01($name01)
    {
        $this->name01 = $name01;

        return $this;
    }

    /**
     * Get name01
     *
     * @return string
     */
    public function getName01()
    {
        return $this->name01;
    }

    /**
     * Set name02
     *
     * @param  string $name02
     * @return Order
     */
    public function setName02($name02)
    {
        $this->name02 = $name02;

        return $this;
    }

    /**
     * Get name02
     *
     * @return string
     */
    public function getName02()
    {
        return $this->name02;
    }

    /**
     * Set kana01
     *
     * @param  string $kana01
     * @return Order
     */
    public function setKana01($kana01)
    {
        $this->kana01 = $kana01;

        return $this;
    }

    /**
     * Get kana01
     *
     * @return string
     */
    public function getKana01()
    {
        return $this->kana01;
    }

    /**
     * Set kana02
     *
     * @param  string $kana02
     * @return Order
     */
    public function setKana02($kana02)
    {
        $this->kana02 = $kana02;

        return $this;
    }

    /**
     * Get kana02
     *
     * @return string
     */
    public function getKana02()
    {
        return $this->kana02;
    }

    /**
     * Set company_name
     *
     * @param  string $companyName
     * @return Order
     */
    public function setCompanyName($companyName)
    {
        $this->company_name = $companyName;

        return $this;
    }

    /**
     * Get company_name
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->company_name;
    }

    /**
     * Set email
     *
     * @param  string $email
     * @return Order
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set tel01
     *
     * @param  string $tel01
     * @return Order
     */
    public function setTel01($tel01)
    {
        $this->tel01 = $tel01;

        return $this;
    }

    /**
     * Get tel01
     *
     * @return string
     */
    public function getTel01()
    {
        return $this->tel01;
    }

    /**
     * Set tel02
     *
     * @param  string $tel02
     * @return Order
     */
    public function setTel02($tel02)
    {
        $this->tel02 = $tel02;

        return $this;
    }

    /**
     * Get tel02
     *
     * @return string
     */
    public function getTel02()
    {
        return $this->tel02;
    }

    /**
     * Set tel03
     *
     * @param  string $tel03
     * @return Order
     */
    public function setTel03($tel03)
    {
        $this->tel03 = $tel03;

        return $this;
    }

    /**
     * Get tel03
     *
     * @return string
     */
    public function getTel03()
    {
        return $this->tel03;
    }

    /**
     * Set fax01
     *
     * @param  string $fax01
     * @return Order
     */
    public function setFax01($fax01)
    {
        $this->fax01 = $fax01;

        return $this;
    }

    /**
     * Get fax01
     *
     * @return string
     */
    public function getFax01()
    {
        return $this->fax01;
    }

    /**
     * Set fax02
     *
     * @param  string $fax02
     * @return Order
     */
    public function setFax02($fax02)
    {
        $this->fax02 = $fax02;

        return $this;
    }

    /**
     * Get fax02
     *
     * @return string
     */
    public function getFax02()
    {
        return $this->fax02;
    }

    /**
     * Set fax03
     *
     * @param  string $fax03
     * @return Order
     */
    public function setFax03($fax03)
    {
        $this->fax03 = $fax03;

        return $this;
    }

    /**
     * Get fax03
     *
     * @return string
     */
    public function getFax03()
    {
        return $this->fax03;
    }

    /**
     * Set zip01
     *
     * @param  string $zip01
     * @return Order
     */
    public function setZip01($zip01)
    {
        $this->zip01 = $zip01;

        return $this;
    }

    /**
     * Get zip01
     *
     * @return string
     */
    public function getZip01()
    {
        return $this->zip01;
    }

    /**
     * Set zip02
     *
     * @param  string $zip02
     * @return Order
     */
    public function setZip02($zip02)
    {
        $this->zip02 = $zip02;

        return $this;
    }

    /**
     * Get zip02
     *
     * @return string
     */
    public function getZip02()
    {
        return $this->zip02;
    }

    /**
     * Set zipcode
     *
     * @param  string $zipcode
     * @return Order
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set addr01
     *
     * @param  string $addr01
     * @return Order
     */
    public function setAddr01($addr01)
    {
        $this->addr01 = $addr01;

        return $this;
    }

    /**
     * Get addr01
     *
     * @return string
     */
    public function getAddr01()
    {
        return $this->addr01;
    }

    /**
     * Set addr02
     *
     * @param  string $addr02
     * @return Order
     */
    public function setAddr02($addr02)
    {
        $this->addr02 = $addr02;

        return $this;
    }

    /**
     * Get addr02
     *
     * @return string
     */
    public function getAddr02()
    {
        return $this->addr02;
    }

    /**
     * Set birth
     *
     * @param  \DateTime $birth
     * @return Order
     */
    public function setBirth($birth)
    {
        $this->birth = $birth;

        return $this;
    }

    /**
     * Get birth
     *
     * @return \DateTime
     */
    public function getBirth()
    {
        return $this->birth;
    }

    /**
     * Set subtotal
     *
     * @param  string $subtotal
     * @return Order
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return string
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set discount
     *
     * @param  string $discount
     * @return Order
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set delivery_fee_total
     *
     * @param  string $deliveryFeeTotal
     * @return Order
     */
    public function setDeliveryFeeTotal($deliveryFeeTotal)
    {
        $this->delivery_fee_total = $deliveryFeeTotal;

        return $this;
    }

    /**
     * Get delivery_fee_total
     *
     * @return string
     */
    public function getDeliveryFeeTotal()
    {
        return $this->delivery_fee_total;
    }

    /**
     * Set charge
     *
     * @param  string $charge
     * @return Order
     */
    public function setCharge($charge)
    {
        $this->charge = $charge;

        return $this;
    }

    /**
     * Get charge
     *
     * @return string
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * Set tax
     *
     * @param  string $tax
     * @return Order
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get tax
     *
     * @return string
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set total
     *
     * @param  string $total
     * @return Order
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set payment_total
     *
     * @param  string $paymentTotal
     * @return Order
     */
    public function setPaymentTotal($paymentTotal)
    {
        $this->payment_total = $paymentTotal;

        return $this;
    }

    /**
     * Get payment_total
     *
     * @return string
     */
    public function getPaymentTotal()
    {
        return $this->payment_total;
    }

    /**
     * Set payment_method
     *
     * @param  string $paymentMethod
     * @return Order
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->payment_method = $paymentMethod;

        return $this;
    }

    /**
     * Get payment_method
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * Set note
     *
     * @param  string $note
     * @return Order
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Order
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param  \DateTime $updateDate
     * @return Order
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set order_date
     *
     * @param  \DateTime $orderDate
     * @return Order
     */
    public function setOrderDate($orderDate)
    {
        $this->order_date = $orderDate;

        return $this;
    }

    /**
     * Get order_date
     *
     * @return \DateTime
     */
    public function getOrderDate()
    {
        return $this->order_date;
    }

    /**
     * Set commit_date
     *
     * @param  \DateTime $commitDate
     * @return Order
     */
    public function setCommitDate($commitDate)
    {
        $this->commit_date = $commitDate;

        return $this;
    }

    /**
     * Get commit_date
     *
     * @return \DateTime
     */
    public function getCommitDate()
    {
        return $this->commit_date;
    }

    /**
     * Set payment_date
     *
     * @param  \DateTime $paymentDate
     * @return Order
     */
    public function setPaymentDate($paymentDate)
    {
        $this->payment_date = $paymentDate;

        return $this;
    }

    /**
     * Get payment_date
     *
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->payment_date;
    }

    /**
     * Set del_flg
     *
     * @param  integer $delFlg
     * @return Order
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Add OrderDetails
     *
     * @param  \Eccube\Entity\OrderDetail $orderDetails
     * @return Order
     */
    public function addOrderDetail(\Eccube\Entity\OrderDetail $orderDetails)
    {
        $this->OrderDetails[] = $orderDetails;

        return $this;
    }

    /**
     * Remove OrderDetails
     *
     * @param \Eccube\Entity\OrderDetail $orderDetails
     */
    public function removeOrderDetail(\Eccube\Entity\OrderDetail $orderDetails)
    {
        $this->OrderDetails->removeElement($orderDetails);
    }

    /**
     * Get OrderDetails
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderDetails()
    {
        return $this->OrderDetails;
    }

    /**
     * Add Shippings
     *
     * @param  \Eccube\Entity\Shipping $shippings
     * @return Order
     */
    public function addShipping(\Eccube\Entity\Shipping $shippings)
    {
        $this->Shippings[] = $shippings;

        return $this;
    }

    /**
     * Remove Shippings
     *
     * @param \Eccube\Entity\Shipping $shippings
     */
    public function removeShipping(\Eccube\Entity\Shipping $shippings)
    {
        $this->Shippings->removeElement($shippings);
    }

    /**
     * Get Shippings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShippings()
    {
        return $this->Shippings;
    }

    /**
     * Add MailHistories
     *
     * @param  \Eccube\Entity\MailHistory $mailHistories
     * @return Order
     */
    public function addMailHistory(\Eccube\Entity\MailHistory $mailHistories)
    {
        $this->MailHistories[] = $mailHistories;

        return $this;
    }

    /**
     * Remove MailHistories
     *
     * @param \Eccube\Entity\MailHistory $mailHistories
     */
    public function removeMailHistory(\Eccube\Entity\MailHistory $mailHistories)
    {
        $this->MailHistories->removeElement($mailHistories);
    }

    /**
     * Get MailHistories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMailHistories()
    {
        return $this->MailHistories;
    }

    /**
     * Set Customer
     *
     * @param  \Eccube\Entity\Customer $customer
     * @return Order
     */
    public function setCustomer(\Eccube\Entity\Customer $customer = null)
    {
        $this->Customer = $customer;

        return $this;
    }

    /**
     * Get Customer
     *
     * @return \Eccube\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    /**
     * Set Country
     *
     * @param  \Eccube\Entity\Master\Country $country
     * @return Order
     */
    public function setCountry(\Eccube\Entity\Master\Country $country = null)
    {
        $this->Country = $country;

        return $this;
    }

    /**
     * Get Country
     *
     * @return \Eccube\Entity\Master\Country
     */
    public function getCountry()
    {
        return $this->Country;
    }

    /**
     * Set Pref
     *
     * @param  \Eccube\Entity\Master\Pref $pref
     * @return Order
     */
    public function setPref(\Eccube\Entity\Master\Pref $pref = null)
    {
        $this->Pref = $pref;

        return $this;
    }

    /**
     * Get Pref
     *
     * @return \Eccube\Entity\Master\Pref
     */
    public function getPref()
    {
        return $this->Pref;
    }

    /**
     * Set Sex
     *
     * @param  \Eccube\Entity\Master\Sex $sex
     * @return Order
     */
    public function setSex(\Eccube\Entity\Master\Sex $sex = null)
    {
        $this->Sex = $sex;

        return $this;
    }

    /**
     * Get Sex
     *
     * @return \Eccube\Entity\Master\Sex
     */
    public function getSex()
    {
        return $this->Sex;
    }

    /**
     * Set Job
     *
     * @param  \Eccube\Entity\Master\Job $job
     * @return Order
     */
    public function setJob(\Eccube\Entity\Master\Job $job = null)
    {
        $this->Job = $job;

        return $this;
    }

    /**
     * Get Job
     *
     * @return \Eccube\Entity\Master\Job
     */
    public function getJob()
    {
        return $this->Job;
    }

    /**
     * Set Payment
     *
     * @param  \Eccube\Entity\Payment $payment
     * @return Order
     */
    public function setPayment(\Eccube\Entity\Payment $payment = null)
    {
        $this->Payment = $payment;

        return $this;
    }

    /**
     * Get Payment
     *
     * @return \Eccube\Entity\Payment
     */
    public function getPayment()
    {
        return $this->Payment;
    }

    /**
     * Set DeviceType
     *
     * @param  \Eccube\Entity\Master\DeviceType $deviceType
     * @return Order
     */
    public function setDeviceType(\Eccube\Entity\Master\DeviceType $deviceType = null)
    {
        $this->DeviceType = $deviceType;

        return $this;
    }

    /**
     * Get DeviceType
     *
     * @return \Eccube\Entity\Master\DeviceType
     */
    public function getDeviceType()
    {
        return $this->DeviceType;
    }

    /**
     * Set CustomerOrderStatus
     *
     * @param  \Eccube\Entity\Master\CustomerOrderStatus $customerOrderStatus
     * @return Order
     */
    public function setCustomerOrderStatus(\Eccube\Entity\Master\CustomerOrderStatus $customerOrderStatus = null)
    {
        $this->CustomerOrderStatus = $customerOrderStatus;

        return $this;
    }

    /**
     * Get CustomerOrderStatus
     *
     * @return \Eccube\Entity\Master\CustomerOrderStatus
     */
    public function getCustomerOrderStatus()
    {
        return $this->CustomerOrderStatus;
    }

    /**
     * Set OrderStatus
     *
     * @param  \Eccube\Entity\Master\OrderStatus $orderStatus
     * @return Order
     */
    public function setOrderStatus(\Eccube\Entity\Master\OrderStatus $orderStatus = null)
    {
        $this->OrderStatus = $orderStatus;

        return $this;
    }

    /**
     * Get OrderStatus
     *
     * @return \Eccube\Entity\Master\OrderStatus
     */
    public function getOrderStatus()
    {
        return $this->OrderStatus;
    }

    /**
     * Set OrderStatusColor
     *
     * @param  \Eccube\Entity\Master\OrderStatusColor $orderStatusColor
     * @return Order
     */
    public function setOrderStatusColor(\Eccube\Entity\Master\OrderStatusColor $orderStatusColor = null)
    {
        $this->OrderStatusColor = $orderStatusColor;

        return $this;
    }

    /**
     * Get OrderStatusColor
     *
     * @return \Eccube\Entity\Master\OrderStatusColor
     */
    public function getOrderStatusColor()
    {
        return $this->OrderStatusColor;
    }
}
