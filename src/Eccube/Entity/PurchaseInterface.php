<?php

namespace Eccube\Entity;

interface PurchaseInterface
{
    /**
     * 合計金額を設定します。
     * @param $total|int
     */
    public function setTotal($total);

    /**
     * 合計金額を返す。
     * @return int
     */
    public function getTotal();

    public function getItems();
}
