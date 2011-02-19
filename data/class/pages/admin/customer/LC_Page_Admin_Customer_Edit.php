<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * 顧客情報修正 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Customer_Edit extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'customer/edit.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subnavi = 'customer/subnavi.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_subtitle = '顧客マスタ';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
        $this->arrStatus = $masterData->getMasterData("mtb_customer_status");
        $this->arrMagazineType = $masterData->getMasterData("mtb_magazine_type");

        // 日付プルダウン設定
        $objDate = new SC_Date(BIRTH_YEAR);
        $this->arrYear = $objDate->getYear();    
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();
        
        // 支払い方法種別
        $objDb = new SC_Helper_DB_Ex();
        $this->arrPayment = $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
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
        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess(new SC_Session());

        // 不正アクセスチェック 
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!SC_Helper_Session_Ex::isValidToken()) {
//                SC_Utils_Ex::sfDispError(INVALID_MOVE_ERRORR);
                echo "不正アクセス";
                exit;
            }
        }
        // トランザクションID
        $this->transactionid = SC_Helper_Session_Ex::getToken();

        // パラメータ管理クラス
        $objFormParam = new SC_FormParam();
        // 検索引き継ぎ用パラメーター管理クラス
        $objFormSearchParam = new SC_FormParam();

        // モードによる処理切り替え
        switch ($this->getMode()) {
        case 'edit':
        case 'edit_search':
            //検索引き継ぎ用パラメーター処理
            $this->lfInitSearchParam($objFormSearchParam);
            $objFormSearchParam->setParam($_REQUEST);
            $this->arrErr = $this->lfCheckErrorSearchParam($objFormSearchParam);
            $this->arrSearchData = $objFormSearchParam->getHashArray();
            if(!SC_Utils_Ex::isBlank($this->arrErr)) {
                return;
            }
            //指定顧客の情報をセット
            $this->arrForm = SC_Helper_Customer::sfGetCustomerData($objFormSearchParam->getValue("edit_customer_id"), true);
            //購入履歴情報の取得
//            $this->arrPurchaseHistory = $this->lfPurchaseHistory($objFormSearchParam->getValue("edit_customer_id"));
            break;
        case 'confirm':
            //パラメーター処理
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            // 入力パラメーターチェック
            $this->arrErr = $this->lfCheckError($objFormParam);
            $this->arrForm = $objFormParam->getHashArray();
            //検索引き継ぎ用パラメーター処理
            $this->lfInitSearchParam($objFormSearchParam);
            $objFormSearchParam->setParam($objFormParam->getValue("search_data"));
            $this->arrSearchErr = $this->lfCheckErrorSearchParam($objFormSearchParam);
            $this->arrSearchData = $objFormSearchParam->getHashArray();
            if(!SC_Utils_Ex::isBlank($this->arrErr) or !SC_Utils_Ex::isBlank($this->arrSearchErr)) {
                return;
            }
            // 確認画面テンプレートに切り替え
            $this->tpl_mainpage = 'customer/edit_confirm.tpl';
            break;
        case 'return':
            //パラメーター処理
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            // 入力パラメーターチェック
            $this->arrErr = $this->lfCheckError($objFormParam);
            $this->arrForm = $objFormParam->getHashArray();
            //検索引き継ぎ用パラメーター処理
            $this->lfInitSearchParam($objFormSearchParam);
            $objFormSearchParam->setParam($objFormParam->getValue("search_data"));
            $this->arrSearchErr = $this->lfCheckErrorSearchParam($objFormSearchParam);
            $this->arrSearchData = $objFormSearchParam->getHashArray();
            if(!SC_Utils_Ex::isBlank($this->arrErr) or !SC_Utils_Ex::isBlank($this->arrSearchErr)) {
                return;
            }
            //購入履歴情報の取得
//            $this->arrPurchaseHistory = $this->lfPurchaseHistory($objFormParam->getValue("customer_id"));
            break;
        case 'complete':
            //登録・保存処理
            //パラメーター処理
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            // 入力パラメーターチェック
            $this->arrErr = $this->lfCheckError($objFormParam);
            $this->arrForm = $objFormParam->getHashArray();
            //検索引き継ぎ用パラメーター処理
            $this->lfInitSearchParam($objFormSearchParam);
            $objFormSearchParam->setParam($objFormParam->getValue("search_data"));
            $this->arrSearchErr = $this->lfCheckErrorSearchParam($objFormSearchParam);
            $this->arrSearchData = $objFormSearchParam->getHashArray();
            if(!SC_Utils_Ex::isBlank($this->arrErr) or !SC_Utils_Ex::isBlank($this->arrSearchErr)) {
                return;
            }
            $this->lfRegistData($objFormParam);
            $this->tpl_mainpage = 'customer/edit_complete.tpl';
            break;
        default:
            break;
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * パラメーター情報の初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        // 会員項目のパラメーター取得
        SC_Helper_Customer_Ex::sfCustomerEntryParam($objFormParam, true);
        // 検索結果一覧画面への戻り用パラメーター
        $objFormParam->addParam("検索用データ", "search_data", "", "", array(), "", false);
    }

    /**
     * 検索パラメーター引き継ぎ用情報の初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return void
     */
    function lfInitSearchParam(&$objFormParam) {
        SC_Helper_Customer_Ex::sfSetSearchParam($objFormParam);
        // 初回受け入れ時用
        $objFormParam->addParam("編集対象顧客ID", "edit_customer_id", INT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * 検索パラメーターエラーチェック
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    function lfCheckErrorSearchParam(&$objFormParam) {
        return SC_Helper_Customer_Ex::sfCheckErrorSearchParam($objFormParam);
    }
    
    /**
     * フォーム入力パラメーターエラーチェック
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    function lfCheckError(&$objFormParam) {
        $arrErr = SC_Helper_Customer_Ex::sfCustomerMypageErrorCheck($objFormParam, true);
        
        //メアド重複チェック(共通ルーチンは使えない)
        $objQuery   =& SC_Query::getSingletonInstance();
        $col = "email, email_mobile, customer_id";
        $table = "dtb_customer";
        $where = "del_flg <> 1 AND (email Like ? OR email_mobile Like ?)";
        $arrVal = array($objFormParam->getValue('email'), $objFormParam->getValue('email_mobile'));
        if($objFormParam->getValue("customer_id")) {
            $where .= " AND customer_id <> ?";
            $arrVal[] = $objFormParam->getValue("customer_id");
        }
        $arrData = $objQuery->getRow($col, $table, $where, $arrVal);
        if(!SC_Utils_Ex::isBlank($arrData['email'])) {
            if($arrData['email'] == $objFormParam->getValue('email')) {
                $arrErr['email'] = '※ すでに他の会員(ID:' . $arrData['customer_id'] . ')が使用しているアドレスです。';
            }else if($arrData['email'] == $objFormParam->getValue('email_mobile')) {
                $arrErr['email_mobile'] = '※ すでに他の会員(ID:' . $arrData['customer_id'] . ')が使用しているアドレスです。';
            }
        }
        if(!SC_Utils_Ex::isBlank($arrData['email_mobile'])) {
            if($arrData['email_mobile'] == $objFormParam->getValue('email_mobile')) {
                $arrErr['email_mobile'] = '※ すでに他の会員(ID:' . $arrData['customer_id'] . ')が使用している携帯アドレスです。';
            }else if($arrData['email_mobile'] == $objFormParam->getValue('email')) {
                $arrErr['email_mobile'] = '※ すでに他の会員(ID:' . $arrData['customer_id'] . ')が使用している携帯アドレスです。';
            }
        }
        return $arrErr;
    }

    /**
     * 登録処理
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    function lfRegistData(&$objFormParam) {
        $objQuery   =& SC_Query::getSingletonInstance();
        // 登録用データ取得
        $arrData = $objFormParam->getDbArray();
        // 足りないものを作る
        if(!SC_Utils_Ex::isBlank($objFormParam->getValue('year'))) {
            $arrData['birth'] = $objFormParam->getValue('year') . '/'
                            . $objFormParam->getValue('month') . '/'
                            . $objFormParam->getValue('day') 
                            . ' 00:00:00';
        }

        if(!is_numeric($arrData['customer_id'])) {
            $arrData['secret_key'] = SC_Utils_Ex::sfGetUniqRandomId("r");
        }else {
            $arrOldCustomerData = SC_Helper_Customer_Ex::sfGetCustomerData($arrData['customer_id']);
            if($arrOldCustomerData['status'] != $arrData['status']) {
                $arrData['secret_key'] = SC_Utils_Ex::sfGetUniqRandomId("r");
            }
        }
        return SC_Helper_Customer_Ex::sfEditCustomerData($arrData, $arrData['customer_id']);
    }

    //購入履歴情報の取得
    function lfPurchaseHistory($customer_id){
        $objQuery   =& SC_Query::getSingletonInstance();
        $this->tpl_pageno = $_POST['search_pageno'];
        $this->edit_customer_id = $customer_id;

        // ページ送りの処理
        $page_max = SEARCH_PMAX;
        //購入履歴の件数取得
        $this->tpl_linemax = $objQuery->count("dtb_order","customer_id=? AND del_flg = 0 ", array($customer_id));
        $linemax = $this->tpl_linemax;

        // ページ送りの取得
        $objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchPage2", NAVI_PMAX);
        $this->arrPagenavi = $objNavi->arrPagenavi;
        $this->arrPagenavi['mode'] = 'edit';
        $startno = $objNavi->start_row;

        // 取得範囲の指定(開始行番号、行数のセット)
        $this->objQuery->setLimitOffset($page_max, $startno);
        // 表示順序
        $order = "order_id DESC";
        $this->objQuery->setOrder($order);
        //購入履歴情報の取得
        $arrPurchaseHistory = $this->objQuery->select("*", "dtb_order", "customer_id=? AND del_flg = 0 ", array($customer_id));

        return $arrPurchaseHistory;
    }

}
?>
