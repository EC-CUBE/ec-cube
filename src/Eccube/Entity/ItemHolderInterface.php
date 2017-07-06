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
     * @param ItemInterface $item
     */
    public function addItem(ItemInterface $item);
}