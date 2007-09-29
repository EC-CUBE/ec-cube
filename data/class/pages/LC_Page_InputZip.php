<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 郵便番号入力 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_InputZip extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_message = "住所を検索しています。";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $conn = new SC_DBconn(ZIP_DSN);
        $objView = new SC_SiteView(false);

        // 入力エラーチェック
        $arrErr = $this->fnErrorCheck();

        // 入力エラーの場合は終了
        if(count($arrErr) > 0) {
            $this->tpl_start = "window.close();";
        }

        // 郵便番号検索文作成
        $zipcode = $_GET['zip1'].$_GET['zip2'];
        $zipcode = mb_convert_kana($zipcode ,"n");
        $sqlse = "SELECT state, city, town FROM mtb_zip WHERE zipcode = ?";

        $data_list = $conn->getAll($sqlse, array($zipcode));
        if (empty($data_list)) $data_list = "";

        $masterData = new SC_DB_MasterData_Ex();
        $arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
        // インデックスと値を反転させる。
        $arrREV_PREF = array_flip($arrPref);

        $this->tpl_state = isset($arrREV_PREF[$data_list[0]['state']])
            ? $arrREV_PREF[$data_list[0]['state']] : "";
        $this->tpl_city = isset($data_list[0]['city']) ? $data_list[0]['city'] : "";
        $town =  isset($data_list[0]['town']) ? $data_list[0]['town'] : "";
        /*
         総務省からダウンロードしたデータをそのままインポートすると
         以下のような文字列が入っているので	対策する。
         ・（１~１９丁目）
         ・以下に掲載がない場合
        */
        $town = ereg_replace("（.*）$","",$town);
        $town = ereg_replace("以下に掲載がない場合","",$town);
        $this->tpl_town = $town;

        // 郵便番号が発見された場合
        if(!empty($data_list)) {
            $func = "fnPutAddress('" . $_GET['input1'] . "','" . $_GET['input2']. "');";
            $this->tpl_onload = "$func";
            $this->tpl_start = "window.close();";
        } else {
            $this->tpl_message = "該当する住所が見つかりませんでした。";
        }

        /* ページの表示 */
        $objView->assignobj($this);
        $objView->display("input_zip.tpl");
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }


    /* 入力エラーのチェック */
    function fnErrorCheck() {
        // エラーメッセージ配列の初期化
        $objErr = new SC_CheckError();

        // 郵便番号
        $objErr->doFunc( array("郵便番号1",'zip1',ZIP01_LEN ) ,array( "NUM_COUNT_CHECK" ) );
        $objErr->doFunc( array("郵便番号2",'zip2',ZIP02_LEN ) ,array( "NUM_COUNT_CHECK" ) );

        return $objErr->arrErr;
    }


}
?>
