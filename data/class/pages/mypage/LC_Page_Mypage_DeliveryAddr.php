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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * お届け先追加 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_DeliveryAddr extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = TEMPLATE_DIR . 'mypage/delivery_addr.tpl';
        $this->tpl_title = "お届け先の追加･変更";
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref= $masterData->getMasterData("mtb_pref",
                            array("pref_id", "pref_name", "rank"));
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView(false);
        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();
        $ParentPage = MYPAGE_DELIVADDR_URL;

        // GETでページを指定されている場合には指定ページに戻す
        if (isset($_GET['page'])) {
            $ParentPage = htmlspecialchars($_GET['page'],ENT_QUOTES);
        }else if(isset($_POST['ParentPage'])) {
            $ParentPage = htmlspecialchars($_POST['ParentPage'],ENT_QUOTES);
        }
        $this->ParentPage = $ParentPage;

        //ログイン判定
        if (!$objCustomer->isLoginSuccess()){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        if (!isset($_GET['other_deliv_id'])) $_GET['other_deliv_id'] = "";

        if ($_POST['mode'] == ""){
            $_SESSION['other_deliv_id'] = $_GET['other_deliv_id'];
        }

        if ($_GET['other_deliv_id'] != ""){
            //不正アクセス判定
            $flag = $objQuery->count("dtb_other_deliv", "customer_id=? AND other_deliv_id=?", array($objCustomer->getValue("customer_id"), $_SESSION['other_deliv_id']));
            if (!$objCustomer->isLoginSuccess() || $flag == 0){
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
            }
        }

        //別のお届け先ＤＢ登録用カラム配列
        $arrRegistColumn = array(
                                 array("column" => "name01",    "convert" => "aKV"),
                                 array("column" => "name02",    "convert" => "aKV"),
                                 array("column" => "kana01",    "convert" => "CKV"),
                                 array("column" => "kana02",    "convert" => "CKV"),
                                 array("column" => "zip01",     "convert" => "n"),
                                 array("column" => "zip02",     "convert" => "n"),
                                 array("column" => "pref",      "convert" => "n"),
                                 array("column" => "addr01",    "convert" => "aKV"),
                                 array("column" => "addr02",    "convert" => "aKV"),
                                 array("column" => "tel01",     "convert" => "n"),
                                 array("column" => "tel02",     "convert" => "n"),
                                 array("column" => "tel03",     "convert" => "n"),
                                 );


        if ($_GET['other_deliv_id'] != ""){
            //別のお届け先情報取得
            $arrOtherDeliv = $objQuery->select("*", "dtb_other_deliv", "other_deliv_id=? ", array($_SESSION['other_deliv_id']));
            $this->arrForm = $arrOtherDeliv[0];
        }

        switch ($_POST['mode']) {
            case 'edit':
                $_POST = $this->lfConvertParam($_POST,$arrRegistColumn);
                $this->arrErr = $this->lfErrorCheck($_POST);
                if ($this->arrErr){
                    foreach ($_POST as $key => $val){
                        if ($val != "") $this->arrForm[$key] = $val;
                    }
                } else {
                    
                    if ($_POST['ParentPage'] == MYPAGE_DELIVADDR_URL || $_POST['ParentPage'] == URL_DELIV_TOP) {
                        $this->tpl_onload = "fnUpdateParent('". $this->getLocation($_POST['ParentPage']) ."'); window.close();";
                    } else {
                        SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                    }
                    
                    $this->lfRegistData($_POST, $arrRegistColumn, $objCustomer);
                }
                break;
        }

        $objView->assignobj($this);
        $objView->display($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* エラーチェック */
    function lfErrorCheck() {
        $objErr = new SC_CheckError();

        $objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("フリガナ（姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("フリガナ（名）", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("住所（1）", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("住所（2）", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("電話番号1", 'tel01'), array("EXIST_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("電話番号2", 'tel02'), array("EXIST_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("電話番号3", 'tel03'), array("EXIST_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("電話番号", "tel01", "tel02", "tel03") ,array("TEL_CHECK"));
        return $objErr->arrErr;

    }

    /* 登録実行 */
    function lfRegistData($array, $arrRegistColumn, &$objCustomer) {
        $objQuery = new SC_Query();
        foreach ($arrRegistColumn as $data) {
            if (strlen($array[ $data["column"] ]) > 0) {
                $arrRegist[ $data["column"] ] = $array[ $data["column"] ];
            }
        }

        $arrRegist['customer_id'] = $objCustomer->getvalue('customer_id');

        // 追加
        if (strlen($_POST['other_deliv_id'] == 0)) {
            // 別のお届け先登録数の取得
            $deliv_count = $objQuery->count("dtb_other_deliv", "customer_id=?", array($objCustomer->getValue('customer_id')));
            // 別のお届け先最大登録数に達している場合、エラー
            if ($deliv_count >= DELIV_ADDR_MAX) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", false, '別のお届け先最大登録数に達しています。');
            }
            
            // 実行
            $arrRegist['other_deliv_id'] = $objQuery->nextVal('dtb_other_deliv_other_deliv_id');
            $objQuery->insert("dtb_other_deliv", $arrRegist);
            
        // 変更
        } else {
            $deliv_count = $objQuery->count("dtb_other_deliv","customer_id=? and other_deliv_id = ?" ,array($objCustomer->getValue('customer_id'), $_POST['other_deliv_id']));
            if ($deliv_count != 1) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", false, '一致する別のお届け先がありません。');
            }
            
            // 実行
            $objQuery->update("dtb_other_deliv", $arrRegist,
                                  "other_deliv_id = "
                                  . SC_Utils_Ex::sfQuoteSmart($array["other_deliv_id"]));
        }
    }

    //----　取得文字列の変換
    function lfConvertParam($array, $arrRegistColumn) {
        /*
         *  文字列の変換
         *  K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *  C :  「全角ひら仮名」を「全角かた仮名」に変換
         *  V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *  n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  全角英数字を半角英数字に変換する
         */
        // カラム名とコンバート情報
        foreach ($arrRegistColumn as $data) {
            $arrConvList[ $data["column"] ] = $data["convert"];
        }

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(strlen(($array[$key])) > 0) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }
        return $array;
    }
}
?>
