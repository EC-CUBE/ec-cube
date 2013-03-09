<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * 税規約を管理するヘルパークラス.
 *
 * @package Helper
 * @author AMUAMU
 * @version $Id:$
 */
class SC_Helper_TaxRule
{
    /**
     * 設定情報に基づいて税金付与した金額を返す
     *
     * @param integer $price 計算対象の金額
     * @return integer 税金付与した金額
     */
    function sfCalcIncTax($price, $product_id = 0, $product_class_id = 0, $pref_id =0, $country_id = 0)
    {
        return $price + SC_Helper_TaxRule_Ex::sfTax($price, $product_id, $product_class_id, $pref_id, $country_id);
    }

    /**
     * 設定情報に基づいて税金の金額を返す
     *
     * @param integer $price 計算対象の金額
     * @return integer 税金した金額
     */
    function sfTax($price, $product_id = 0, $product_class_id = 0, $pref_id =0, $country_id = 0)
    {
        $arrTaxRule = SC_Helper_TaxRule_Ex::getTaxRule($product_id, $product_class_id, $pref_id, $country_id);
        return SC_Helper_TaxRule_Ex::calcTax($price, $arrTaxRule['tax_rate'], $arrTaxRule['tax_rule'], $arrTaxRule['tax_adjust']);
    }

    /**
     * 税金額を計算する
     *
     * @param integer $price 計算対象の金額
     * @param integer $tax 税率(%単位)
     *     XXX integer のみか不明
     * @param integer $tax_rule 端数処理
     * @return integer 税金額
     */
    function calcTax ($price, $tax, $calc_rule, $tax_adjust = 0)
    {
        $real_tax = $tax / 100;
        $ret = $price * $real_tax;
        switch ($calc_rule) {
            // 四捨五入
            case 1:
                $ret = round($ret);
                break;
            // 切り捨て
            case 2:
                $ret = floor($ret);
                break;
            // 切り上げ
            case 3:
                $ret = ceil($ret);
                break;
            // デフォルト:切り上げ
            default:
                $ret = ceil($ret);
                break;
        }
        return $ret + $tax_adjust;
    }

    /**
     * 現在有効な税金設定情報を返す
     *
     * @param integer $price 計算対象の金額
     * @return array 税設定情報
     */
    function getTaxRule($product_id = 0, $product_class_id = 0, $pref_id = 0, $country_id = 0)
    {
        // 条件に基づいて税情報を取得
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_tax_rule';
        $where = '(product_id = 0 OR product_id = ?)'
                    . ' AND (product_class_id = 0 OR product_class_id = ?)'
                    . ' AND (pref_id = 0 OR pref_id = ?)'
                    . ' AND (country_id = 0 OR country_id = ?)';

        $arrVal = array($product_id, $product_class_id, $pref_id, $country_id);
        $order = 'apply_date DESC';
        $objQuery->setOrder($order);
        $arrData = $objQuery->select('*', $table, $where, $arrVal);
        // 日付や条件でこねて選択は、作り中。取りあえずスタブ的にデフォルトを返却
        return $arrData[0];
    }


    function getTaxRuleList($has_disable = false)
    {

    }

    function getTaxRuleData($tax_rule_id)
    {

    }


    function registerTaxRuleData() {
    }
}
