<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * おすすめ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_Recommend extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/recomend.tpl';
        $this->tpl_mainno = 'contents';
        $this->tpl_subnavi = 'contents/subnavi.tpl';
        $this->tpl_subno = "recommend";
        $this->tpl_subtitle = 'オススメ管理';
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

        $arrRegistColumn = array(
                                 array(  "column" => "product_id", "convert" => "n" ),
                                 array(  "column" => "category_id", "convert" => "n" ),
                                 array(  "column" => "rank", "convert" => "n" ),
                                 array(  "column" => "title", "convert" => "aKV" ),
                                 array(  "column" => "comment", "convert" => "aKV" ),
                                 );

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        //最大登録数の表示
        $this->tpl_disp_max = RECOMMEND_NUM;

        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        if (!isset($_POST['category_id'])) $_POST['category_id'] = "";

        // 登録時
        if ( $_POST['mode'] == 'regist' ){

            // 入力文字の強制変換
            $this->arrForm = $_POST;
            $this->arrForm = lfConvertParam($this->arrForm, $arrRegistColumn);
            // エラーチェック
            $this->arrErr[$this->arrForm['rank']] = lfErrorCheck();
            if ( ! $this->arrErr[$this->arrForm['rank']]) {
                // 古いのを消す
                $sql = "DELETE FROM dtb_best_products WHERE category_id = ? AND rank = ?";
                $conn->query($sql, array($this->arrForm['category_id'] ,$this->arrForm['rank']));

                // ＤＢ登録
                $this->arrForm['creator_id'] = $_SESSION['member_id'];
                $this->arrForm['update_date'] = "NOW()";
                $this->arrForm['create_date'] = "NOW()";

                $objQuery = new SC_Query();
                $objQuery->insert("dtb_best_products", $this->arrForm );
                //		$conn->autoExecute("dtb_best_products", $this->arrForm );
            }

        } elseif ( $_POST['mode'] == 'delete' ){
            // 削除時

            $sql = "DELETE FROM dtb_best_products WHERE category_id = ? AND rank = ?";
            $conn->query($sql, array($_POST['category_id'] ,$_POST['rank']));

        }

        // カテゴリID取得 無いときはトップページ
        if ( SC_Utils_Ex::sfCheckNumLength($_POST['category_id']) ){
            $this->category_id = $_POST['category_id'];
        } else {
            $this->category_id = 0;
        }

        // 既に登録されている内容を取得する
        $sql = "SELECT B.name, B.main_list_image, A.* FROM dtb_best_products as A INNER JOIN dtb_products as B USING (product_id)
		 WHERE A.del_flg = 0 ORDER BY rank";
        $arrItems = $conn->getAll($sql);
        foreach( $arrItems as $data ){
            $this->arrItems[$data['rank']] = $data;
        }

        // 商品変更時は、選択された商品に一時的に置き換える
        if ( $_POST['mode'] == 'set_item'){
            $sql = "SELECT product_id, name, main_list_image FROM dtb_products WHERE product_id = ? AND del_flg = 0";
            $result = $conn->getAll($sql, array($_POST['product_id']));
            if ( $result ){
                $data = $result[0];
                foreach( $data as $key=>$val){
                    $this->arrItems[$_POST['rank']][$key] = $val;
                }
                $this->arrItems[$_POST['rank']]['rank'] = $_POST['rank'];
            }
            $this->checkRank = $_POST['rank'];
        }

        //各ページ共通
        $this->cnt_question = 6;
        $this->arrActive = isset($arrActive) ? $arrActive : "";;
        $this->arrQuestion = isset($arrQuestion) ? $arrQuestion : "";

        // カテゴリ取得
        $objDb = new SC_Helper_DB_Ex();
        $this->arrCatList = $objDb->sfGetCategoryList("level = 1");

        //----　ページ表示
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

    //----　取得文字列の変換
    function lfConvertParam($array, $arrRegistColumn) {

        // カラム名とコンバート情報
        foreach ($arrRegistColumn as $data) {
            $arrConvList[ $data["column"] ] = $data["convert"];
        }
        // 文字変換
        $new_array = array();
        foreach ($arrConvList as $key => $val) {
            $new_array[$key] = $array[$key];
            if( strlen($val) > 0) {
                $new_array[$key] = mb_convert_kana($new_array[$key] ,$val);
            }
        }
        return $new_array;

    }

    /* 入力エラーチェック */
    function lfErrorCheck() {
        $objQuery = new SC_Query;
        $objErr = new SC_CheckError();

        $objErr->doFunc(array("見出しコメント", "title", STEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("オススメコメント", "comment", LTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));

        return $objErr->arrErr;
    }

}
?>
