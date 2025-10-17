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

use Eccube\Entity\Master\OrderItemType;

interface ItemInterface
{
    /**
     * 商品明細かどうか.
     *
     * @return bool 商品明細の場合 true
     */
    public function isProduct(): bool;

    /**
     * 送料明細かどうか.
     *
     * @return bool 送料明細の場合 true
     */
    public function isDeliveryFee(): bool;

    /**
     * 手数料明細かどうか.
     *
     * @return bool 手数料明細の場合 true
     */
    public function isCharge(): bool;

    /**
     * 値引き明細かどうか.
     *
     * @return bool 値引き明細の場合 true
     */
    public function isDiscount(): bool;

    /**
     * ポイント明細かどうか.
     *
     * @return bool ポイント明細の場合 true
     */
    public function isPoint(): bool;

    /**
     * 税額明細かどうか.
     *
     * @return bool 税額明細の場合 true
     */
    public function isTax(): bool;

    /**
     * @return OrderItemType|null
     */
    public function getOrderItemType(): ?OrderItemType;

    /**
     * @return ?ProductClass
     */
    public function getProductClass();

    /**
     * @return string|null
     */
    public function getPrice(): ?string;

    /**
     * @return string
     */
    public function getQuantity(): string;

    /**
     * @param string $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity): static;

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getPointRate(): ?string;

    /**
     * @param string|null $price
     *
     * @return $this
     */
    public function setPrice($price): static;

    /**
     * @return string
     */
    public function getPriceIncTax(): string;
}
