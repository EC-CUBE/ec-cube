<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework\Helper;

use Eccube\Application;
use Eccube\Framework\Customer;
use Eccube\Framework\Query;

/**
 * 税規約を管理するヘルパークラス.
 *
 * @package Helper
 * @author AMUAMU
 */
class TaxRuleHelper
{
    /**
     * 設定情報に基づいて税金付与した金額を返す
     *
     * @param int $price 計算対象の金額
     * @param int $product_id 商品ID
     * @param int $product_class_id 商品規格ID
     * @param int $pref_id 都道府県ID
     * @param int $country_id 国ID
     * @return double 税金付与した金額
     */
    public static function sfCalcIncTax($price, $product_id = 0, $product_class_id = 0, $pref_id =0, $country_id = 0)
    {
        return $price + static::sfTax($price, $product_id, $product_class_id, $pref_id, $country_id);
    }

    /**
     * 設定情報に基づいて税金の金額を返す
     *
     * @param int $price 計算対象の金額
     * @param int $product_id 商品ID
     * @param int $product_class_id 商品規格ID
     * @param int $pref_id 都道府県ID
     * @param int $country_id 国ID
     * @return double 税金付与した金額
     */
    public static function sfTax($price, $product_id = 0, $product_class_id = 0, $pref_id =0, $country_id = 0)
    {
        $arrTaxRule = static::getTaxRule($product_id, $product_class_id, $pref_id, $country_id);

        return static::calcTax($price, $arrTaxRule['tax_rate'], $arrTaxRule['tax_rule'], $arrTaxRule['tax_adjust']);
    }

    /**
     * 設定情報IDに基づいて税金付与した金額を返す
     * (受注データのようにルールが決まっている場合用)
     *
     * @param int $price 計算対象の金額
     * @param int $tax_rule_id 税規約ID
     * @return double 税金付与した金額
     */
    public static function calcIncTaxFromRuleId($price, $tax_rule_id = 0)
    {
        return $price + static::calcTaxFromRuleId($price, $tax_rule_id);
    }

    /**
     * 設定情報IDに基づいて税金の金額を返す
     * (受注データのようにルールが決まっている場合用)
     *
     * @param int $price 計算対象の金額
     * @param int $tax_rule_id 税規約ID
     * @return double 税金付与した金額
     */
    public static function calcTaxFromRuleId($price, $tax_rule_id = 0)
    {
        $arrTaxRule = static::getTaxRuleData($tax_rule_id);

        return static::calcTax($price, $arrTaxRule['tax_rate'], $arrTaxRule['tax_rule'], $arrTaxRule['tax_adjust']);
    }

    /**
     * 税金額を計算する
     *
     * @param int $price 計算対象の金額
     * @param int $tax 税率(%単位)
     *     XXX int のみか不明
     * @param int $calc_rule 端数処理
     * @param int $tax_adjust 調整額
     * @return double 税金額
     */
    public static function calcTax ($price, $tax, $calc_rule, $tax_adjust = 0)
    {
        $real_tax = $tax / 100;
        $ret = $price * $real_tax;
        $ret = self::roundByCalcRule($ret, $calc_rule);

        return $ret + $tax_adjust;
    }

    /**
     * 現在有効な税率設定情報を返す
     *
     * @param int $product_id 商品ID
     * @param int $product_class_id 商品規格ID
     * @param int $pref_id 都道府県ID
     * @param int $country_id 国ID
     * @return integer 税設定情報
     */
    public static function getTaxRule($product_id = 0, $product_class_id = 0, $pref_id = 0, $country_id = 0)
    {
        // 複数回呼出があるのでキャッシュ化
        static $data_c = array();

        // 初期化
        $product_id = $product_id > 0 ? $product_id : 0;
        $product_class_id = $product_class_id > 0 ? $product_class_id : 0;
        $pref_id = $pref_id > 0 ? $pref_id : 0;
        $country_id = $country_id > 0 ? $country_id : 0;

        // 一覧画面の速度向上のため商品単位税率設定がOFFの時はキャッシュキーを丸めてしまう
        if (OPTION_PRODUCT_TAX_RULE == 1) {
            $cache_key = "$product_id,$product_class_id,$pref_id,$country_id";
        } else {
            $cache_key = "$pref_id,$country_id";
        }

        if (empty($data_c[$cache_key])) {
            // ログイン済み会員で国と地域指定が無い場合は、会員情報をデフォルトで利用。管理画面では利用しない
            if (!(defined('ADMIN_FUNCTION') && ADMIN_FUNCTION == true)) {
                /* @var $objCustomer Customer */
                $objCustomer = Application::alias('eccube.customer');
                if ($objCustomer->isLoginSuccess(true)) {
                    if ($country_id == 0) {
                        $country_id = $objCustomer->getValue('country_id');
                    }
                    if ($pref_id == 0) {
                        $pref_id = $objCustomer->getValue('pref');
                    }
                }
            }

            $arrRet = array();
            // リクエストの配列化
            $arrRequest = array('product_id' => $product_id,
                            'product_class_id' => $product_class_id,
                            'pref_id' => $pref_id,
                            'country_id' => $country_id);

            // 地域設定を優先するが、システムパラメーターなどに設定を持っていくか
            // 後に書いてあるほど優先される、詳細後述MEMO参照
            $arrPriorityKeys = explode(',', TAX_RULE_PRIORITY);

            // 条件に基づいて税の設定情報を取得
            $objQuery = Application::alias('eccube.query');
            $table = 'dtb_tax_rule';
            $cols = '*';

            // 商品税率有無設定により分岐
            if (OPTION_PRODUCT_TAX_RULE == 1) {
                $where = '
                        (
                            (product_id = 0 OR product_id = ?)
                            AND (product_class_id = 0 OR product_class_id = ?)
                        )
                    AND (pref_id = 0 OR pref_id = ?)
                    AND (country_id = 0 OR country_id = ?)
                    AND apply_date < CURRENT_TIMESTAMP
                    AND del_flg = 0';
                $arrVal = array($product_id, $product_class_id, $pref_id, $country_id);
            } else {
                $where = '     product_id = 0 '
                       . ' AND product_class_id = 0 '
                       . ' AND (pref_id = 0 OR pref_id = ?)'
                       . ' AND (country_id = 0 OR country_id = ?)'
                       . ' AND apply_date < CURRENT_TIMESTAMP'
                       . ' AND del_flg = 0';
                $arrVal = array($pref_id, $country_id);
            }

            $order = 'apply_date DESC';
            $objQuery->setOrder($order);
            $arrData = $objQuery->select($cols, $table, $where, $arrVal);
            // 優先度付け
            // MEMO: 税の設定は相反する設定を格納可能だが、その中で優先度を付けるため
            //       キーの優先度により、利用する税設定を判断する
            //       優先度が同等の場合、適用日付で判断する
            foreach ($arrData as $data_key => $data) {
                $res = 0;
                foreach ($arrPriorityKeys as $key_no => $key) {
                    if ($arrRequest[$key] != 0 && $data[$key] == $arrRequest[$key]) {
                        // 配列の数値添字を重みとして利用する
                        $res += 1 << ($key_no + 1);
                    }
                }
                $arrData[$data_key]['rank'] = $res;
            }

            // 優先順位が高いものを返却値として確定
            // 適用日降順に並んでいるので、単に優先順位比較のみで格納判断可能
            foreach ($arrData as $data) {
                if (!isset($arrRet['rank']) || $arrRet['rank'] < $data['rank']) {
                    // 優先度が高い場合, または空の場合
                    $arrRet = $data;
                }
            }
            // XXXX: 互換性のためtax_ruleにもcalc_ruleを設定
            $arrRet['tax_rule'] = $arrRet['calc_rule'];
            $data_c[$cache_key] = $arrRet;
        }

        return $data_c[$cache_key];
    }

    /**
     * 税率設定情報を登録する（商品管理用）
     *
     * @param float $tax_rate 消費税率
     * @param int $product_id 商品ID
     * @param int $product_class_id 商品規格ID
     * @param integer $tax_adjust 消費税加算額
     * @param int $pref_id 県ID
     * @param int $country_id 国ID
     * @return void
     */
    public static function setTaxRuleForProduct($tax_rate, $product_id = 0, $product_class_id = 0, $tax_adjust=0, $pref_id = 0, $country_id = 0)
    {
        // 基本設定を取得
        $arrRet = static::getTaxRule($product_id, $product_class_id);

        // 基本設定の消費税率と一緒であれば設定しない
        if ($arrRet['tax_rate'] != $tax_rate) {
            // 課税規則は基本設定のものを使用
            $calc_rule = $arrRet['calc_rule'];
            // 日付は登録時点を設定
            $apply_date = date('Y/m/d H:i:s');
            // 税情報を設定
            static::setTaxRule($calc_rule, $tax_rate, $apply_date, NULL, $tax_adjust, $product_id, $product_class_id, $pref_id, $country_id);
        }
    }

    /**
     * 税率設定情報を登録する（仮）リファクタする（memo：規格設定後に商品編集を行うと消費税が0になるのを対応が必要）
     *
     * @param int $calc_rule 端数処理
     * @param int $tax_rate 税率
     * @param string $apply_date 適用日時
     * @param null|integer $tax_rule_id 税規約ID
     * @param int $tax_adjust 調整額
     * @param int $product_id 商品ID
     * @param int $product_class_id 商品規格ID
     * @param int $pref_id 都道府県ID
     * @param int $country_id 国ID
     * @return void
     */
    public function setTaxRule($calc_rule, $tax_rate, $apply_date, $tax_rule_id=NULL, $tax_adjust=0, $product_id = 0, $product_class_id = 0, $pref_id = 0, $country_id = 0)
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
        $objQuery = Application::alias('eccube.query');
        if ($tax_rule_id == NULL && $product_id != 0 && $product_class_id != 0) {
            $where = 'product_id = ? AND product_class_id= ? AND pref_id = ? AND country_id = ?';
            $arrVal = array($product_id, $product_class_id, $pref_id, $country_id);
            $arrCheck = $objQuery->getRow('*', 'dtb_tax_rule', $where, $arrVal);
            $tax_rule_id = $arrCheck['tax_rule_id'];
        }

        if ($tax_rule_id == NULL) {
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
            $objQuery->update($table, $arrValues, $where, array($tax_rule_id));
        }
    }

    /**
     * @param bool $has_deleted
     * @return array|null
     */
    public function getTaxRuleList($has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $col = 'tax_rule_id, tax_rate, calc_rule, apply_date';
        $where = '';
        if (!$has_deleted) {
            $where .= 'del_flg = 0 AND product_id = 0 AND product_class_id = 0';
        }
        $table = 'dtb_tax_rule';
        // 適用日時順に更新
        $objQuery->setOrder('apply_date DESC');
        $arrRet = $objQuery->select($col, $table, $where);

        return $arrRet;
    }

    /**
     * @param int $tax_rule_id
     * @param bool $has_deleted
     * @return array
     */
    public function getTaxRuleData($tax_rule_id, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
        $where = 'tax_rule_id = ?';
        if (!$has_deleted) {
            $where .= ' AND del_flg = 0';
        }

        return $objQuery->getRow('*', 'dtb_tax_rule', $where, array($tax_rule_id));
    }

    /**
     * @param string $apply_date
     * @param bool $has_deleted
     * @return mixed
     */
    public function getTaxRuleByTime($apply_date, $has_deleted = false)
    {
        $objQuery = Application::alias('eccube.query');
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
     * @param  int $tax_rule_id 税規約ID
     * @return void
     */
    public function deleteTaxRuleData($tax_rule_id)
    {
        $objQuery = Application::alias('eccube.query');

        $sqlval = array();
        $sqlval['del_flg']     = 1;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'tax_rule_id = ?';
        $objQuery->update('dtb_tax_rule', $sqlval, $where, array($tax_rule_id));
    }

    /**
     * 課税規則に応じて端数処理を行う
     *
     * @param float|integer $value 端数処理を行う数値
     * @param integer $calc_rule 課税規則
     * @return double 端数処理後の数値
     */
    public static function roundByCalcRule($value, $calc_rule)
    {
        switch ($calc_rule) {
            // 四捨五入
            case 1:
                $ret = round($value);
                break;
            // 切り捨て
            case 2:
                $ret = floor($value);
                break;
            // 切り上げ
            case 3:
                $ret = ceil($value);
                break;
            // デフォルト:切り上げ
            default:
                $ret = ceil($value);
                break;
        }

        return $ret;
    }
}
