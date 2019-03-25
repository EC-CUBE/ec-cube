<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

interface ItemHolderInterface
{
    /**
     * @return ItemInterface[]
     */
    public function getItems();

    /**
     * 合計金額を返します。
     *
     * @return int
     */
    public function getTotal();

    /**
     * 合計金額を設定します。
     *
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
     *
     * @param $total|int
     */
    public function setDeliveryFeeTotal($total);

    /**
     * 送料合計を返します。
     *
     * @return int
     */
    public function getDeliveryFeeTotal();

    /**
     * 値引き合計を設定します。
     *
     * @param $total|int
     */
    public function setDiscount($total);

    /**
     * 手数料合計を設定します。
     *
     * @param $total|int
     */
    public function setCharge($total);

    /**
     * 税額合計を設定します。
     *
     * @param $total|int
     *
     * @deprecated 明細ごとに集計した税額と差異が発生する場合があるため非推奨
     */
    public function setTax($total);

    /**
     * 加算ポイントを設定します。
     *
     * @param $addPoint|int
     */
    public function setAddPoint($addPoint);

    /**
     * 加算ポイントを返します.
     *
     * @return int
     */
    public function getAddPoint();

    /**
     * 利用ポイントを設定します。
     *
     * @param $usePoint|int
     */
    public function setUsePoint($usePoint);

    /**
     * 利用ポイントを返します.
     *
     * @return int
     */
    public function getUsePoint();

    /**
     * @param ItemInterface $item
     */
    public function addItem(ItemInterface $item);
}
