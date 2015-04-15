<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 */
class Order extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

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
    private $deliv_fee;

    /**
     * @var string
     */
    private $charge;

    /**
     * @var string
     */
    private $use_point;

    /**
     * @var string
     */
    private $add_point;

    /**
     * @var string
     */
    private $birth_point;

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
     * @var integer
     */
    private $status;

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
     * @var string
     */
    private $memo01;

    /**
     * @var string
     */
    private $memo02;

    /**
     * @var string
     */
    private $memo03;

    /**
     * @var string
     */
    private $memo04;

    /**
     * @var string
     */
    private $memo05;

    /**
     * @var string
     */
    private $memo06;

    /**
     * @var string
     */
    private $memo07;

    /**
     * @var string
     */
    private $memo08;

    /**
     * @var string
     */
    private $memo09;

    /**
     * @var string
     */
    private $memo10;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $OrderDetails;

    /**
     * @var \Eccube\Entity\OrderTemp
     */
    private $OrderTemp;

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
     * @var \Eccube\Entity\Deliv
     */
    private $Deliv;

    /**
     * @var \Eccube\Entity\Payment
     */
    private $Payment;

    /**
     * @var \Eccube\Entity\Master\DeviceType
     */
    private $DeviceType;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->OrderDetails = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set message
     *
     * @param string $message
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
     * @param string $name01
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
     * @param string $name02
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
     * @param string $kana01
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
     * @param string $kana02
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
     * @param string $companyName
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
     * @param string $email
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
     * @param string $tel01
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
     * @param string $tel02
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
     * @param string $tel03
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
     * @param string $fax01
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
     * @param string $fax02
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
     * @param string $fax03
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
     * @param string $zip01
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
     * @param string $zip02
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
     * @param string $zipcode
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
     * @param string $addr01
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
     * @param string $addr02
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
     * @param \DateTime $birth
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
     * @param string $subtotal
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
     * @param string $discount
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
     * Set deliv_fee
     *
     * @param string $delivFee
     * @return Order
     */
    public function setDelivFee($delivFee)
    {
        $this->deliv_fee = $delivFee;

        return $this;
    }

    /**
     * Get deliv_fee
     *
     * @return string 
     */
    public function getDelivFee()
    {
        return $this->deliv_fee;
    }

    /**
     * Set charge
     *
     * @param string $charge
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
     * Set use_point
     *
     * @param string $usePoint
     * @return Order
     */
    public function setUsePoint($usePoint)
    {
        $this->use_point = $usePoint;

        return $this;
    }

    /**
     * Get use_point
     *
     * @return string 
     */
    public function getUsePoint()
    {
        return $this->use_point;
    }

    /**
     * Set add_point
     *
     * @param string $addPoint
     * @return Order
     */
    public function setAddPoint($addPoint)
    {
        $this->add_point = $addPoint;

        return $this;
    }

    /**
     * Get add_point
     *
     * @return string 
     */
    public function getAddPoint()
    {
        return $this->add_point;
    }

    /**
     * Set birth_point
     *
     * @param string $birthPoint
     * @return Order
     */
    public function setBirthPoint($birthPoint)
    {
        $this->birth_point = $birthPoint;

        return $this;
    }

    /**
     * Get birth_point
     *
     * @return string 
     */
    public function getBirthPoint()
    {
        return $this->birth_point;
    }

    /**
     * Set tax
     *
     * @param string $tax
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
     * @param string $total
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
     * @param string $paymentTotal
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
     * @param string $paymentMethod
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
     * @param string $note
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
     * Set status
     *
     * @param integer $status
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
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
     * @param \DateTime $updateDate
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
     * Set commit_date
     *
     * @param \DateTime $commitDate
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
     * @param \DateTime $paymentDate
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
     * @param integer $delFlg
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
     * Set memo01
     *
     * @param string $memo01
     * @return Order
     */
    public function setMemo01($memo01)
    {
        $this->memo01 = $memo01;

        return $this;
    }

    /**
     * Get memo01
     *
     * @return string 
     */
    public function getMemo01()
    {
        return $this->memo01;
    }

    /**
     * Set memo02
     *
     * @param string $memo02
     * @return Order
     */
    public function setMemo02($memo02)
    {
        $this->memo02 = $memo02;

        return $this;
    }

    /**
     * Get memo02
     *
     * @return string 
     */
    public function getMemo02()
    {
        return $this->memo02;
    }

    /**
     * Set memo03
     *
     * @param string $memo03
     * @return Order
     */
    public function setMemo03($memo03)
    {
        $this->memo03 = $memo03;

        return $this;
    }

    /**
     * Get memo03
     *
     * @return string 
     */
    public function getMemo03()
    {
        return $this->memo03;
    }

    /**
     * Set memo04
     *
     * @param string $memo04
     * @return Order
     */
    public function setMemo04($memo04)
    {
        $this->memo04 = $memo04;

        return $this;
    }

    /**
     * Get memo04
     *
     * @return string 
     */
    public function getMemo04()
    {
        return $this->memo04;
    }

    /**
     * Set memo05
     *
     * @param string $memo05
     * @return Order
     */
    public function setMemo05($memo05)
    {
        $this->memo05 = $memo05;

        return $this;
    }

    /**
     * Get memo05
     *
     * @return string 
     */
    public function getMemo05()
    {
        return $this->memo05;
    }

    /**
     * Set memo06
     *
     * @param string $memo06
     * @return Order
     */
    public function setMemo06($memo06)
    {
        $this->memo06 = $memo06;

        return $this;
    }

    /**
     * Get memo06
     *
     * @return string 
     */
    public function getMemo06()
    {
        return $this->memo06;
    }

    /**
     * Set memo07
     *
     * @param string $memo07
     * @return Order
     */
    public function setMemo07($memo07)
    {
        $this->memo07 = $memo07;

        return $this;
    }

    /**
     * Get memo07
     *
     * @return string 
     */
    public function getMemo07()
    {
        return $this->memo07;
    }

    /**
     * Set memo08
     *
     * @param string $memo08
     * @return Order
     */
    public function setMemo08($memo08)
    {
        $this->memo08 = $memo08;

        return $this;
    }

    /**
     * Get memo08
     *
     * @return string 
     */
    public function getMemo08()
    {
        return $this->memo08;
    }

    /**
     * Set memo09
     *
     * @param string $memo09
     * @return Order
     */
    public function setMemo09($memo09)
    {
        $this->memo09 = $memo09;

        return $this;
    }

    /**
     * Get memo09
     *
     * @return string 
     */
    public function getMemo09()
    {
        return $this->memo09;
    }

    /**
     * Set memo10
     *
     * @param string $memo10
     * @return Order
     */
    public function setMemo10($memo10)
    {
        $this->memo10 = $memo10;

        return $this;
    }

    /**
     * Get memo10
     *
     * @return string 
     */
    public function getMemo10()
    {
        return $this->memo10;
    }

    /**
     * Add OrderDetails
     *
     * @param \Eccube\Entity\OrderDetail $orderDetails
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
     * Set OrderTemp
     *
     * @param \Eccube\Entity\OrderTemp $orderTemp
     * @return Order
     */
    public function setOrderTemp(\Eccube\Entity\OrderTemp $orderTemp = null)
    {
        $this->OrderTemp = $orderTemp;

        return $this;
    }

    /**
     * Get OrderTemp
     *
     * @return \Eccube\Entity\OrderTemp 
     */
    public function getOrderTemp()
    {
        return $this->OrderTemp;
    }

    /**
     * Set Customer
     *
     * @param \Eccube\Entity\Customer $customer
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
     * @param \Eccube\Entity\Master\Country $country
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
     * @param \Eccube\Entity\Master\Pref $pref
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
     * @param \Eccube\Entity\Master\Sex $sex
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
     * @param \Eccube\Entity\Master\Job $job
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
     * Set Deliv
     *
     * @param \Eccube\Entity\Deliv $deliv
     * @return Order
     */
    public function setDeliv(\Eccube\Entity\Deliv $deliv = null)
    {
        $this->Deliv = $deliv;

        return $this;
    }

    /**
     * Get Deliv
     *
     * @return \Eccube\Entity\Deliv 
     */
    public function getDeliv()
    {
        return $this->Deliv;
    }

    /**
     * Set Payment
     *
     * @param \Eccube\Entity\Payment $payment
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
     * @param \Eccube\Entity\Master\DeviceType $deviceType
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
}
