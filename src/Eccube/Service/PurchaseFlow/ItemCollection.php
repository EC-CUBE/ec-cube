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

namespace Eccube\Service\PurchaseFlow;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;

/**
 * @extends ArrayCollection<int, ItemInterface>
 */
class ItemCollection extends ArrayCollection
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @param array<int, ItemInterface>|array<int, OrderItem>|Collection<int, ItemInterface>|Collection<int, OrderItem>|array<int, CartItem>|Collection<int, CartItem>|null $Items
     * @param string|null $type
     */
    public function __construct($Items = null, $type = null)
    {
        $this->type = is_null($type) ? Order::class : $type;

        if ($Items instanceof Collection) {
            $Items = $Items->toArray();
        }
        parent::__construct($Items);
    }

    /**
     * @param \Closure $func
     * @param mixed|null $initial
     *
     * @return mixed|null
     */
    public function reduce(\Closure $func, $initial = null): mixed
    {
        return array_reduce($this->toArray(), $func, $initial);
    }

    /**
     * @return ItemCollection<int, ItemInterface>
     */
    // 明細種別ごとに返すメソッド作る
    public function getProductClasses(): ItemCollection
    {
        return $this->filter(
            function (ItemInterface $OrderItem) {
                return $OrderItem->isProduct();
            });
    }

    /**
     * @return ItemCollection<int, ItemInterface>
     */
    public function getDeliveryFees(): ItemCollection
    {
        return $this->filter(
            function (ItemInterface $OrderItem) {
                return $OrderItem->isDeliveryFee();
            });
    }

    /**
     * @return ItemCollection<int, ItemInterface>
     */
    public function getCharges(): ItemCollection
    {
        return $this->filter(
            function (ItemInterface $OrderItem) {
                return $OrderItem->isCharge();
            });
    }

    /**
     * @return ItemCollection<int, ItemInterface>
     */
    public function getDiscounts(): ItemCollection
    {
        return $this->filter(
            function (ItemInterface $OrderItem) {
                return $OrderItem->isDiscount() || $OrderItem->isPoint();
            });
    }

    /**
     * 同名の明細が存在するかどうか.
     *
     * TODO 暫定対応. 本来は明細種別でチェックする.
     *
     * @param string $productName
     *
     * @return bool
     */
    public function hasProductByName($productName): bool
    {
        $OrderItems = $this->filter(
            function (ItemInterface $OrderItem) use ($productName) {
                /** @var OrderItem $OrderItem */
                return $OrderItem->getProductName() == $productName;
            });

        return !$OrderItems->isEmpty();
    }

    /**
     * 指定した受注明細区分の明細が存在するかどうか.
     *
     * @param OrderItemType $OrderItemType 受注区分
     *
     * @return bool
     */
    public function hasItemByOrderItemType($OrderItemType): bool
    {
        $filteredItems = $this->filter(function (ItemInterface $OrderItem) use ($OrderItemType) {
            /* @var OrderItem $OrderItem */
            return $OrderItem->getOrderItemType() && $OrderItem->getOrderItemType()->getId() == $OrderItemType->getId();
        });

        return !$filteredItems->isEmpty();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return self
     */
    public function sort(): ItemCollection
    {
        $Items = $this->toArray();
        usort($Items, function (ItemInterface $a, ItemInterface $b) {
            if ($a->getOrderItemType() === $b->getOrderItemType()) {
                return $a->getId() <=> $b->getId();
            } elseif ($a->isProduct()) {
                return -1;
            } elseif ($a->isDeliveryFee()) {
                if ($b->isProduct()) {
                    return 1;
                }

                return -1;
            } elseif ($a->isCharge()) {
                if ($b->isDeliveryFee() || $b->isProduct()) {
                    return 1;
                }

                return -1;
            } elseif ($a->isDiscount() || $a->isPoint()) {
                if ($b->isDiscount()) {
                    return -1;
                }

                if ($b->isPoint()) {
                    return 1;
                }

                if (!$b->isTax()) {
                    return 1;
                }

                return -1;
            } elseif ($a->isTax()) {
                return 1;
            }

            return 0;
        });

        return new self($Items);
    }
}
