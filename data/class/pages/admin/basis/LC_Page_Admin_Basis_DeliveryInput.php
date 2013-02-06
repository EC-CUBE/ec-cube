<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 配送方法設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_DeliveryInput extends LC_Page_Admin_Ex {
    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/delivery_input.tpl';
        $this->tpl_subno = 'delivery';
        $this->tpl_mainno = 'basis';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrProductType = $masterData->getMasterData('mtb_product_type');
        $this->arrPayments = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');
        $this->tpl_maintitle = t('c_Basic information_01');;
        $this->tpl_subtitle = t('c_Delivery method settings_01');;
        $this->mode = $this->getMode();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {

        $this->lfInitParam($this->mode, $objFormParam);
        $objFormParam->setParam($_POST);

        // 入力値の変換
        $objFormParam->convParam();
        $this->arrErr = $this->lfCheckError($objFormParam);

        switch ($this->mode) {
            case 'edit':
                if (count($this->arrErr) == 0) {
                    $objFormParam->setValue('deliv_id', $this->lfRegistData($objFormParam->getHashArray(), $_SESSION['member_id']));
                    $this->tpl_onload = "window.alert('" . t('c_Delivery method settings are complete._01') ."');";
                }
                break;
            case 'pre_edit':
                if (count($this->arrErr) > 0) {
                    trigger_error('', E_USER_ERROR);
                }
                $this->lfGetDelivData($objFormParam);
                break;
            default:
                break;
        }

        $this->arrForm = $objFormParam->getFormParamList();

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* パラメーター情報の初期化 */
    function lfInitParam($mode, &$objFormParam) {
        $objFormParam = new SC_FormParam_Ex();

        switch ($mode) {
            case 'edit':
                $objFormParam->addParam(t('c_Delivery company ID_01'), 'deliv_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Delivery company name_01'), 'name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Name_03'), 'service_name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Explanation_01'), 'remark', LLTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Voucher No. confirmation URL_01'), 'confirm_url', URL_LEN, 'n', array('URL_CHECK', 'MAX_LENGTH_CHECK'), 'http://');
                $objFormParam->addParam(t('c_Available product types_01'), 'product_type_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam(t('c_Available payment methods_01'), 'payment_ids', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));

                for ($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
                    $objFormParam->addParam(t('c_Delivery time T_ARG1_01', array('T_ARG1' => $cnt)), "deliv_time$cnt", STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
                }

                if (INPUT_DELIV_FEE) {
                    for ($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
                        $objFormParam->addParam(t('c_Delivery charge T_ARG1_01', array('T_ARG1' => $cnt)), "fee$cnt", PRICE_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
                    }
                }
                break;

            case 'pre_edit':
                $objFormParam->addParam(t('c_Delivery company ID_01'), 'deliv_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                break;

            default:
                break;
        }
    }

    /**
     * 配送情報を登録する
     *
     * @return $deliv_id
     */
    function lfRegistData($arrRet, $member_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        // 入力データを渡す。
        $sqlval['name'] = $arrRet['name'];
        $sqlval['service_name'] = $arrRet['service_name'];
        $sqlval['remark'] = $arrRet['remark'];
        $sqlval['confirm_url'] = $arrRet['confirm_url'];
        $sqlval['product_type_id'] = $arrRet['product_type_id'];
        $sqlval['creator_id'] = $member_id;
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';

        // deliv_id が決まっていた場合
        if ($arrRet['deliv_id'] != '') {
            $deliv_id = $arrRet['deliv_id'];
            $where = 'deliv_id = ?';
            $objQuery->update('dtb_deliv', $sqlval, $where, array($deliv_id));

            // お届け時間の登録
            $table = 'dtb_delivtime';
            $where = 'deliv_id = ? AND time_id = ?';
            for ($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
                $sqlval = array();
                $keyname = 'deliv_time'.$cnt;
                $arrWhereVal = array($deliv_id, $cnt);
                // 既存データの有無を確認
                $curData = $objQuery->select('*', $table, $where, $arrWhereVal);

                if (strcmp($arrRet[$keyname], '') != 0) {
                    $sqlval['deliv_time'] = $arrRet[$keyname];

                    // 入力が空ではなく、DBに情報があれば更新
                    if (count($curData)) {
                        $objQuery->update($table, $sqlval, $where, $arrWhereVal);
                    }
                    // DBに情報がなければ登録
                    else {
                        $sqlval['deliv_id'] = $deliv_id;
                        $sqlval['time_id'] = $cnt;
                        $objQuery->insert($table, $sqlval);
                    }
                }
                // 入力が空で、DBに情報がある場合は削除
                else if (count($curData)) {
                    $objQuery->delete($table, $where, $arrWhereVal);
                }
            }

            // 配送料の登録
            if (INPUT_DELIV_FEE) {
                for ($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
                    $keyname = 'fee'.$cnt;
                    if (strcmp($arrRet[$keyname], '') != 0) {
                        $sqlval = array('fee' => $arrRet[$keyname]);
                        $objQuery->update('dtb_delivfee', $sqlval, 'deliv_id = ? AND fee_id = ?', array($deliv_id, $cnt));
                    }
                }
            }
        } else {
            // 登録する配送業者IDの取得
            $deliv_id = $objQuery->nextVal('dtb_deliv_deliv_id');
            $sqlval['deliv_id'] = $deliv_id;
            $sqlval['rank'] = $objQuery->max('rank', 'dtb_deliv') + 1;
            $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
            // INSERTの実行
            $objQuery->insert('dtb_deliv', $sqlval);

            $sqlval = array();
            // お届け時間の設定
            for ($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
                $keyname = "deliv_time$cnt";
                if ($arrRet[$keyname] != '') {
                    $sqlval['deliv_id'] = $deliv_id;
                    $sqlval['time_id'] = $cnt;
                    $sqlval['deliv_time'] = $arrRet[$keyname];
                    // INSERTの実行
                    $objQuery->insert('dtb_delivtime', $sqlval);
                }
            }

            if (INPUT_DELIV_FEE) {
                $sqlval = array();
                // 配送料金の設定
//                for ($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
//                    $keyname = "fee$cnt";
//                    if ($arrRet[$keyname] != '') {
//                        $sqlval['deliv_id'] = $deliv_id;
//                        $sqlval['fee'] = $arrRet[$keyname];
//                        $sqlval['pref'] = $cnt;
//                        // INSERTの実行
//                        $sqlval['fee_id'] = $cnt;
//                        $objQuery->insert('dtb_delivfee', $sqlval);
//                    }
//                }
            }
        }

        $objQuery->delete('dtb_payment_options', 'deliv_id = ?', array($arrRet['deliv_id']));
        $sqlval = array();
        $i = 1;
        foreach ($arrRet['payment_ids'] as $val) {
            $sqlval['deliv_id'] = $deliv_id;
            $sqlval['payment_id'] = $val;
            $sqlval['rank'] = $i;
            $objQuery->insert('dtb_payment_options', $sqlval);
            $i++;
        }
        $objQuery->commit();
        return $deliv_id;
    }

    /* 配送業者情報の取得 */
    function lfGetDelivData(&$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $deliv_id = $objFormParam->getValue('deliv_id');

        // パラメーター情報の初期化
        $this->lfInitParam('edit', $objFormParam);

        // 配送業者一覧の取得
        $col = 'deliv_id, name, service_name, remark, confirm_url, product_type_id';
        $where = 'deliv_id = ?';
        $table = 'dtb_deliv';
        $arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
        $objFormParam->setParam($arrRet[0]);
        // お届け時間の取得
        $col = 'deliv_time';
        $where = 'deliv_id = ?  ORDER BY time_id';
        $table = 'dtb_delivtime';
        $arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
        $objFormParam->setParamList($arrRet, 'deliv_time');
        // 配送料金の取得
        $col = 'fee';
        $where = 'deliv_id = ? ORDER BY pref';
        $table = 'dtb_delivfee';
        $arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
        $objFormParam->setParamList($arrRet, 'fee');
        // 支払方法
        $col = 'payment_id';
        $where = 'deliv_id = ? ORDER BY rank';
        $table = 'dtb_payment_options';
        $arrRet = $objQuery->select($col, $table, $where, array($deliv_id));
        $arrPaymentIds = array();
        foreach ($arrRet as $val) {
            $arrPaymentIds[] = $val['payment_id'];
        }
        $objFormParam->setValue('payment_ids', $arrPaymentIds);
    }

    /* 入力内容のチェック */
    function lfCheckError(&$objFormParam) {
        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError();

        if (!isset($objErr->arrErr['name'])) {
            // 既存チェック
            $objDb = new SC_Helper_DB_Ex();
            if ($arrRet['deliv_id'] == '') {
                $ret = $objDb->sfIsRecord('dtb_deliv', 'service_name', array($arrRet['service_name']));
            } else {
                $objQuery =& SC_Query_Ex::getSingletonInstance();
                $ret = (($objQuery->count('dtb_deliv', 'deliv_id != ? AND service_name = ? ', array($arrRet['deliv_id'], $arrRet['service_name'])) > 0)? true : false);
            }
            if ($ret) {
                $objErr->arrErr['service_name'] = t('c_* It is not possible register a combination of the same name.<br>_01');
            }
        }

        return $objErr->arrErr;
    }
}
