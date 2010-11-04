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
 * お届け先追加のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Shopping_DelivAddr extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->tpl_mainpage = 'shopping/deliv_addr.tpl';
        $this->tpl_title = "新しいお届け先の追加";
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $objView = new SC_MobileView(false);
        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();
        $objSiteSess = new SC_SiteSession();
        $objCartSess = new SC_CartSession();

        //ログイン判定
        if (!$objCustomer->isLoginSuccess(true)){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $this->arrForm = $_POST;

        //-- データ設定
        foreach($_POST as $key => $val) {
            if ($key != "mode" && $key != "return" && $key != "submit" && $key != session_name()) {
                $this->list_data[ $key ] = $val;
            }
        }
        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $uniqid = SC_Utils_Ex::sfCheckNormalAccess($objSiteSess, $objCartSess);

        //別のお届け先ＤＢ登録用カラム配列
        $arrRegistColumn = array(
                                 array(  "column" => "name01",		"convert" => "aKV" ),
                                 array(  "column" => "name02",		"convert" => "aKV" ),
                                 array(  "column" => "kana01",		"convert" => "CKV" ),
                                 array(  "column" => "kana02",		"convert" => "CKV" ),
                                 array(  "column" => "zip01",		"convert" => "n" ),
                                 array(  "column" => "zip02",		"convert" => "n" ),
                                 array(  "column" => "pref",		"convert" => "n" ),
                                 array(  "column" => "addr01",		"convert" => "aKV" ),
                                 array(  "column" => "addr02",		"convert" => "aKV" ),
                                 array(  "column" => "tel01",		"convert" => "n" ),
                                 array(  "column" => "tel02",		"convert" => "n" ),
                                 array(  "column" => "tel03",		"convert" => "n" ),
                                 );

        // 戻るボタン用処理
        if (!empty($_POST["return"])) {
            switch ($_POST["mode"]) {
            case 'complete':
                $_POST["mode"] = "set2";
                break;
            case 'set2':
                $_POST["mode"] = "set1";
                break;
            default:
                $this->sendRedirect($this->getLocation("./deliv.php"), true);
                exit;
            }
        }

        switch ($_POST['mode']){
        case 'set1':
            $this->arrErr = $this->lfErrorCheck1($this->arrForm);
            if (count($this->arrErr) == 0 && empty($_POST["return"])) {
                $this->tpl_mainpage = 'shopping/set1.tpl';

                $checkVal = array("pref", "addr01", "addr02", "addr03", "tel01", "tel02", "tel03");
                foreach($checkVal as $key) {
                    unset($this->list_data[$key]);
                }

                // 郵便番号から住所の取得
                if (@$this->arrForm['pref'] == "" && @$this->arrForm['addr01'] == "" && @$this->arrForm['addr02'] == "") {
                    $address = SC_Utils_Ex::sfGetAddress($_REQUEST['zip01'].$_REQUEST['zip02']);
                    $this->arrForm['pref'] = @$address[0]['state'];
                    $this->arrForm['addr01'] = @$address[0]['city'] . @$address[0]['town'];
                }
            } else {
                $checkVal = array("name01", "name02", "kana01", "kana02", "zip01", "zip02");
                foreach($checkVal as $key) {
                    unset($this->list_data[$key]);
                }
            }
            break;
        case 'set2':
            $this->arrErr = $this->lfErrorCheck2($this->arrForm);
            if (count($this->arrErr) == 0 && empty($_POST["return"])) {
                $this->tpl_mainpage = 'shopping/set2.tpl';
            } else {
                $this->tpl_mainpage = 'shopping/set1.tpl';

                $checkVal = array("pref", "addr01", "addr02", "addr03", "tel01", "tel02", "tel03");
                foreach($checkVal as $key) {
                    unset($this->list_data[$key]);
                }
            }
            break;
        case 'complete':
            $this->arrErr = $this->lfErrorCheck($this->arrForm);
            if (count($this->arrErr) == 0) {
                // 登録
                $other_deliv_id = $this->lfRegistData($_POST,$arrRegistColumn, $objCustomer);

                // 登録済みの別のお届け先を受注一時テーブルに書き込む
                $this->lfRegistOtherDelivData($uniqid, $objCustomer, $other_deliv_id);

                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // お支払い方法選択ページへ移動
                $this->sendRedirect($this->getLocation(MOBILE_URL_SHOP_PAYMENT), true);
                exit;
            } else {
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
            }
            break;
        default:
            $deliv_count = $objQuery->count("dtb_other_deliv", "customer_id=?", array($objCustomer->getValue('customer_id')));
            if ($deliv_count >= DELIV_ADDR_MAX){
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", false, "最大登録件数を超えています。");
            }
        }

        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
    }

    /* エラーチェック */
    function lfErrorCheck() {
        $objErr = new SC_CheckError();

        $objErr->doFunc(array("お名前(姓)", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(名)", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・姓)", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・名)", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("市区町村", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("番地", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("電話番号1", 'tel01'), array("EXIST_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("電話番号2", 'tel02'), array("EXIST_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("電話番号3", 'tel03'), array("EXIST_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("電話番号", "tel01", "tel02", "tel03") ,array("TEL_CHECK"));
        return $objErr->arrErr;

    }

    /* エラーチェック */
    function lfErrorCheck1() {
        $objErr = new SC_CheckError();

        $objErr->doFunc(array("お名前(姓)", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(名)", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・姓)", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・名)", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        return $objErr->arrErr;

    }

    /* エラーチェック */
    function lfErrorCheck2() {
        $objErr = new SC_CheckError();

        $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("市区町村", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("番地", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
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

        //-- 編集登録実行
        $objQuery->begin();
        if ($array['other_deliv_id'] != ""){
            $objQuery->update("dtb_other_deliv", $arrRegist, "other_deliv_id="  . SC_Utils_Ex::sfQuoteSmart($array["other_deliv_id"]));
        }else{
            $arrRegist['other_deliv_id'] = $objQuery->nextVal('dtb_other_deliv_other_deliv_id');
            $objQuery->insert("dtb_other_deliv", $arrRegist);
            $array['other_deliv_id'] = $arrRegist['other_deliv_id'];
        }

        $objQuery->commit();

        return $array['other_deliv_id'];
    }

    //----　取得文字列の変換
    function lfConvertParam($array, $arrRegistColumn) {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
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

    /* 別のお届け先住所を一時受注テーブルへ */
    function lfRegistOtherDelivData($uniqid, $objCustomer, $other_deliv_id) {
        // 登録データの作成
        $sqlval['order_temp_id'] = $uniqid;
        $sqlval['update_date'] = 'Now()';
        $sqlval['customer_id'] = $objCustomer->getValue('customer_id');
        $sqlval['order_birth'] = $objCustomer->getValue('birth');

        $objQuery = new SC_Query();
        $where = "other_deliv_id = ?";
        $arrRet = $objQuery->select("*", "dtb_other_deliv", $where, array($other_deliv_id));

        $sqlval['deliv_check'] = '1';
        $sqlval['deliv_name01'] = $arrRet[0]['name01'];
        $sqlval['deliv_name02'] = $arrRet[0]['name02'];
        $sqlval['deliv_kana01'] = $arrRet[0]['kana01'];
        $sqlval['deliv_kana02'] = $arrRet[0]['kana02'];
        $sqlval['deliv_zip01'] = $arrRet[0]['zip01'];
        $sqlval['deliv_zip02'] = $arrRet[0]['zip02'];
        $sqlval['deliv_pref'] = $arrRet[0]['pref'];
        $sqlval['deliv_addr01'] = $arrRet[0]['addr01'];
        $sqlval['deliv_addr02'] = $arrRet[0]['addr02'];
        $sqlval['deliv_tel01'] = $arrRet[0]['tel01'];
        $sqlval['deliv_tel02'] = $arrRet[0]['tel02'];
        $sqlval['deliv_tel03'] = $arrRet[0]['tel03'];
        $objDb = new SC_Helper_DB_Ex();
        $objDb->sfRegistTempOrder($uniqid, $sqlval);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
?>
