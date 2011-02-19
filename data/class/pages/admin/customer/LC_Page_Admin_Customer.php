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
 * 顧客管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Customer extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'customer/index.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subnavi = 'customer/subnavi.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_subtitle = '顧客マスタ';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrJob["不明"] = "不明";
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrPageRows = $masterData->getMasterData("mtb_page_rows");
        $this->arrStatus = $masterData->getMasterData("mtb_customer_status");
        $this->arrMagazineType = $masterData->getMasterData("mtb_magazine_type");

        // 日付プルダウン設定
        $objDate = new SC_Date(BIRTH_YEAR);
        $this->arrYear = $objDate->getYear();   
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();
        $this->objDate = $objDate;

        // カテゴリ一覧設定
        $objDb = new SC_Helper_DB_Ex();
        $this->arrCatList = $objDb->sfGetCategoryList();
        
        $this->httpCacheControl('nocache');
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
                SC_Utils_Ex::sfDispError(INVALID_MOVE_ERRORR);
            }
        }
        // トランザクションID
        $this->transactionid = SC_Helper_Session_Ex::getToken();

        // パラメータ管理クラス
        $objFormParam = new SC_FormParam();
        // パラメータ設定
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        // パラメーター読み込み
        $this->arrForm = $this->lfGetFormParam($objFormParam);
        // 検索ワードの引き継ぎ
        $this->arrHidden = $this->lfGetSearchWords($objFormParam);
        // 入力パラメーターチェック
        $this->arrErr = $this->lfCheckError($objFormParam);
        if(!SC_Utils_Ex::isBlank($this->arrErr)) {
            return;
        }

        // モードによる処理切り替え
        switch ($this->getMode()) {
        case 'delete':
            $this->is_delete = $this->lfDoDeleteCustomer($objFormParam->getValue('edit_customer_id'));
            list($this->tpl_linemax, $this->arrData, $this->objNavi) = $this->lfDoSearch($this->arrForm);
            $this->arrPagenavi = $this->objNavi->arrPagenavi;
            break;
        case 'resend_mail':
            $this->is_resendmail = $this->lfDoResendMail($objFormParam->getValue('edit_customer_id'));
            list($this->tpl_linemax, $this->arrData, $this->objNavi) = $this->lfDoSearch($this->arrForm);
            $this->arrPagenavi = $this->objNavi->arrPagenavi;
            break;
        case 'search':
            list($this->tpl_linemax, $this->arrData, $this->objNavi) = $this->lfDoSearch($this->arrForm);
            $this->arrPagenavi = $this->objNavi->arrPagenavi;
            break;
        case 'csv':
            $this->lfDoCSV($this->arrForm);
            exit;
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
        SC_Helper_Customer_Ex::sfSetSearchParam($objFormParam);
    }

    /**
     * エラーチェック
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    function lfCheckError(&$objFormParam) {
        return SC_Helper_Customer_Ex::sfCheckErrorSearchParam($objFormParam);
    }

    /**
     * 顧客を削除する処理
     *
     * @param integer $customer_id 顧客ID
     * @return boolean true:成功 false:失敗
     */
    function lfDoDeleteCustomer($customer_id) {
        $arrData = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($customer_id, "del_flg = 0");
        if(SC_Utils_Ex::isBlank($arrData)) {
            //対象となるデータが見つからない。
            return false;
        }
        // XXXX: 仮会員は物理削除となっていたが論理削除に変更。
        $arrVal["del_flg"] = "1";
        $arrVal["update_date"] ="now()";
        SC_Helper_Customer_Ex::sfEditCustomerData($arrVal, $customer_id);
        return true;
    }

    /**
     * 顧客に登録メールを再送する処理
     *
     * @param integer $customer_id 顧客ID
     * @return boolean true:成功 false:失敗
     */
    function lfDoResendMail($cutomer_id) {
        $arrData = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($customer_id);
        if(SC_Utils_Ex::isBlank($arrData) or $arrData['del_flg'] == 1) {
            //対象となるデータが見つからない、または削除済み
            return false;
        }
        // 登録メール再送
        $objHelperMail = new SC_Helper_Mail_Ex();
        $objHelperMail->sfSendRegistMail($arrData['secret_key'], $customer_id);
        return true;
    }

    /**
     * 顧客一覧を検索する処理
     *
     * @param array $arrParam 検索パラメーター連想配列
     * @return array( integer 全体件数, mixed 顧客データ一覧配列, mixed SC_PageNaviオブジェクト)
     */
    function lfDoSearch($arrParam) {
        $objQuery =& SC_Query::getSingletonInstance();
        $objSelect = new SC_CustomerList($arrParam, "customer");
        $page_rows = $arrParam['page_rows'];
        if(SC_Utils_Ex::sfIsInt($page_rows)) {
            $page_max = $page_rows;
        }else{
            $page_max = SEARCH_PMAX;
        }
        $disp_pageno = $arrParam['search_pageno'];
        if($disp_pageno == 0) {
            $disp_pageno = 1;
        }
        $offset = $page_max * ($disp_pageno - 1);
        $objSelect->setLimitOffset($page_max, $offset);
        $arrData = $objQuery->getAll($objSelect->getList(), $objSelect->arrVal);
        
        // 該当全体件数の取得
        $linemax = $objQuery->getOne($objSelect->getListCount(), $objSelect->arrVal);
        // ページ送りの取得
        $objNavi = new SC_PageNavi($arrParam['search_pageno'],
                                    $linemax,
                                    $page_max,
                                    "fnCustomerPage",
                                    NAVI_PMAX);
        return array($linemax, $arrData, $objNavi);
    }

    /**
     * 顧客一覧CSVを検索してダウンロードする処理
     *
     * @param array $arrParam 検索パラメーター連想配列
     * @return boolean true:成功 false:失敗
     */
    function lfDoCSV($arrParam) {
        $objSelect = new SC_CustomerList($arrParam, "customer");
        $order = "update_date DESC, customer_id DESC";
        require_once(CLASS_EX_REALDIR . "helper_extends/SC_Helper_CSV_Ex.php");
        $objCSV = new SC_Helper_CSV_Ex();
        list($where, $arrVal) = $objSelect->getWhere();
        $objCSV->sfDownloadCsv('2', $where, $arrVal);
    }

    /**
     * 検索パラメーター引継ぎ用展開
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array 引き継ぎ用連想配列
     */
    function lfGetSearchWords(&$objFormParam) {
        $arrData = $objFormParam->getSearchArray("search_");
        $arrData['sex'] = SC_Utils_Ex::sfMergeParamCheckBoxes($objFormParam->getValue('sex'));
        $arrData['status'] = SC_Utils_Ex::sfMergeParamCheckBoxes($objFormParam->getValue('status'));
        $arrData['job'] = SC_Utils_Ex::sfMergeParamCheckBoxes($objFormParam->getValue('job'));
    }

    /**
     * 表示用パラメーター値取得処理
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array 表示用連想配列
     */
    function lfGetFormParam(&$objFormParam) {
        $arrForm = $objFormParam->getHashArray();
        // 配列形式のデータの展開処理
        $val_sex = $objFormParam->getValue('sex');
        if(!is_array($val_sex) and !SC_Utils_Ex::isBlank($val_sex)) {
            $arrForm['sex'] = explode("-", $val_sex);
        }
        $val_status = $objFormParam->getValue('status');
        if(!is_array($val_status) and !SC_Utils_Ex::isBlank($val_status)) {
            $arrForm['status'] = explode("-", $val_status);
        }
        $val_job = $objFormParam->getValue('job');
        if(!is_array($val_job) and !SC_Utils_Ex::isBlank($val_job)) {
            $arrForm['job'] = explode("-", $val_job);
        }
        return $arrForm;
    }

}
?>
