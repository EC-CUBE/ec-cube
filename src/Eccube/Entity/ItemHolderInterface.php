<?php

namespace Eccube\Entity;


use Eccube\Service\ItemValidateException;

interface ItemHolderInterface
{
    /**
     * @param string $error
     * @return void
     */
    public function addError($error);

    /**
     * @return ItemInterface[]
     */
    public function getItems();

    /**
     * @return ItemValidateException[]
     */
    public function getErrors();

    /**
     * 合計金額を返します。
     * @return int
     */
    public function getTotal();

    /**
     * 合計金額を設定します。
     * @param $total|int
     */
    public function setTotal($total);

    /**
     * 個数の合計を返します。
     *
     * @return mixed
     */
    public function getQuantity();

    /**
     * 送料合計を設定します。
     * @param $total|int
     */
    public function setDeliveryFeeTotal($total);

    /**
     * 送料合計を返します。
     * @return int
     */
    public function getDeliveryFeeTotal();

    /**
     * 値引き合計を設定します。
     * @param $total|int
     */
    public function setDiscount($total);

    /**
     * 手数料合計を設定します。
     * @param $total|int
     */
    public function setCharge($total);

    /**
     * 税額合計を設定します。
     * @param $total|int
     */
    public function setTax($total);

    /**
     * @param ItemInterface $item
     */
    public function addItem(ItemInterface $item);
}
