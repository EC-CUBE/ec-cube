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

        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /** @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'price', type: 'decimal', precision: 12, scale: 2, options: ['default' => 0])]
        private $price = '0';

        /**
         * @var string
         */
        #[ORM\Column(name: 'quantity', type: 'decimal', precision: 10, scale: 0, options: ['default' => 0])]
        private $quantity = '0';

        /**
         * @var ProductClass|null
         */
        #[ORM\ManyToOne(targetEntity: ProductClass::class)]
        #[ORM\JoinColumn(name: 'product_class_id', referencedColumnName: 'id')]
        private $ProductClass;

        /**
         * @var Cart|null
         */
        #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'CartItems', cascade: ['persist'])]
        #[ORM\JoinColumn(name: 'cart_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
        private $Cart;

        /**
         * sessionのシリアライズのために使われる
         *
         * @var int
         */
        private $product_class_id;

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

        /**
         * @param string $price
         *
         * @return static
         */
        public function setPrice($price): static
        {
            $this->price = $price;

            return $this;
        }

        /**
         * @return string|null
         */
        #[\Override]
        public function getPrice(): ?string
        {
            return $this->price;
        }

        /**
         * @param  string  $quantity
         *
         * @return static
         */
        #[\Override]
        public function setQuantity($quantity): static
        {
            $this->quantity = $quantity;

            return $this;
        }

        /**
         * @return string
         */
        #[\Override]
        public function getQuantity(): string
        {
            return $this->quantity;
        }

        /**
         * @return string
         */
        public function getTotalPrice(): string
        {
            return bcmul($this->getPrice(), $this->getQuantity(), 2);
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

        /**
         * @return OrderItemType
         */
        #[\Override]
        public function getOrderItemType(): OrderItemType
        {
            // TODO OrderItemType::PRODUCT
            $ItemType = new OrderItemType();

            return $ItemType;
        }

        /**
         * @param ProductClass $ProductClass
         *
         * @return $this
         */
        public function setProductClass(ProductClass $ProductClass): static
        {
            $this->ProductClass = $ProductClass;

            $this->product_class_id = $ProductClass->getId();

            return $this;
        }

        /**
         * @return ProductClass|null
         */
        #[\Override]
        public function getProductClass(): ?ProductClass
        {
            return $this->ProductClass;
        }

        /**
         * @return int|null
         */
        public function getProductClassId(): ?int
        {
            return $this->product_class_id;
        }

        /**
         * @return string
         */
        public function getPriceIncTax(): string
        {
            // TODO ItemInterfaceに追加, Cart::priceは税込み金額が入っているので,フィールドを分ける必要がある
            return $this->price;
        }

        /**
         * @return Cart|null
         */
        public function getCart(): ?Cart
        {
            return $this->Cart;
        }

        /**
         * @param Cart $Cart
         *
         * @return $this
         */
        public function setCart(Cart $Cart): static
        {
            $this->Cart = $Cart;

            return $this;
        }
    }
}
