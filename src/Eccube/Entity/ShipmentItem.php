<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShipmentItem
 */
class ShipmentItem extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $shippingId;

    /**
     * @var integer
     */
    private $productClassId;

    /**
     * @var integer
     */
    private $orderId;

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
     * Set shippingId
     *
     * @param integer $shippingId
     * @return ShipmentItem
     */
    public function setShippingId($shippingId)
    {
        $this->shippingId = $shippingId;

        return $this;
    }

    /**
     * Get shippingId
     *
     * @return integer 
     */
    public function getShippingId()
    {
        return $this->shippingId;
    }

    /**
     * Set productClassId
     *
     * @param integer $productClassId
     * @return ShipmentItem
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
     * Set orderId
     *
     * @param integer $orderId
     * @return ShipmentItem
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
     * Set productName
     *
     * @param string $productName
     * @return ShipmentItem
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
     * @return ShipmentItem
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
     * @return ShipmentItem
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
     * @return ShipmentItem
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
     * @return ShipmentItem
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
     * @return ShipmentItem
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
}
