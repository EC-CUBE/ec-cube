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

namespace Eccube\Service\Calculator;

use Eccube\Entity\ItemInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<int, mixed>
 */
class OrderItemCollection extends \Doctrine\Common\Collections\ArrayCollection
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @param array<int, OrderItem>|null $OrderItems
     * @param string|null $type
     */
    public function __construct($OrderItems = null, $type = null)
    {
        // $OrderItems が Collection だったら toArray(); する
        $this->type = is_null($type) ? Order::class : $type;
        parent::__construct($OrderItems);
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
     * 明細種別ごとに返すメソッド作る
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<int, OrderItem>
     */
    public function getProductClasses(): \Doctrine\Common\Collections\ArrayCollection
    {
        return $this->filter(
            function (ItemInterface $OrderItem) {
                return $OrderItem->isProduct();
            });
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<int, OrderItem>
     */
    public function getDeliveryFees(): \Doctrine\Common\Collections\ArrayCollection
    {
        return $this->filter(
            function (ItemInterface $OrderItem) {
                return $OrderItem->isDeliveryFee();
            });
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<int, OrderItem>
     */
    public function getCharges(): \Doctrine\Common\Collections\ArrayCollection
    {
        return $this->filter(
            function (ItemInterface $OrderItem) {
                return $OrderItem->isCharge();
            });
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<int, OrderItem>
     */
    public function getDiscounts(): \Doctrine\Common\Collections\ArrayCollection
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
     * 指定した受注明細区分の明細が存在するかどうか
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
}
