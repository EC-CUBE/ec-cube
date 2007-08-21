<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * SEO管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Seo extends LC_Page {

    // {{{ properties

    /** エラー情報 */
    var $arrErr;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/seo.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'seo';
        $this->tpl_mainno = 'basis';
        $this->tpl_subtitle = 'SEO管理';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
        $this->arrTAXRULE = $masterData->getMasterData("mtb_taxrule");
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $conn = new SC_DBConn();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objQuery = new SC_Query();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // データの取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $this->arrPageData = $objLayout->lfgetPageData(" edit_flg = 2 ");

        if (isset($_POST['page_id'])) $page_id = $_POST['page_id'];

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        if($_POST['mode'] == "confirm") {
            // エラーチェック
            $this->arrErr[$page_id] = $this->lfErrorCheck($arrPOST['meta'][$page_id]);

            // エラーがなければデータを更新
            if(count($this->arrErr[$page_id]) == 0) {

                // 更新データの変換
                $arrMETA = $this->lfConvertParam($_POST['meta'][$page_id]);

                // 更新データ配列生成
                $arrUpdData = array($arrMETA['author'], $arrMETA['description'], $arrMETA['keyword'], $page_id);
                // データ更新
                $this->lfUpdPageData($arrUpdData);
            }else{
                // POSTのデータを再表示
                $arrPageData = lfSetData($arrPageData, $arrPOST['meta']);
                $this->arrPageData = $arrPageData;
            }
        }

        $arrDisp_flg = array();
        // エラーがなければデータの取得
        if (isset($page_id) && isset($this->arrErr[$page_id])) {

            if(count($this->arrErr[$page_id]) == 0) {
                // データの取得
                $arrPageData = $objLayout->lfgetPageData(" edit_flg = 2 ");
                $this->arrPageData = $arrPageData;
            }

            // 表示･非表示切り替え
            foreach($arrPageData as $key => $val){
                $arrDisp_flg[$val['page_id']] = $_POST['disp_flg'.$val['page_id']];
            }
        }
        $this->disp_flg = $arrDisp_flg;

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

    /**
     * ページレイアウトテーブルにデータ更新を行う.
     *
     * @param array $arrUpdData 更新データ
     * @return integer 更新結果
     */
    function lfUpdPageData($arrUpdData = array()){
        $objQuery = new SC_Query();
        $sql = "";

        // SQL生成
        $sql .= " UPDATE ";
        $sql .= "     dtb_pagelayout ";
        $sql .= " SET ";
        $sql .= "     author = ? , ";
        $sql .= "     description = ? , ";
        $sql .= "     keyword = ? ";
        $sql .= " WHERE ";
        $sql .= "     page_id = ? ";
        $sql .= " ";

        // SQL実行
        $ret = $objQuery->query($sql, $arrUpdData);

        return $ret;
    }

    /**
     * 入力項目のエラーチェックを行う.
     *
     * @param array $array エラーチェック対象データ
     * @return array エラー内容
     */
    function lfErrorCheck($array) {
        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("メタタグ:Author", "author", STEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("メタタグ:Description", "description", STEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("メタタグ:Keywords", "keyword", STEXT_LEN), array("MAX_LENGTH_CHECK"));

        return $objErr->arrErr;
    }

    /**
     * テンプレート表示データに値をセットする.
     *
     * @param array 表示元データ
     * @param array 表示データ
     * @return array 表示データ
     */
    function lfSetData($arrPageData, $arrDispData){

        foreach($arrPageData as $key => $val){
            $page_id = $val['page_id'];
            $arrPageData[$key]['author'] = $arrDispData[$page_id]['author'];
            $arrPageData[$key]['description'] = $arrDispData[$page_id]['description'];
            $arrPageData[$key]['keyword'] = $arrDispData[$page_id]['keyword'];
        }

        return $arrPageData;
    }

    /* 取得文字列の変換 */
    function lfConvertParam($array) {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  全角英数字を半角英数字に変換する
         */
        // 人物基本情報

        // スポット商品
        $arrConvList['author'] = "KVa";
        $arrConvList['description'] = "KVa";
        $arrConvList['keyword'] = "KVa";

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($array[$key])) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }
        return $array;
    }
}
?>
