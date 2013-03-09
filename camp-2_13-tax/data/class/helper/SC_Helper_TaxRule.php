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
        // 日付や条件でこねて選択は、作り中。取りあえずスタブ的にデフォルトを返却
        // 一旦配列の最後の項目を返すように変更
        // return $arrData[0];
        return $arrData[count($arrData)-1];
    }

    /**
     * 税金設定情報を登録する（商品管理用）
     *
     * @param
     * @return
     */
    function setTaxRuleForProduct($tax_rate, $product_id = 0, $product_class_id = 0, $tax_adjust=0, $pref_id = 0, $country_id = 0)
    {
        // 税情報を設定
        SC_Helper_TaxRule_Ex::setTaxRule($calc_rule, $tax_rate, $apply_date, $tax_rule_id=NULL, $tax_adjust=0, $product_id, $product_class_id, $pref_id, $country_id);
    }

    /**
     * 税金設定情報を登録する（仮）リファクタする（memo：規格設定後に商品編集を行うと消費税が0になるのを対応が必要）
     *
     * @param
     * @return
     */
    function setTaxRule($calc_rule, $tax_rate, $apply_date, $tax_rule_id=NULL, $tax_adjust=0, $product_id = 0, $product_class_id = 0, $pref_id = 0, $country_id = 0)
    {
		$table = 'dtb_tax_rule';
		$arrValues = array();
		$arrValues['calc_rule'] = $calc_rule;
		$arrValues['tax_rate'] = $tax_rate;
		$arrValues['tax_adjust'] = $tax_adjust;
		$arrValues['apply_date'] = $apply_date;
		$arrValues['member_id'] = $_SESSION['member_id'];
		$arrValues['update_date'] = 'CURRENT_TIMESTAMP';
		
        // 新規か更新か？
        $objQuery =& SC_Query_Ex::getSingletonInstance();
		if($tax_rule_id == NULL && $product_id != 0 && $product_class_id != 0){
        $where = 'product_id = ? AND product_class_id= ? AND pref_id = ? AND country_id = ?';
        $arrVal = array($product_id, $product_class_id, $pref_id, $country_id);
		$arrCheck = $objQuery->getRow('*', 'dtb_tax_rule', $where, $arrVal);
		$tax_rule_id = $arrCheck['tax_rule_id'];
		}
		
        if($tax_rule_id == NULL) {
            // 税情報を新規
            // INSERTの実行
            $arrValues['tax_rule_id'] = $objQuery->nextVal('dtb_tax_rule_tax_rule_id');
            $arrValues['country_id'] = $country_id;
            $arrValues['pref_id'] = $pref_id;
            $arrValues['product_id'] = $product_id;
            $arrValues['product_class_id'] = $product_class_id;
			$arrValues['create_date'] = 'CURRENT_TIMESTAMP';
        
            $objQuery->insert($table, $arrValues);
        } else {
            // 税情報を更新
            $where = 'tax_rule_id = ?';
            $ret = $objQuery->update($table, $arrValues, $where, array($tax_rule_id));
        }
    }
    
    
    function getTaxRuleList($has_deleted = false)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = 'tax_rule_id, tax_rate, calc_rule, apply_date';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0';
        }
        $table = 'dtb_tax_rule';
        $objQuery->setOrder('tax_rule_id DESC');
        $arrRet = $objQuery->select($col, $table, $where);
        return $arrRet;

    }

    function getTaxRuleData($tax_rule_id, $has_deleted = false)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = 'tax_rule_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        return $objQuery->getRow('*', 'dtb_tax_rule', $where, array($tax_rule_id));
    }

	

    function getTaxRuleByTime($apply_date, $has_deleted = false)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = 'apply_date = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }
        $arrRet = $objQuery->select('*', 'dtb_tax_rule', $where, array($apply_date));
        return $arrRet[0];
    }

    /**
     * 税規約の削除.
     *
     * @param integer $tax_rule_id 税規約ID
     * @return void
     */
    function deleteTaxRuleData($tax_rule_id)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sqlval = array();
        $sqlval['del_flg']     = 1;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'tax_rule_id = ?';
        $objQuery->update('dtb_tax_rule', $sqlval, $where, array($tax_rule_id));
    }
}
