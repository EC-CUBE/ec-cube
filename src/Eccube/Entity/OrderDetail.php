<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderDetail
 */
class OrderDetail extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $orderDetailId;

    /**
     * @var integer
     */
    private $orderId;

    /**
     * @var integer
     */
    private $productId;

    /**
     * @var integer
     */
    private $productClassId;

    /**
     * @var string
     */
    private $productName;

    /**
     * @var string
     */
    private $productCode;

    /**
     * @var string
     */
    private $classcategoryName1;

    /**
     * @var string
     */
    private $classcategoryName2;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @var string
     */
    private $pointRate;

    /**
     * @var string
     */
    private $taxRate;

    /**
     * @var integer
     */
    private $taxRule;


    /**
     * Get orderDetailId
     *
     * @return integer 
     */
    public function getOrderDetailId()
    {
        return $this->orderDetailId;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     * @return OrderDetail
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
     * Set productId
     *
     * @param integer $productId
     * @return OrderDetail
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set productClassId
     *
     * @param integer $productClassId
     * @return OrderDetail
     */
    public function setProductClassId($productClassId)
    {
        $this->productClassId = $productClassId;

        return $this;
    }

    /**
     * Get productClassId
     *
     * @return integer 
     */
    public function getProductClassId()
    {
        return $this->productClassId;
    }

    /**
     * Set productName
     *
     * @param string $productName
     * @return OrderDetail
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string 
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set productCode
     *
     * @param string $productCode
     * @return OrderDetail
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * Get productCode
     *
     * @return string 
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * Set classcategoryName1
     *
     * @param string $classcategoryName1
     * @return OrderDetail
     */
    public function setClasscategoryName1($classcategoryName1)
    {
        $this->classcategoryName1 = $classcategoryName1;

        return $this;
    }

    /**
     * Get classcategoryName1
     *
     * @return string 
     */
    public function getClasscategoryName1()
    {
        return $this->classcategoryName1;
    }

    /**
     * Set classcategoryName2
     *
     * @param string $classcategoryName2
     * @return OrderDetail
     */
    public function setClasscategoryName2($classcategoryName2)
    {
        $this->classcategoryName2 = $classcategoryName2;

        return $this;
    }

    /**
     * Get classcategoryName2
     *
     * @return string 
     */
    public function getClasscategoryName2()
    {
        return $this->classcategoryName2;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return OrderDetail
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set quantity
     *
     * @param string $quantity
     * @return OrderDetail
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set pointRate
     *
     * @param string $pointRate
     * @return OrderDetail
     */
    public function setPointRate($pointRate)
    {
        $this->pointRate = $pointRate;

        return $this;
    }

    /**
     * Get pointRate
     *
     * @return string 
     */
    public function getPointRate()
    {
        return $this->pointRate;
    }

    /**
     * Set taxRate
     *
     * @param string $taxRate
     * @return OrderDetail
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * Get taxRate
     *
     * @return string 
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * Set taxRule
     *
     * @param integer $taxRule
     * @return OrderDetail
     */
    public function setTaxRule($taxRule)
    {
        $this->taxRule = $taxRule;

        return $this;
    }

    /**
     * Get taxRule
     *
     * @return integer 
     */
    public function getTaxRule()
    {
        return $this->taxRule;
    }
}
