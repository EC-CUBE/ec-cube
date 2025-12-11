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

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;

/**
 * @extends ArrayCollection<int, mixed>
 */
class OrderItemCollection extends ArrayCollection
{
    protected string $type;

    /**
     * @param array<int, OrderItem>|null $OrderItems
     */
    public function __construct(?array $OrderItems = null, ?string $type = null)
    {
        // $OrderItems が Collection だったら toArray(); する
        $this->type = is_null($type) ? Order::class : $type;
        parent::__construct($OrderItems);
    }

    /**
     * @param mixed|null $initial
     *
     * @return mixed|null
     */
    #[\Override]
    public function reduce(\Closure $func, mixed $initial = null): mixed
    {
        return array_reduce($this->toArray(), $func, $initial);
    }

    /**
     * 明細種別ごとに返すメソッド作る
     *
     * @return ArrayCollection<int, OrderItem>
     */
    public function getProductClasses(): ArrayCollection
    {
        return $this->filter(
            fn (ItemInterface $OrderItem) => $OrderItem->isProduct());
    }

    /**
     * @return ArrayCollection<int, OrderItem>
     */
    public function getDeliveryFees(): ArrayCollection
    {
        return $this->filter(
            fn (ItemInterface $OrderItem) => $OrderItem->isDeliveryFee());
    }

    /**
     * @return ArrayCollection<int, OrderItem>
     */
    public function getCharges(): ArrayCollection
    {
        return $this->filter(
            fn (ItemInterface $OrderItem) => $OrderItem->isCharge());
    }

    /**
     * @return ArrayCollection<int, OrderItem>
     */
    public function getDiscounts(): ArrayCollection
    {
        return $this->filter(
            fn (ItemInterface $OrderItem) => $OrderItem->isDiscount() || $OrderItem->isPoint());
    }

    /**
     * 同名の明細が存在するかどうか.
     *
     * TODO 暫定対応. 本来は明細種別でチェックする.
     */
    public function hasProductByName(string $productName): bool
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
     */
    public function hasItemByOrderItemType(OrderItemType $OrderItemType): bool
    {
        $filteredItems = $this->filter(fn (ItemInterface $OrderItem) =>
            /* @var OrderItem $OrderItem */
            $OrderItem->getOrderItemType() && $OrderItem->getOrderItemType()->getId() == $OrderItemType->getId());

        return !$filteredItems->isEmpty();
    }

    public function getType(): string
    {
        return $this->type;
    }
}
