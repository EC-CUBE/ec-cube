<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerFavoriteProduct
 */
class CustomerFavoriteProduct extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $customer_id;

    /**
     * @var integer
     */
    private $product_id;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var \Eccube\Entity\Customer
     */
    private $Customer;

    /**
     * @var \Eccube\Entity\Product
     */
    private $Product;

    /**
     * Get customer_id
     *
     * @return integer 
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * Get product_id
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return CustomerFavoriteProduct
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
     * @return CustomerFavoriteProduct
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
     * Set Customer
     *
     * @param \Eccube\Entity\Customer $customer
     * @return CustomerFavoriteProduct
     */
    public function setCustomer(\Eccube\Entity\Customer $customer = null)
    {
        $this->Customer = $customer;
        $this->customer_id = $customer ? $customer->getId() : null;

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
     * Set Product
     *
     * @param \Eccube\Entity\Product $product
     * @return CustomerFavoriteProduct
     */
    public function setProduct(\Eccube\Entity\Product $product = null)
    {
        $this->Product = $product;
        $this->product_id = $product ? $product->getId() : null;

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
     * Set customer_id
     *
     * @param integer $customerId
     * @return CustomerFavoriteProduct
     */
    public function setCustomerId($customerId)
    {
        $this->customer_id = $customerId;

        return $this;
    }

    /**
     * Set product_id
     *
     * @param integer $productId
     * @return CustomerFavoriteProduct
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;

        return $this;
    }
}
