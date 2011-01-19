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
 * Admin_Customer_SearchCustomer のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
class LC_Page_Admin_Customer_SearchCustomer extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
        $this->tpl_mainpage = 'customer/search_customer.tpl';
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
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // POSTのモードがsearchなら顧客検索開始
        if($_POST['mode'] == 'search'){
            $this->objFormParam = new SC_FormParam();
            // 値の初期化
            $this->lfInitParam();
            // POST値の取得
            $this->objFormParam->setParam($_POST);
            // 入力値の変換
            $this->objFormParam->convParam();

            // 入力された値を取得する
            $arrForm = $this->objFormParam->getHashArray();

            // エラーチェック
            $this->arrErr = $this->lfCheckError();
            $is_select = empty($this->arrErr);

            $sqlval = array();
            $where = "";
            
            if ($is_select) {
                $where = "del_flg = 0";

                // 検索
                foreach($arrForm as $tmp_key => $val){
                    if( is_array($val) === false && 0 < strlen($val)){
                        $key = strtr($tmp_key , array('search_' => ''));
                        switch($key){
                            case 'customer_id':
                                $where .= " AND customer_id = ? ";
                                $sqlval[] = $val;
                                break;
                            case 'name01':
                                    $where .= " AND name01 ILIKE ? ";
                                    $sqlval[] = '%'.$val.'%';
                                break;
                            case 'name02':
                                    $where .= " AND name02 ILIKE ? ";
                                    $sqlval[] = '%'.$val.'%';
                                break;
                            case 'kana01':
                                    $where .= " AND kana01 ILIKE ? ";
                                    $sqlval[] = '%'.$val.'%';
                                break;
                            case 'kana02':
                                    $where .= " AND kana02 ILIKE ? ";
                                    $sqlval[] = '%'.$val.'%';
                                break;
                            default :
                                break;
                        }
                    }
                }
            }


            if( $is_select === true ){
                $objQuery = new SC_Query();

                // 既に購入した事がある顧客を取得
                $col = '*';
                $from = 'dtb_customer';
                $order = 'customer_id';
                $arrCustomer = $objQuery->select($col, $from, $where, $sqlval);

                // 顧客情報を取得できたら、テンプレートに
                if( is_array($arrCustomer) === true && count($arrCustomer) > 0){
                    $customer_count = count($arrCustomer);
                    if( $customer_count != 0 ){
                        $this->tpl_linemax = $customer_count;
                    }
                } else {
                    $this->tpl_linemax = null;
                }

                // ページ送りの処理
                if(isset($_POST['search_page_max'])
                   && is_numeric($_POST['search_page_max'])) {
                    $page_max = $_POST['search_page_max'];
                } else {
                    $page_max = SEARCH_PMAX;
                }

                // ページ送りの取得
                $objNavi = new SC_PageNavi($_POST['search_pageno'], $customer_count, $page_max, "fnNaviSearchOnlyPage", NAVI_PMAX);
                $this->tpl_strnavi = $objNavi->strnavi;      // 表示文字列
                $startno = $objNavi->start_row;

                // 取得範囲の指定(開始行番号、行数のセット)
                $objQuery->setLimitOffset($page_max, $startno);
                // 表示順序
                $objQuery->setOrder($order);
                // 検索結果の取得
                $this->arrCustomer = $objQuery->select($col, $from, $where, $sqlval);
            }

        }
        $this->arrForm = $arrForm;
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy()
    {
        parent::destroy();
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("顧客ID", "search_customer_id", INT_LEN, "n", array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("顧客名(姓)", "search_name01", STEXT_LEN, "aKV", array("NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("顧客名(名)", "search_name02", STEXT_LEN, "aKV", array("NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("顧客名(カナ)", 'search_kana01', STEXT_LEN, "CKV", array("NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $this->objFormParam->addParam("顧客名(カナ/名)", 'search_kana02', STEXT_LEN, "CKV", array("NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        return $objErr->arrErr;
    }
}
?>
