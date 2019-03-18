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

if (!class_exists('\Eccube\Entity\CartItem')) {
    /**
     * CartItem
     *
     * @ORM\Table(name="dtb_cart_item")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\CartItemRepository")
     */
    class CartItem extends \Eccube\Entity\AbstractEntity implements ItemInterface
    {
        use PointRateTrait;

        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(name="price", type="decimal", precision=12, scale=2, options={"default":0})
         */
        private $price = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="quantity", type="decimal", precision=10, scale=0, options={"default":0})
         */
        private $quantity = 0;

        /**
         * @var \Eccube\Entity\ProductClass
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\ProductClass")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="product_class_id", referencedColumnName="id")
         * })
         */
        private $ProductClass;

        /**
         * @var \Eccube\Entity\Cart
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Cart", inversedBy="CartItems")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="cart_id", referencedColumnName="id", onDelete="CASCADE")
         * })
         */
        private $Cart;

        /**
         * sessionのシリアライズのために使われる
         *
         * @var int
         */
        private $product_class_id;

        public function __sleep()
        {
            return ['product_class_id', 'price', 'quantity'];
        }

        /**
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }
        
        /**
         * @param  integer  $price
         *
         * @return CartItem
         */
        public function setPrice($price)
        {
            $this->price = $price;

            return $this;
        }

        /**
         * @return string
         */
        public function getPrice()
        {
            return $this->price;
        }

        /**
         * @param  integer  $quantity
         *
         * @return CartItem
         */
        public function setQuantity($quantity)
        {
            $this->quantity = $quantity;

            return $this;
        }

        /**
         * @return string
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
         * 商品明細かどうか.
         *
         * @return boolean 商品明細の場合 true
         */
        public function isProduct()
        {
            return true;
        }

        /**
         * 送料明細かどうか.
         *
         * @return boolean 送料明細の場合 true
         */
        public function isDeliveryFee()
        {
            return false;
        }

        /**
         * 手数料明細かどうか.
         *
         * @return boolean 手数料明細の場合 true
         */
        public function isCharge()
        {
            return false;
        }

        /**
         * 値引き明細かどうか.
         *
         * @return boolean 値引き明細の場合 true
         */
        public function isDiscount()
        {
            return false;
        }

        /**
         * 税額明細かどうか.
         *
         * @return boolean 税額明細の場合 true
         */
        public function isTax()
        {
            return false;
        }

        /**
         * ポイント明細かどうか.
         *
         * @return boolean ポイント明細の場合 true
         */
        public function isPoint()
        {
            return false;
        }

        public function getOrderItemType()
        {
            // TODO OrderItemType::PRODUCT
            $ItemType = new \Eccube\Entity\Master\OrderItemType();

            return $ItemType;
        }

        /**
         * @param ProductClass $ProductClass
         *
         * @return $this
         */
        public function setProductClass(ProductClass $ProductClass)
        {
            $this->ProductClass = $ProductClass;

            $this->product_class_id = is_object($ProductClass) ?
            $ProductClass->getId() : null;

            return $this;
        }

        /**
         * @return ProductClass
         */
        public function getProductClass()
        {
            return $this->ProductClass;
        }

        /**
         * @return int|null
         */
        public function getProductClassId()
        {
            return $this->product_class_id;
        }

        public function getPriceIncTax()
        {
            // TODO ItemInterfaceに追加, Cart::priceは税込み金額が入っているので,フィールドを分ける必要がある
            return $this->price;
        }

        /**
         * @return Cart
         */
        public function getCart()
        {
            return $this->Cart;
        }

        /**
         * @param Cart $Cart
         */
        public function setCart(Cart $Cart)
        {
            $this->Cart = $Cart;
        }
    }
}
