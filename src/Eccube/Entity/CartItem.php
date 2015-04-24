<?php

namespace Eccube\Entity;

class CartItem extends \Eccube\Entity\AbstractEntity
{

    private $class_name;
    private $class_id;
    private $price;
    private $quantity;
    private $object;

    public function __construct()
    {
    }

    public function __sleep()
    {
        return array('class_name', 'class_id', 'price', 'quantity');
    }

    /**
     * @param string $class_name
     * @return CartItem
     */
    public function setClassName($class_name)
    {
        $this->class_name = $class_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * @param string $class_id
     * @return CartItem
     */
    public function setClassId($class_id)
    {
        $this->class_id = $class_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassId()
    {
        return $this->class_id;
    }

    /**
     * @param integer $price
     * @return CartItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param integer $quantity
     * @return CartItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return integer
     */
    public function getTotalPrice()
    {
        return $this->getPrice() * $this->getQuantity();
    }

    /**
     * @param object $object
     * @return CartItem
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }
}