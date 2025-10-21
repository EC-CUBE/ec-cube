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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\CartRepository;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemCollection;

if (!class_exists(Cart::class)) {
    /**
     * Cart
     */
    #[ORM\Table(name: 'dtb_cart')]
    #[ORM\Index(columns: ['update_date'], name: 'dtb_cart_update_date_idx')]
    #[ORM\UniqueConstraint(name: 'dtb_cart_pre_order_id_idx', columns: ['pre_order_id'])]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: CartRepository::class)]
    class Cart extends AbstractEntity implements PurchaseInterface, ItemHolderInterface
    {
        use PointTrait;

        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /** @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'cart_key', type: 'string', nullable: true)]
        private $cart_key;

        /**
         * @var Customer|null
         */
        #[ORM\ManyToOne(targetEntity: Customer::class)]
        #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
        private $Customer;

        /**
         * @var bool
         */
        private $lock = false;

        /**
         * @var Collection<int, CartItem>
         */
        #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'Cart', cascade: ['persist'])]
        #[ORM\OrderBy(['id' => 'ASC'])]
        private $CartItems;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'pre_order_id', type: 'string', length: 255, nullable: true)]
        private $pre_order_id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'total_price', type: 'decimal', precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
        private $total_price;

        /**
         * @var string
         */
        #[ORM\Column(name: 'delivery_fee_total', type: 'decimal', precision: 12, scale: 2, options: ['unsigned' => true, 'default' => 0])]
        private $delivery_fee_total;

        /**
         * @var int|null
         */
        #[ORM\Column(name: 'sort_no', type: 'smallint', nullable: true, options: ['unsigned' => true])]
        private $sort_no;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: 'datetimetz')]
        private $create_date;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'update_date', type: 'datetimetz')]
        private $update_date;

        /**
         * @var InvalidItemException[]
         */
        /** @phpstan-ignore-next-line */
        private $errors = [];

        public function __wakeup()
        {
            $this->errors = [];
        }

        public function __construct()
        {
            $this->CartItems = new ArrayCollection();
        }

        /**
         * @return int
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * @return string
         */
        public function getCartKey(): string
        {
            return $this->cart_key;
        }

        /**
         * @param string $cartKey
         *
         * @return Cart
         */
        public function setCartKey(string $cartKey): Cart
        {
            $this->cart_key = $cartKey;

            return $this;
        }

        /**
         * @return bool
         *
         * @deprecated 使用しないので削除予定
         */
        public function getLock(): bool
        {
            return $this->lock;
        }

        /**
         * @param  bool                $lock
         *
         * @return Cart
         *
         * @deprecated 使用しないので削除予定
         */
        public function setLock($lock): Cart
        {
            $this->lock = $lock;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getPreOrderId(): ?string
        {
            return $this->pre_order_id;
        }

        /**
         * @param string|null $pre_order_id
         *
         * @return Cart
         */
        public function setPreOrderId($pre_order_id): Cart
        {
            $this->pre_order_id = $pre_order_id;

            return $this;
        }

        /**
         * @param  CartItem            $CartItem
         *
         * @return Cart
         */
        public function addCartItem(CartItem $CartItem): Cart
        {
            $this->CartItems[] = $CartItem;

            return $this;
        }

        /**
         * カートの中に出荷データがないので、空のコレクションを返します。
         *
         * @return ArrayCollection<int, Shipping>
         */
        public function getShippings(): ArrayCollection
        {
            return new ArrayCollection();
        }

        /**
         * @return Cart
         */
        public function clearCartItems(): Cart
        {
            $this->CartItems->clear();

            return $this;
        }

        /**
         * @return Collection<int, CartItem>
         */
        public function getCartItems(): Collection
        {
            return $this->CartItems;
        }

        /**
         * Alias of getCartItems()
         *
         * @return ItemCollection<int, ItemInterface>
         */
        #[\Override]
        public function getItems(): ItemCollection
        {
            return (new ItemCollection($this->getCartItems()))->sort();
        }

        /**
         * @param  Collection<int, CartItem> $CartItems
         *
         * @return Cart
         */
        public function setCartItems($CartItems): Cart
        {
            $this->CartItems = $CartItems;

            return $this;
        }

        /**
         * Set total.
         *
         * @param string $total_price
         *
         * @return $this
         */
        public function setTotalPrice($total_price): static
        {
            $this->total_price = $total_price;

            return $this;
        }

        /**
         * @return string
         */
        public function getTotalPrice(): string
        {
            return $this->total_price;
        }

        /**
         * Alias of setTotalPrice.
         *
         * @param string $total
         *
         * @return $this
         */
        #[\Override]
        public function setTotal($total): static
        {
            return $this->setTotalPrice($total);
        }

        /**
         * Alias of getTotalPrice
         *
         * @return string
         */
        #[\Override]
        public function getTotal(): string
        {
            return $this->getTotalPrice();
        }

        /**
         * @return string
         */
        public function getTotalQuantity(): string
        {
            $totalQuantity = '0';
            foreach ($this->CartItems as $CartItem) {
                $totalQuantity = bcadd($totalQuantity, $CartItem->getQuantity());
            }

            return $totalQuantity;
        }

        /**
         * @param ItemInterface $item
         *
         * @return void
         */
        #[\Override]
        public function addItem(ItemInterface $item): void
        {
            if ($item instanceof CartItem) {
                $this->CartItems->add($item);
            }
        }

        /**
         * @param ItemInterface $item
         *
         * @return void
         */
        public function removeItem(ItemInterface $item): void
        {
            if ($item instanceof CartItem) {
                $this->CartItems->removeElement($item);
            }
        }

        /**
         * 個数の合計を返します。
         *
         * @return string
         */
        #[\Override]
        public function getQuantity(): string
        {
            return $this->getTotalQuantity();
        }

        /**
         * {@inheritdoc}
         *
         * @param string $total
         *
         * @return $this
         */
        #[\Override]
        public function setDeliveryFeeTotal($total): static
        {
            $this->delivery_fee_total = $total;

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        #[\Override]
        public function getDeliveryFeeTotal(): string
        {
            return $this->delivery_fee_total;
        }

        /**
         * @return Customer|null
         */
        public function getCustomer(): ?Customer
        {
            return $this->Customer;
        }

        /**
         * @param Customer|null $Customer
         *
         * @return Cart
         */
        public function setCustomer(?Customer $Customer = null): Cart
        {
            $this->Customer = $Customer;

            return $this;
        }

        /**
         * Set sortNo.
         *
         * @param int|null $sortNo
         *
         * @return Cart
         */
        public function setSortNo($sortNo = null): Cart
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         *
         * @return int|null
         */
        public function getSortNo(): ?int
        {
            return $this->sort_no;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Cart
         */
        public function setCreateDate($createDate): Cart
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime|null
         */
        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return Cart
         */
        public function setUpdateDate($updateDate): Cart
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime|null
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }

        /**
         * {@inheritdoc}
         *
         * @param string $total
         *
         * @return $this
         */
        #[\Override]
        public function setDiscount($total): static
        {
            // quiet
            return $this;
        }

        /**
         * {@inheritdoc}
         *
         * @param string $total
         *
         * @return $this
         */
        #[\Override]
        public function setCharge($total): static
        {
            // quiet
            return $this;
        }

        /**
         * {@inheritdoc}
         *
         * @param string $total
         *
         * @return $this
         *
         * @deprecated
         */
        #[\Override]
        public function setTax($total): static
        {
            // quiet
            return $this;
        }

        /**
         * 注文ではないので、nullを返します。
         *
         * @return null
         */
        public function getOrderStatus(): null
        {
            return null;
        }

        /**
         * {@inheritdoc}
         *
         * @return OrderItem[]
         */
        public function getProductOrderItems(): array
        {
            return [];
        }
    }
}
