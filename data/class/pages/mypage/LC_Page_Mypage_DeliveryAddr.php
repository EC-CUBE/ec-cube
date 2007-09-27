<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
        $this->tpl_title = "新しいお届け先の追加･変更";
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref= $masterData->getMasterData("mtb_pref",
                            array("pref_id", "pref_name", "rank"));
        $this->allowClientCache();
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
            $ParentPage = $_GET['page'];
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


        switch ($_POST['mode']){
        case 'edit':
            $_POST = $this->lfConvertParam($_POST,$arrRegistColumn);
            $this->arrErr = $this->lfErrorCheck($_POST);
            if ($this->arrErr){
                foreach ($_POST as $key => $val){
                    $this->$key = $val;
                }
            }else{
                //別のお届け先登録数の取得
                $deliv_count = $objQuery->count("dtb_other_deliv", "customer_id=?", array($objCustomer->getValue('customer_id')));
                if ($deliv_count < DELIV_ADDR_MAX or isset($_POST['other_deliv_id'])){
                    $this->lfRegistData($_POST,$arrRegistColumn, $objCustomer);
                }
                $this->tpl_onload = "fnUpdateParent('". $this->getLocation($_POST['ParentPage']) ."'); window.close();";
            }
            break;
        }

        if ($_GET['other_deliv_id'] != ""){
            //別のお届け先情報取得
            $arrOtherDeliv = $objQuery->select("*", "dtb_other_deliv", "other_deliv_id=? ", array($_SESSION['other_deliv_id']));
            $this->arrOtherDeliv = $arrOtherDeliv[0];
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
        $objErr->doFunc(array("ご住所（1）", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("ご住所（2）", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お電話番号1", 'tel01'), array("EXIST_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("お電話番号2", 'tel02'), array("EXIST_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("お電話番号3", 'tel03'), array("EXIST_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03", TEL_LEN) ,array("TEL_CHECK"));
        return $objErr->arrErr;

    }

    /* 登録実行 */
    function lfRegistData($array, $arrRegistColumn, &$objCustomer) {
        $objConn = new SC_DBConn();
        foreach ($arrRegistColumn as $data) {
            if (strlen($array[ $data["column"] ]) > 0) {
                $arrRegist[ $data["column"] ] = $array[ $data["column"] ];
            }
        }

        $arrRegist['customer_id'] = $objCustomer->getvalue('customer_id');

        //-- 編集登録実行
        $objConn->query("BEGIN");
        if ($array['other_deliv_id'] != ""){
            $objConn->autoExecute("dtb_other_deliv", $arrRegist,
                                  "other_deliv_id = "
                                  . SC_Utils_Ex::sfQuoteSmart($array["other_deliv_id"]));
        }else{
            $objConn->autoExecute("dtb_other_deliv", $arrRegist);
        }
        $objConn->query("COMMIT");
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
}
?>
