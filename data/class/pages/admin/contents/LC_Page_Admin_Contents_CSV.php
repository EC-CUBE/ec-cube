<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");
require_once(CLASS_PATH . "helper_extends/SC_Helper_CSV_Ex.php");

/**
 * CSV項目設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_CSV extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/csv.tpl';
        $this->tpl_subnavi = 'contents/subnavi.tpl';
        $this->tpl_subno = 'csv';

        $this->tpl_mainno = "contents";
        $this->tpl_subtitle = 'CSV出力設定';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objCSV = new SC_Helper_CSV_Ex();

        $this->arrSubnavi = $objCSV->arrSubnavi;
        $this->tpl_subno_csv = $objCSV->arrSubnavi[1];
        $this->arrSubnaviName = $objCSV->arrSubnaviName;

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        $arrOutput = array();
        $arrChoice = array();

        $get_tpl_subno_csv = isset($_GET['tpl_subno_csv'])
                                     ? $_GET['tpl_subno_csv'] : "";

        // GETで値が送られている場合にはその値を元に画面表示を切り替える
        if ($get_tpl_subno_csv != ""){
            // 送られてきた値が配列に登録されていなければTOPを表示
            if (in_array($get_tpl_subno_csv,$this->arrSubnavi)){
                $subno_csv = $get_tpl_subno_csv;
            }else{
                $subno_csv = $this->arrSubnavi[1];
            }
        } else {
            // GETで値がなければPOSTの値を使用する
            if (isset($_POST['tpl_subno_csv'])
                && $_POST['tpl_subno_csv'] != "") {

                $subno_csv = $_POST['tpl_subno_csv'];
            }else{
                $subno_csv = $this->arrSubnavi[1];
            }
        }

        // subnoの番号を取得
        $subno_id = array_keys($this->arrSubnavi,$subno_csv);
        $subno_id = $subno_id[0];
        // データの登録

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        if ($_POST["mode"] == "confirm") {

            // エラーチェック
            $this->arrErr = $this->lfCheckError($_POST['output_list']);

            if (count($this->arrErr) <= 0){
                // データの更新
                $this->lfUpdCsvOutput($subno_id, $_POST['output_list']);

                // 画面のリロード
                $this->reload(array("tpl_subno_csv" => $subno_csv));
            }
        }

        // 出力項目の取得
        $arrOutput = SC_Utils_Ex::sfSwapArray($objCSV->sfgetCsvOutput($subno_csv, "WHERE csv_id = ? AND status = 1", array($subno_id)));
        $arrOutput = SC_Utils_Ex::sfarrCombine($arrOutput['col'], $arrOutput['disp_name']);

        // 非出力項目の取得
        $arrChoice = SC_Utils_Ex::sfSwapArray($objCSV->sfgetCsvOutput($subno_csv, "WHERE csv_id = ? AND status = 2", array($subno_id)));

        if (!isset($arrChoice['col'])) $arrChoice['col'] = array();
        if (!isset($arrChoice['disp_name'])) $arrChoice['disp_name'] = array();

        $arrChoice = SC_Utils_Ex::sfarrCombine($arrChoice['col'], $arrChoice['disp_name']);

        $this->arrOutput=$arrOutput;
        $this->arrChoice=$arrChoice;


        $this->SubnaviName = $this->arrSubnaviName[$subno_id];
        $this->tpl_subno_csv = $subno_csv;

        // 画面の表示
        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
    }

   /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    function lfUpdCsvOutput($csv_id, $arrData = array()){
        $objQuery = new SC_Query();

        // ひとまず、全部使用しないで更新する
        $upd_sql = "UPDATE dtb_csv SET status = 2, rank = NULL, update_date = now() WHERE csv_id = ?";
        $objQuery->query($upd_sql, array($csv_id));

        // 使用するものだけ、再更新する。
        if (is_array($arrData)) {
            foreach($arrData as $key => $val){
                $upd_sql = "UPDATE dtb_csv SET status = 1, rank = ? WHERE csv_id = ? AND col = ? ";
                $objQuery->query($upd_sql, array($key+1, $csv_id,$val));
            }
        }
    }

    Function Lfcheckerror($data){
        $objErr = new SC_CheckError();
        $objErr->doFunc( array("出力項目", "output_list"), array("EXIST_CHECK") );

        return $objErr->arrErr;

    }
}
?>
