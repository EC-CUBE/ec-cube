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
     * 設定情報IDに基づいて税金付与した金額を返す
     * (受注データのようにルールが決まっている場合用)
     *
     * @param integer $price 計算対象の金額
     * @return integer 税金付与した金額
     */
    function calcIncTaxFromRuleId($price, $tax_rule_id = 0)
    {
        return $price + SC_Helper_TaxRule_Ex::calcTaxFromRuleId($price, $tax_rule_id);
    }

    /**
     * 設定情報IDに基づいて税金の金額を返す
     * (受注データのようにルールが決まっている場合用)
     *
     * @param integer $price 計算対象の金額
     * @return integer 税金した金額
     */
    function calcTaxFromRuleId($price, $tax_rule_id = 0)
    {
        $arrTaxRule = SC_Helper_TaxRule_Ex::getTaxRuleData($tax_rule_id);
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
        print_r($arrData);
        // 日付や条件でこねて選択は、作り中。取りあえずスタブ的にデフォルトを返却
        return $arrData[0];
    }

    /**
     * 税金設定情報を登録する（商品管理用）
     *
     * @param
     * @return
     */
    function setTaxRuleForProduct($tax_rate, $tax_adjust=0, $product_id = 0, $product_class_id = 0, $pref_id = 0, $country_id = 0)
    {
        // デフォルトの設定取得
        $arrRet = SC_Helper_TaxRule_Ex::getTaxRule();
        // 税情報を設定
        SC_Helper_TaxRule_Ex::setTaxRule($arrRet['calc_rule'],
                                         $tax_rate,
                                         $arrRet['apply_date'],
                                         $tax_adjust,
                                         $product_id,
                                         $product_class_id,
                                         $pref_id,
                                         $country_id);
    }

    /**
     * 税金設定情報を登録する（仮）
     *
     * @param
     * @return
     */
    function setTaxRule($calc_rule, $tax_rate, $apply_date, $tax_adjust=0, $product_id = 0, $product_class_id = 0, $pref_id = 0, $country_id = 0)
    {
        // デフォルトの設定とtax_rateの値が同じ場合は登録しない
        $arrRet = SC_Helper_TaxRule_Ex::getTaxRule();
        if( $arrRet['tax_rate'] == $tax_rate ) {
            return;
        }
        // 税情報を設定
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_tax_rule';
        $arrValues = array();
        // todo idを計算して設定する必要あり
        $arrValues['tax_rule_id'] = 1;
        $arrValues['country_id'] = $country_id;
        $arrValues['pref_id'] = $pref_id;
        $arrValues['product_id'] = $product_id;
        $arrValues['product_class_id'] = $product_class_id;
        $arrValues['calc_rule'] = $calc_rule;
        $arrValues['tax_rate'] = $tax_rate;
        $arrValues['tax_adjust'] = $tax_adjust;
        $arrValues['apply_date'] = $apply_date;
        $arrValues['create_date'] = 'CURRENT_TIMESTAMP';
        $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
        
        $objQuery->insert($table, $arrValues);
    }
    
    
    function getTaxRuleList($has_disable = false)
    {

    }

    function getTaxRuleData($tax_rule_id)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->getRow('*', 'dtb_tax_rule', 'tax_rule_id = ?', array($tax_rule_id));
    }


    function registerTaxRuleData() {
    }


}
