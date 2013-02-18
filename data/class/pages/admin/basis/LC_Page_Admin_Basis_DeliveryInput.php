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
        $this->arrPayments = SC_Helper_Payment_Ex::getIDValueList();
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '配送方法設定';
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
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($this->mode, $objFormParam);
        $objFormParam->setParam($_POST);

        // 入力値の変換
        $objFormParam->convParam();
        $this->arrErr = $this->lfCheckError($objFormParam);

        switch ($this->mode) {
            case 'edit':
                if (count($this->arrErr) == 0) {
                    $objFormParam->setValue('deliv_id', $this->lfRegistData($objFormParam->getHashArray(), $_SESSION['member_id']));
                    $this->tpl_onload = "window.alert('配送方法設定が完了しました。');";
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
                $objFormParam->addParam('配送業者ID', 'deliv_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('配送業者名', 'name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('名称', 'service_name', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('説明', 'remark', LLTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
                $objFormParam->addParam('伝票No.確認URL', 'confirm_url', URL_LEN, 'n', array('URL_CHECK', 'MAX_LENGTH_CHECK'), 'http://');
                $objFormParam->addParam('取扱商品種別', 'product_type_id', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
                $objFormParam->addParam('取扱支払方法', 'payment_ids', INT_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));

                for ($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
                    $objFormParam->addParam("お届け時間$cnt", "deliv_time$cnt", STEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
                }

                if (INPUT_DELIV_FEE) {
                    for ($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
                        $objFormParam->addParam("配送料金$cnt", "fee$cnt", PRICE_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
                    }
                }
                break;

            case 'pre_edit':
                $objFormParam->addParam('配送業者ID', 'deliv_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
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
        $objDelivery = new SC_Helper_Delivery_Ex();

        // 入力データを渡す。
        $sqlval['deliv_id'] = $arrRet['deliv_id'];
        $sqlval['name'] = $arrRet['name'];
        $sqlval['service_name'] = $arrRet['service_name'];
        $sqlval['remark'] = $arrRet['remark'];
        $sqlval['confirm_url'] = $arrRet['confirm_url'];
        $sqlval['product_type_id'] = $arrRet['product_type_id'];
        $sqlval['creator_id'] = $member_id;

        // お届け時間
        $sqlval['deliv_time'] = array();
        for ($cnt = 1; $cnt <= DELIVTIME_MAX; $cnt++) {
            $keyname = "deliv_time$cnt";
            if ($arrRet[$keyname] != '') {
                $sqlval['deliv_time'][$cnt] = $arrRet[$keyname];
            }
        }

        // 配送料
        if (INPUT_DELIV_FEE) {
            $sqlval['deliv_fee'] = array();
            // 配送料金の設定
            for ($cnt = 1; $cnt <= DELIVFEE_MAX; $cnt++) {
                $keyname = "fee$cnt";
                if ($arrRet[$keyname] != '') {
                    $fee = array();
                    $fee['fee_id'] = $cnt;
                    $fee['fee'] = $arrRet[$keyname];
                    $fee['pref'] = $cnt;
                    $sqlval['deliv_fee'][$cnt] = $fee;
                }
            }
        }

        // 支払い方法
        $sqlval['payment_ids'] = array();
        foreach ($arrRet['payment_ids'] as $payment_id) {
            $sqlval['payment_ids'][] = $payment_id;
        }

        $deliv_id = $objDelivery->save($sqlval);

        return $deliv_id;
    }

    /* 配送業者情報の取得 */
    function lfGetDelivData(&$objFormParam) {
        $objDelivery = new SC_Helper_Delivery_Ex();

        $deliv_id = $objFormParam->getValue('deliv_id');

        // パラメーター情報の初期化
        $this->lfInitParam('edit', $objFormParam);

        $arrDeliv = $objDelivery->get($deliv_id);

        // お届け時間
        $deliv_times = array();
        foreach ($arrDeliv['deliv_time'] as $value) {
            $deliv_times[]['deliv_time'] = $value;
        }
        $objFormParam->setParamList($deliv_times, 'deliv_time');
        unset($arrDeliv['deliv_time']);
        // 配送料金
        $deliv_fee = array();
        foreach ($arrDeliv['deliv_fee'] as $value) {
            $deliv_fee[]['fee'] = $value['fee'];
        }
        $objFormParam->setParamList($deliv_fee, 'fee');
        unset($arrDeliv['deliv_fee']);
        // 支払方法
        $objFormParam->setValue('payment_ids', $arrDeliv['payment_ids']);
        unset($arrDeliv['payment_ids']);
        // 配送業者
        $objFormParam->setParam($arrDeliv);
    }

    /* 入力内容のチェック */
    function lfCheckError(&$objFormParam) {
        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError();

        if (!isset($objErr->arrErr['name'])) {
            // 既存チェック
            $objDelivery = new SC_Helper_Delivery_Ex();
            if ($objDelivery->checkExist($arrRet)) {
                $objErr->arrErr['service_name'] = '※ 同じ名称の組み合わせは登録できません。<br>';
            }
        }

        return $objErr->arrErr;
    }
}
