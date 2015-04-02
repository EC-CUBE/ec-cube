<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderTemp
 */
class OrderTemp extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var string
     */
    private $orderTempId;

    /**
     * @var integer
     */
    private $customerId;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $orderName01;

    /**
     * @var string
     */
    private $orderName02;

    /**
     * @var string
     */
    private $orderKana01;

    /**
     * @var string
     */
    private $orderKana02;

    /**
     * @var string
     */
    private $orderCompanyName;

    /**
     * @var string
     */
    private $orderEmail;

    /**
     * @var string
     */
    private $orderTel01;

    /**
     * @var string
     */
    private $orderTel02;

    /**
     * @var string
     */
    private $orderTel03;

    /**
     * @var string
     */
    private $orderFax01;

    /**
     * @var string
     */
    private $orderFax02;

    /**
     * @var string
     */
    private $orderFax03;

    /**
     * @var string
     */
    private $orderZip01;

    /**
     * @var string
     */
    private $orderZip02;

    /**
     * @var string
     */
    private $orderZipcode;

    /**
     * @var integer
     */
    private $orderCountryId;

    /**
     * @var integer
     */
    private $orderPref;

    /**
     * @var string
     */
    private $orderAddr01;

    /**
     * @var string
     */
    private $orderAddr02;

    /**
     * @var integer
     */
    private $orderSex;

    /**
     * @var \DateTime
     */
    private $orderBirth;

    /**
     * @var integer
     */
    private $orderJob;

    /**
     * @var string
     */
    private $subtotal;

    /**
     * @var string
     */
    private $discount;

    /**
     * @var integer
     */
    private $delivId;

    /**
     * @var string
     */
    private $delivFee;

    /**
     * @var string
     */
    private $charge;

    /**
     * @var string
     */
    private $usePoint;

    /**
     * @var string
     */
    private $addPoint;

    /**
     * @var string
     */
    private $birthPoint;

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
    private $paymentTotal;

    /**
     * @var integer
     */
    private $paymentId;

    /**
     * @var string
     */
    private $paymentMethod;

    /**
     * @var string
     */
    private $note;

    /**
     * @var integer
     */
    private $mailFlag;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $delivCheck;

    /**
     * @var integer
     */
    private $pointCheck;

    /**
     * @var \DateTime
     */
    private $createDate;

    /**
     * @var \DateTime
     */
    private $updateDate;

    /**
     * @var integer
     */
    private $deviceTypeId;

    /**
     * @var integer
     */
    private $delFlg;

    /**
     * @var integer
     */
    private $orderId;

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
     * @var string
     */
    private $session;


    /**
     * Get orderTempId
     *
     * @return string 
     */
    public function getOrderTempId()
    {
        return $this->orderTempId;
    }

    /**
     * Set customerId
     *
     * @param integer $customerId
     * @return OrderTemp
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return integer 
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return OrderTemp
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
     * Set orderName01
     *
     * @param string $orderName01
     * @return OrderTemp
     */
    public function setOrderName01($orderName01)
    {
        $this->orderName01 = $orderName01;

        return $this;
    }

    /**
     * Get orderName01
     *
     * @return string 
     */
    public function getOrderName01()
    {
        return $this->orderName01;
    }

    /**
     * Set orderName02
     *
     * @param string $orderName02
     * @return OrderTemp
     */
    public function setOrderName02($orderName02)
    {
        $this->orderName02 = $orderName02;

        return $this;
    }

    /**
     * Get orderName02
     *
     * @return string 
     */
    public function getOrderName02()
    {
        return $this->orderName02;
    }

    /**
     * Set orderKana01
     *
     * @param string $orderKana01
     * @return OrderTemp
     */
    public function setOrderKana01($orderKana01)
    {
        $this->orderKana01 = $orderKana01;

        return $this;
    }

    /**
     * Get orderKana01
     *
     * @return string 
     */
    public function getOrderKana01()
    {
        return $this->orderKana01;
    }

    /**
     * Set orderKana02
     *
     * @param string $orderKana02
     * @return OrderTemp
     */
    public function setOrderKana02($orderKana02)
    {
        $this->orderKana02 = $orderKana02;

        return $this;
    }

    /**
     * Get orderKana02
     *
     * @return string 
     */
    public function getOrderKana02()
    {
        return $this->orderKana02;
    }

    /**
     * Set orderCompanyName
     *
     * @param string $orderCompanyName
     * @return OrderTemp
     */
    public function setOrderCompanyName($orderCompanyName)
    {
        $this->orderCompanyName = $orderCompanyName;

        return $this;
    }

    /**
     * Get orderCompanyName
     *
     * @return string 
     */
    public function getOrderCompanyName()
    {
        return $this->orderCompanyName;
    }

    /**
     * Set orderEmail
     *
     * @param string $orderEmail
     * @return OrderTemp
     */
    public function setOrderEmail($orderEmail)
    {
        $this->orderEmail = $orderEmail;

        return $this;
    }

    /**
     * Get orderEmail
     *
     * @return string 
     */
    public function getOrderEmail()
    {
        return $this->orderEmail;
    }

    /**
     * Set orderTel01
     *
     * @param string $orderTel01
     * @return OrderTemp
     */
    public function setOrderTel01($orderTel01)
    {
        $this->orderTel01 = $orderTel01;

        return $this;
    }

    /**
     * Get orderTel01
     *
     * @return string 
     */
    public function getOrderTel01()
    {
        return $this->orderTel01;
    }

    /**
     * Set orderTel02
     *
     * @param string $orderTel02
     * @return OrderTemp
     */
    public function setOrderTel02($orderTel02)
    {
        $this->orderTel02 = $orderTel02;

        return $this;
    }

    /**
     * Get orderTel02
     *
     * @return string 
     */
    public function getOrderTel02()
    {
        return $this->orderTel02;
    }

    /**
     * Set orderTel03
     *
     * @param string $orderTel03
     * @return OrderTemp
     */
    public function setOrderTel03($orderTel03)
    {
        $this->orderTel03 = $orderTel03;

        return $this;
    }

    /**
     * Get orderTel03
     *
     * @return string 
     */
    public function getOrderTel03()
    {
        return $this->orderTel03;
    }

    /**
     * Set orderFax01
     *
     * @param string $orderFax01
     * @return OrderTemp
     */
    public function setOrderFax01($orderFax01)
    {
        $this->orderFax01 = $orderFax01;

        return $this;
    }

    /**
     * Get orderFax01
     *
     * @return string 
     */
    public function getOrderFax01()
    {
        return $this->orderFax01;
    }

    /**
     * Set orderFax02
     *
     * @param string $orderFax02
     * @return OrderTemp
     */
    public function setOrderFax02($orderFax02)
    {
        $this->orderFax02 = $orderFax02;

        return $this;
    }

    /**
     * Get orderFax02
     *
     * @return string 
     */
    public function getOrderFax02()
    {
        return $this->orderFax02;
    }

    /**
     * Set orderFax03
     *
     * @param string $orderFax03
     * @return OrderTemp
     */
    public function setOrderFax03($orderFax03)
    {
        $this->orderFax03 = $orderFax03;

        return $this;
    }

    /**
     * Get orderFax03
     *
     * @return string 
     */
    public function getOrderFax03()
    {
        return $this->orderFax03;
    }

    /**
     * Set orderZip01
     *
     * @param string $orderZip01
     * @return OrderTemp
     */
    public function setOrderZip01($orderZip01)
    {
        $this->orderZip01 = $orderZip01;

        return $this;
    }

    /**
     * Get orderZip01
     *
     * @return string 
     */
    public function getOrderZip01()
    {
        return $this->orderZip01;
    }

    /**
     * Set orderZip02
     *
     * @param string $orderZip02
     * @return OrderTemp
     */
    public function setOrderZip02($orderZip02)
    {
        $this->orderZip02 = $orderZip02;

        return $this;
    }

    /**
     * Get orderZip02
     *
     * @return string 
     */
    public function getOrderZip02()
    {
        return $this->orderZip02;
    }

    /**
     * Set orderZipcode
     *
     * @param string $orderZipcode
     * @return OrderTemp
     */
    public function setOrderZipcode($orderZipcode)
    {
        $this->orderZipcode = $orderZipcode;

        return $this;
    }

    /**
     * Get orderZipcode
     *
     * @return string 
     */
    public function getOrderZipcode()
    {
        return $this->orderZipcode;
    }

    /**
     * Set orderCountryId
     *
     * @param integer $orderCountryId
     * @return OrderTemp
     */
    public function setOrderCountryId($orderCountryId)
    {
        $this->orderCountryId = $orderCountryId;

        return $this;
    }

    /**
     * Get orderCountryId
     *
     * @return integer 
     */
    public function getOrderCountryId()
    {
        return $this->orderCountryId;
    }

    /**
     * Set orderPref
     *
     * @param integer $orderPref
     * @return OrderTemp
     */
    public function setOrderPref($orderPref)
    {
        $this->orderPref = $orderPref;

        return $this;
    }

    /**
     * Get orderPref
     *
     * @return integer 
     */
    public function getOrderPref()
    {
        return $this->orderPref;
    }

    /**
     * Set orderAddr01
     *
     * @param string $orderAddr01
     * @return OrderTemp
     */
    public function setOrderAddr01($orderAddr01)
    {
        $this->orderAddr01 = $orderAddr01;

        return $this;
    }

    /**
     * Get orderAddr01
     *
     * @return string 
     */
    public function getOrderAddr01()
    {
        return $this->orderAddr01;
    }

    /**
     * Set orderAddr02
     *
     * @param string $orderAddr02
     * @return OrderTemp
     */
    public function setOrderAddr02($orderAddr02)
    {
        $this->orderAddr02 = $orderAddr02;

        return $this;
    }

    /**
     * Get orderAddr02
     *
     * @return string 
     */
    public function getOrderAddr02()
    {
        return $this->orderAddr02;
    }

    /**
     * Set orderSex
     *
     * @param integer $orderSex
     * @return OrderTemp
     */
    public function setOrderSex($orderSex)
    {
        $this->orderSex = $orderSex;

        return $this;
    }

    /**
     * Get orderSex
     *
     * @return integer 
     */
    public function getOrderSex()
    {
        return $this->orderSex;
    }

    /**
     * Set orderBirth
     *
     * @param \DateTime $orderBirth
     * @return OrderTemp
     */
    public function setOrderBirth($orderBirth)
    {
        $this->orderBirth = $orderBirth;

        return $this;
    }

    /**
     * Get orderBirth
     *
     * @return \DateTime 
     */
    public function getOrderBirth()
    {
        return $this->orderBirth;
    }

    /**
     * Set orderJob
     *
     * @param integer $orderJob
     * @return OrderTemp
     */
    public function setOrderJob($orderJob)
    {
        $this->orderJob = $orderJob;

        return $this;
    }

    /**
     * Get orderJob
     *
     * @return integer 
     */
    public function getOrderJob()
    {
        return $this->orderJob;
    }

    /**
     * Set subtotal
     *
     * @param string $subtotal
     * @return OrderTemp
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
     * @return OrderTemp
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
     * Set delivId
     *
     * @param integer $delivId
     * @return OrderTemp
     */
    public function setDelivId($delivId)
    {
        $this->delivId = $delivId;

        return $this;
    }

    /**
     * Get delivId
     *
     * @return integer 
     */
    public function getDelivId()
    {
        return $this->delivId;
    }

    /**
     * Set delivFee
     *
     * @param string $delivFee
     * @return OrderTemp
     */
    public function setDelivFee($delivFee)
    {
        $this->delivFee = $delivFee;

        return $this;
    }

    /**
     * Get delivFee
     *
     * @return string 
     */
    public function getDelivFee()
    {
        return $this->delivFee;
    }

    /**
     * Set charge
     *
     * @param string $charge
     * @return OrderTemp
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
     * Set usePoint
     *
     * @param string $usePoint
     * @return OrderTemp
     */
    public function setUsePoint($usePoint)
    {
        $this->usePoint = $usePoint;

        return $this;
    }

    /**
     * Get usePoint
     *
     * @return string 
     */
    public function getUsePoint()
    {
        return $this->usePoint;
    }

    /**
     * Set addPoint
     *
     * @param string $addPoint
     * @return OrderTemp
     */
    public function setAddPoint($addPoint)
    {
        $this->addPoint = $addPoint;

        return $this;
    }

    /**
     * Get addPoint
     *
     * @return string 
     */
    public function getAddPoint()
    {
        return $this->addPoint;
    }

    /**
     * Set birthPoint
     *
     * @param string $birthPoint
     * @return OrderTemp
     */
    public function setBirthPoint($birthPoint)
    {
        $this->birthPoint = $birthPoint;

        return $this;
    }

    /**
     * Get birthPoint
     *
     * @return string 
     */
    public function getBirthPoint()
    {
        return $this->birthPoint;
    }

    /**
     * Set tax
     *
     * @param string $tax
     * @return OrderTemp
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
     * @return OrderTemp
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
     * Set paymentTotal
     *
     * @param string $paymentTotal
     * @return OrderTemp
     */
    public function setPaymentTotal($paymentTotal)
    {
        $this->paymentTotal = $paymentTotal;

        return $this;
    }

    /**
     * Get paymentTotal
     *
     * @return string 
     */
    public function getPaymentTotal()
    {
        return $this->paymentTotal;
    }

    /**
     * Set paymentId
     *
     * @param integer $paymentId
     * @return OrderTemp
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * Get paymentId
     *
     * @return integer 
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     * @return OrderTemp
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string 
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return OrderTemp
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
     * Set mailFlag
     *
     * @param integer $mailFlag
     * @return OrderTemp
     */
    public function setMailFlag($mailFlag)
    {
        $this->mailFlag = $mailFlag;

        return $this;
    }

    /**
     * Get mailFlag
     *
     * @return integer 
     */
    public function getMailFlag()
    {
        return $this->mailFlag;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return OrderTemp
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
     * Set delivCheck
     *
     * @param integer $delivCheck
     * @return OrderTemp
     */
    public function setDelivCheck($delivCheck)
    {
        $this->delivCheck = $delivCheck;

        return $this;
    }

    /**
     * Get delivCheck
     *
     * @return integer 
     */
    public function getDelivCheck()
    {
        return $this->delivCheck;
    }

    /**
     * Set pointCheck
     *
     * @param integer $pointCheck
     * @return OrderTemp
     */
    public function setPointCheck($pointCheck)
    {
        $this->pointCheck = $pointCheck;

        return $this;
    }

    /**
     * Get pointCheck
     *
     * @return integer 
     */
    public function getPointCheck()
    {
        return $this->pointCheck;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return OrderTemp
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return OrderTemp
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set deviceTypeId
     *
     * @param integer $deviceTypeId
     * @return OrderTemp
     */
    public function setDeviceTypeId($deviceTypeId)
    {
        $this->deviceTypeId = $deviceTypeId;

        return $this;
    }

    /**
     * Get deviceTypeId
     *
     * @return integer 
     */
    public function getDeviceTypeId()
    {
        return $this->deviceTypeId;
    }

    /**
     * Set delFlg
     *
     * @param integer $delFlg
     * @return OrderTemp
     */
    public function setDelFlg($delFlg)
    {
        $this->delFlg = $delFlg;

        return $this;
    }

    /**
     * Get delFlg
     *
     * @return integer 
     */
    public function getDelFlg()
    {
        return $this->delFlg;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     * @return OrderTemp
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set memo01
     *
     * @param string $memo01
     * @return OrderTemp
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
     * @return OrderTemp
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
     * @return OrderTemp
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
     * @return OrderTemp
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
     * @return OrderTemp
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
     * @return OrderTemp
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
     * @return OrderTemp
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
     * @return OrderTemp
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
     * @return OrderTemp
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
     * @return OrderTemp
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
     * Set session
     *
     * @param string $session
     * @return OrderTemp
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return string 
     */
    public function getSession()
    {
        return $this->session;
    }
}
