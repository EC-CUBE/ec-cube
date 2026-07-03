<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Repository\CartItemRepository;

if (!class_exists(CartItem::class)) {
    /**
     * CartItem
     */
    #[ORM\Table(name: 'dtb_cart_item')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: CartItemRepository::class)]
    class CartItem extends AbstractEntity implements ItemInterface
    {
        use PointRateTrait;

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        #[ORM\Column(name: 'price', type: Types::DECIMAL, precision: 12, scale: 2, options: ['default' => 0])]
        private ?string $price = '0';

        #[ORM\Column(name: 'quantity', type: Types::DECIMAL, precision: 10, scale: 0, options: ['default' => 0])]
        private string $quantity = '0';

        #[ORM\ManyToOne(targetEntity: ProductClass::class)]
        #[ORM\JoinColumn(name: 'product_class_id', referencedColumnName: 'id')]
        private ?ProductClass $ProductClass = null;

        #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'CartItems', cascade: ['persist'])]
        #[ORM\JoinColumn(name: 'cart_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
        private ?Cart $Cart = null;

        /**
         * sessionのシリアライズのために使われる
         */
        private int $product_class_id;

        public function __sleep(): array
        {
            return ['product_class_id', 'price', 'quantity'];
        }

        /**
         * @return int
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        public function setPrice(?string $price): static
        {
            $this->price = $price;

            return $this;
        }

        #[\Override]
        public function getPrice(): ?string
        {
            return $this->price;
        }

        /**
         * @param  string  $quantity
         */
        #[\Override]
        public function setQuantity($quantity): static
        {
            $this->quantity = $quantity;

            return $this;
        }

        #[\Override]
        public function getQuantity(): string
        {
            return $this->quantity;
        }

        public function getTotalPrice(): string
        {
            return bcmul((string) $this->getPrice(), $this->getQuantity(), 2);
        }

        /**
         * 商品明細かどうか.
         *
         * @return bool 商品明細の場合 true
         */
        #[\Override]
        public function isProduct(): bool
        {
            return true;
        }

        /**
         * 送料明細かどうか.
         *
         * @return bool 送料明細の場合 true
         */
        #[\Override]
        public function isDeliveryFee(): bool
        {
            return false;
        }

        /**
         * 手数料明細かどうか.
         *
         * @return bool 手数料明細の場合 true
         */
        #[\Override]
        public function isCharge(): bool
        {
            return false;
        }

        /**
         * 値引き明細かどうか.
         *
         * @return bool 値引き明細の場合 true
         */
        #[\Override]
        public function isDiscount(): bool
        {
            return false;
        }

        /**
         * 税額明細かどうか.
         *
         * @return bool 税額明細の場合 true
         */
        #[\Override]
        public function isTax(): bool
        {
            return false;
        }

        /**
         * ポイント明細かどうか.
         *
         * @return bool ポイント明細の場合 true
         */
        #[\Override]
        public function isPoint(): bool
        {
            return false;
        }

        #[\Override]
        public function getOrderItemType(): OrderItemType
        {
            // TODO OrderItemType::PRODUCT
            $ItemType = new OrderItemType();

            return $ItemType;
        }

        /**
         * @return $this
         */
        public function setProductClass(ProductClass $ProductClass): static
        {
            $this->ProductClass = $ProductClass;

            $this->product_class_id = $ProductClass->getId();

            return $this;
        }

        #[\Override]
        public function getProductClass(): ?ProductClass
        {
            return $this->ProductClass;
        }

        public function getProductClassId(): ?int
        {
            return $this->product_class_id;
        }

        public function getPriceIncTax(): string
        {
            // TODO ItemInterfaceに追加, Cart::priceは税込み金額が入っているので,フィールドを分ける必要がある
            return $this->price;
        }

        public function getCart(): ?Cart
        {
            return $this->Cart;
        }

        /**
         * @return $this
         */
        public function setCart(Cart $Cart): static
        {
            $this->Cart = $Cart;

            return $this;
        }
    }
}
