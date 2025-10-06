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

use Doctrine\Common\Collections\Collection;
use Eccube\Service\PurchaseFlow\ItemCollection;

interface ItemHolderInterface
{
    /**
     * @return ItemCollection<int, CartItem>|ItemCollection<int, OrderItem>
     */
    public function getItems(): ItemCollection;

    /**
     * 合計金額を返します。
     *
     * @return string
     */
    public function getTotal(): string;

    /**
     * 合計金額を設定します。
     *
     * @param string $total
     *
     * @return ItemHolderInterface
     */
    public function setTotal($total): ItemHolderInterface;

    /**
     * 個数の合計を返します。
     *
     * @return string
     */
    public function getQuantity(): string;

    /**
     * 送料合計を設定します。
     *
     * @param string $total
     *
     * @return ItemHolderInterface
     */
    public function setDeliveryFeeTotal($total): ItemHolderInterface;

    /**
     * 送料合計を返します。
     *
     * @return string
     */
    public function getDeliveryFeeTotal(): string;

    /**
     * 値引き合計を設定します。
     *
     * @param string $total
     *
     * @return $this
     */
    public function setDiscount($total): self;

    /**
     * 手数料合計を設定します。
     *
     * @param string $total
     *
     * @return $this
     */
    public function setCharge($total): self;

    /**
     * 税額合計を設定します。
     *
     * @param string $total
     *
     * @return $this
     *
     * @deprecated 明細ごとに集計した税額と差異が発生する場合があるため非推奨
     */
    public function setTax($total): self;

    /**
     * 加算ポイントを設定します。
     *
     * @param string $addPoint
     *
     * @return ItemHolderInterface
     */
    public function setAddPoint($addPoint): ItemHolderInterface;

    /**
     * 加算ポイントを返します.
     *
     * @return string
     */
    public function getAddPoint(): string;

    /**
     * 利用ポイントを設定します。
     *
     * @param string $usePoint
     *
     * @return ItemHolderInterface
     */
    public function setUsePoint($usePoint): ItemHolderInterface;

    /**
     * 利用ポイントを返します.
     *
     * @return string
     */
    public function getUsePoint(): string;

    /**
     * @param ItemInterface $item
     *
     * @return void
     */
    public function addItem(ItemInterface $item): void;

    /**
     * Get customer.
     *
     * @return Customer|null
     */
    public function getCustomer(): ?Customer;

    /**
     * 出荷情報を追加します - 注文のみ
     *
     * @return Collection<int, Shipping>
     */
    public function getShippings(): Collection;

    /**
     * 注文ステータスを返す - 注文のみ
     *
     * @return mixed
     */
    public function getOrderStatus(): mixed;

    /**
     * 商品の受注明細を取得 - 注文のみ
     *
     * @return OrderItem[]
     */
    public function getProductOrderItems(): array;
}
