<?php

namespace Eccube\Entity;
interface ItemInterface
{
    /**
     * 商品明細かどうか.
     *
     * @return boolean 商品明細の場合 true
     */
    public function isProduct();

    /**
     * 送料明細かどうか.
     *
     * @return boolean 送料明細の場合 true
     */
    public function isDeliveryFee();

    /**
     * 手数料明細かどうか.
     *
     * @return boolean 手数料明細の場合 true
     */
    public function isCharge();

    /**
     * 値引き明細かどうか.
     *
     * @return boolean 値引き明細の場合 true
     */
    public function isDiscount();

    /**
     * 税額明細かどうか.
     *
     * @return boolean 税額明細の場合 true
     */
    public function isTax();

    public function getOrderItemType();

    /**
     * @return ProductClass
     */
    public function getProductClass();

    public function getPrice();

    public function getQuantity();

    public function setQuantity($quantity);
}
