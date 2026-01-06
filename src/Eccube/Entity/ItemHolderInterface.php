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

use Eccube\Service\PurchaseFlow\ItemCollection;

interface ItemHolderInterface
{
    /**
     * @return ItemCollection
     */
    public function getItems();

    /**
     * 合計金額を返します。
     *
     * @return string
     */
    public function getTotal();

    /**
     * 合計金額を設定します。
     *
     * @param string $total
     */
    public function setTotal($total);

    /**
     * 個数の合計を返します。
     *
     * @return string
     */
    public function getQuantity();

    /**
     * 送料合計を設定します。
     *
     * @param string $total
     */
    public function setDeliveryFeeTotal($total);

    /**
     * 送料合計を返します。
     *
     * @return string
     */
    public function getDeliveryFeeTotal();

    /**
     * 値引き合計を設定します。
     *
     * @param string $total
     */
    public function setDiscount($total);

    /**
     * 手数料合計を設定します。
     *
     * @param string $total
     */
    public function setCharge($total);

    /**
     * 税額合計を設定します。
     *
     * @param string $total
     *
     * @deprecated 明細ごとに集計した税額と差異が発生する場合があるため非推奨
     */
    public function setTax($total);

    /**
     * 加算ポイントを設定します。
     *
     * @param string $addPoint
     */
    public function setAddPoint($addPoint);

    /**
     * 加算ポイントを返します.
     *
     * @return string
     */
    public function getAddPoint();

    /**
     * 利用ポイントを設定します。
     *
     * @param string $usePoint
     */
    public function setUsePoint($usePoint);

    /**
     * 利用ポイントを返します.
     *
     * @return string
     */
    public function getUsePoint();

    /**
     * @param ItemInterface $item
     */
    public function addItem(ItemInterface $item);
}
