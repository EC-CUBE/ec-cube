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
    private $id;

    /**
     * @var string
     */
    private $product_name;

    /**
     * @var string
     */
    private $product_code;

    /**
     * @var string
     */
    private $classcategory_name1;

    /**
     * @var string
     */
    private $classcategory_name2;

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
    private $point_rate;

    /**
     * @var string
     */
    private $tax_rate;

    /**
     * @var integer
     */
    private $tax_rule;

    /**
     * @var \Eccube\Entity\Order
     */
    private $Order;

    /**
     * @var \Eccube\Entity\Product
     */
    private $Product;

    /**
     * @var \Eccube\Entity\ProductClass
     */
    private $ProductClass;


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
     * Set product_name
     *
     * @param string $productName
     * @return OrderDetail
     */
    public function setProductName($productName)
    {
        $this->product_name = $productName;

        return $this;
    }

    /**
     * Get product_name
     *
     * @return string 
     */
    public function getProductName()
    {
        return $this->product_name;
    }

    /**
     * Set product_code
     *
     * @param string $productCode
     * @return OrderDetail
     */
    public function setProductCode($productCode)
    {
        $this->product_code = $productCode;

        return $this;
    }

    /**
     * Get product_code
     *
     * @return string 
     */
    public function getProductCode()
    {
        return $this->product_code;
    }

    /**
     * Set classcategory_name1
     *
     * @param string $classcategoryName1
     * @return OrderDetail
     */
    public function setClasscategoryName1($classcategoryName1)
    {
        $this->classcategory_name1 = $classcategoryName1;

        return $this;
    }

    /**
     * Get classcategory_name1
     *
     * @return string 
     */
    public function getClasscategoryName1()
    {
        return $this->classcategory_name1;
    }

    /**
     * Set classcategory_name2
     *
     * @param string $classcategoryName2
     * @return OrderDetail
     */
    public function setClasscategoryName2($classcategoryName2)
    {
        $this->classcategory_name2 = $classcategoryName2;

        return $this;
    }

    /**
     * Get classcategory_name2
     *
     * @return string 
     */
    public function getClasscategoryName2()
    {
        return $this->classcategory_name2;
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
     * Set point_rate
     *
     * @param string $pointRate
     * @return OrderDetail
     */
    public function setPointRate($pointRate)
    {
        $this->point_rate = $pointRate;

        return $this;
    }

    /**
     * Get point_rate
     *
     * @return string 
     */
    public function getPointRate()
    {
        return $this->point_rate;
    }

    /**
     * Set tax_rate
     *
     * @param string $taxRate
     * @return OrderDetail
     */
    public function setTaxRate($taxRate)
    {
        $this->tax_rate = $taxRate;

        return $this;
    }

    /**
     * Get tax_rate
     *
     * @return string 
     */
    public function getTaxRate()
    {
        return $this->tax_rate;
    }

    /**
     * Set tax_rule
     *
     * @param integer $taxRule
     * @return OrderDetail
     */
    public function setTaxRule($taxRule)
    {
        $this->tax_rule = $taxRule;

        return $this;
    }

    /**
     * Get tax_rule
     *
     * @return integer 
     */
    public function getTaxRule()
    {
        return $this->tax_rule;
    }

    /**
     * Set Order
     *
     * @param \Eccube\Entity\Order $order
     * @return OrderDetail
     */
    public function setOrder(\Eccube\Entity\Order $order)
    {
        $this->Order = $order;

        return $this;
    }

    /**
     * Get Order
     *
     * @return \Eccube\Entity\Order 
     */
    public function getOrder()
    {
        return $this->Order;
    }

    /**
     * Set Product
     *
     * @param \Eccube\Entity\Product $product
     * @return OrderDetail
     */
    public function setProduct(\Eccube\Entity\Product $product)
    {
        $this->Product = $product;

        return $this;
    }

    /**
     * Get Product
     *
     * @return \Eccube\Entity\Product 
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * Set ProductClass
     *
     * @param \Eccube\Entity\ProductClass $productClass
     * @return OrderDetail
     */
    public function setProductClass(\Eccube\Entity\ProductClass $productClass)
    {
        $this->ProductClass = $productClass;

        return $this;
    }

    /**
     * Get ProductClass
     *
     * @return \Eccube\Entity\ProductClass 
     */
    public function getProductClass()
    {
        return $this->ProductClass;
    }
}
