<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * XXX のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Inquiry extends LC_Page {

    // {{{ properties

    // TODO
    var $errmsg;
    var $arrPref;

    var $QUESTION;
    var $question_id;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'inquiry/index.tpl';
        $this->tpl_mainno = 'contents';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $conn = new SC_DBConn();
        $objPage = new LC_Page();
        $objView = new SC_SiteView();
        $objSess = new SC_Session();


        // 都道府県プルダウン用配列
        $objPage->arrPref = $arrPref;

        // CSV保存項目
        //---- 登録用カラム配列 オプション以外
        $arrRegistColumn = array(
                                     array(  "column" => "name01", "convert" => "aKV" ),
                                     array(  "column" => "name02", "convert" => "aKV" ),
                                     array(  "column" => "kana01", "convert" => "CKV" ),
                                     array(  "column" => "kana02", "convert" => "CKV" ),
                                     array(  "column" => "zip01", "convert" => "n" ),
                                     array(  "column" => "zip02", "convert" => "n" ),
                                     array(  "column" => "pref", "convert" => "n" ),
                                     array(  "column" => "addr01", "convert" => "aKV" ),
                                     array(  "column" => "addr02", "convert" => "aKV" ),
                                     array(  "column" => "email", "convert" => "a" ),
                                     array(  "column" => "email02", "convert" => "a" ),
                                     array(  "column" => "tel01", "convert" => "n" ),
                                     array(  "column" => "tel02", "convert" => "n" ),
                                     array(  "column" => "tel03", "convert" => "n" ),
                            );



        if ( ( ! $_POST['mode'] == 'confirm' ) && ( ! is_numeric($_REQUEST['question_id']) ) ){
            echo "不正アクセス";
            exit;
        }

        // テンプレート登録項目取得
        $sql = "SELECT question_id, question FROM dtb_question WHERE question_id = ?";
        $result = $conn->getAll( $sql, array($_REQUEST['question_id']) );
        $objPage->QUESTION = lfGetArrInput( unserialize( $result[0]['question'] ) );

        $objPage->question_id = $_REQUEST['question_id'];

        $objPage->arrHidden = sfMakeHiddenArray($_POST);
        unset($objPage->arrHidden['mode']);

        if ( (int)$objPage->QUESTION["delete"] !== 0 ){

            $objPage->tpl_mainpage = "inquiry/closed.tpl";

        } elseif( $_POST['mode'] == "confirm" ) {

            //--　入力エラーチェック
            $objPage->arrForm = $_POST;
            $objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
            $objPage->arrErr = lfErrorCheck($objPage->arrForm);
            $objPage->arrErr = lfGetArrInput($objPage->arrErr);

            if( ! $objPage->arrErr ) {
                $objPage->tpl_mainpage = "inquiry/confirm.tpl";
            }


        }elseif( $_POST['mode'] == "return"){
            $objPage->arrForm = $_POST;

        }elseif( $_POST['mode'] == "regist" )  {

            //--　入力文字・変換＆エラーチェック
            $objPage->arrForm = $_POST;
            $objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
            $objPage->arrErr = lfErrorCheck($objPage->arrForm);
            $objPage->arrErr = lfGetArrInput($objPage->arrErr);


            if( ! $objPage->arrErr ) {

                //完了画面
                $objPage->tpl_mainpage = "inquiry/complete.tpl";


                //--------- ▼ SQL ---------//

                    // テーブルに入れるように整形する
                    $arrOption = $objPage->arrForm['option'];
                    unset ($objPage->arrForm['email02']);
                    $objPage->arrForm['mail01'] = $objPage->arrForm['email'];
                    unset ($objPage->arrForm['email']);
                    unset ($objPage->arrForm['option']);
                    $objPage->arrForm['question_id'] = $objPage->question_id;
                    $objPage->arrForm['question_name'] = $objPage->QUESTION['title'];
                    for ( $i=0; $i<(count($arrOption)); $i++ ){
                        $tmp = "";
                        if ( is_array($arrOption[$i]) ){
                            for( $j=0; $j<count($arrOption[$i]); $j++){
                                if ( $j>0 ) $tmp .= ",";
                                $tmp .= $arrOption[$i][$j];
                            }
                            $objPage->arrForm['question0'.($i+1)] = $tmp;
                        } else {
                            $objPage->arrForm['question0'.($i+1)] = $arrOption[$i];
                        }
                    }
                    $objPage->arrForm['create_date'] = "now()";
                    // ＤＢ登録
                    $objQuery = new SC_Query();
                    $objQuery->insert("dtb_question_result", $objPage->arrForm );

                //--------- ▲ SQL ---------//

            }
        }

        $objPage->cnt_question = 6;
        $objPage->arrActive = $arrActive;
        $objPage->arrQuestion = $arrQuestion;


        //----　ページ表示
        $objView->_smarty->register_function("lfArray_Search_key_Smarty","lfArray_Search_key_Smarty");
        $objView->assignobj($objPage);
        $objView->display($objPage->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // }}}
    // {{{ protected functions

    /**
     * エラーチェック
     *
     * @param array FormParam の配列
     * @return array エラー情報の配列
     **/
    function lfErrorCheck($array) {

        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("フリガナ(セイ）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("フリガナ（メイ）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("ご住所1", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("ご住所2", "addr02", MTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お電話番号1", 'tel01'), array("EXIST_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("お電話番号2", 'tel02'), array("EXIST_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("お電話番号3", 'tel03'), array("EXIST_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("EXIST_CHECK", "SPTAB_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス(確認)', "email02", MTEXT_LEN) ,array("EXIST_CHECK", "SPTAB_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', "email", "email02") ,array("EQUAL_CHECK"));

        $objErr->arrErr["option"] =  array_map( "lfCheckNull", (array)$_POST['option'] );

        return $objErr->arrErr;
    }

    /**
     * 取得文字列の変換
     *
     * @param array TODO
     * @param array TODO
     * @return array 変換後の文字列
     **/
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

        // オプション配列用
        for ($i=0; $i<count($array['option']); $i++){
            if ( is_array($array['option'][$i]) ){
                $new_array['option'][$i] = $array['option'][$i];
            } else {
                $new_array['option'][$i] = mb_convert_kana($array['option'][$i] ,"aKV");
            }
        }

        return $new_array;
    }

    /**
     * 値が入力された配列のみを返す.
     * TODO 要リファクタリング
     *
     * @param array $arr TODO
     * @return array 値が入力された配列
     */
    function lfGetArrInput( $arr ){
        // 値が入力された配列のみを返す

        if ( is_array($arr)	){
            foreach ( $arr as $key=>$val ) {
                if ( is_string($val) && strlen($val) > 0 ){
                    $return[$key] = $val;
                } elseif ( is_array( $val ) ) {
                    $data = lfGetArrInput ( $val );
                    if ( $data ){
                        $return[$key] = $data;
                    }
                }
            }
        }
        return $return;
    }

    /**
     * TODO
     *
     * @param unknown_type $palams
     * @return unknown
     */
    function lfArray_Search_key_Smarty ( $palams ){

        $val = $palams['val'];
        $arr = $palams['arr'];

        $revers_arr = array_flip($arr);
        return array_search( $val ,$revers_arr );


    }

    /**
     * TODO
     *
     * @param unknown_type $val
     * @return unknown
     */
    function lfCheckNull ( $val ){

        if ( ( ! is_array( $val ) ) && ( strlen( $val ) < 1 ) ){
            $return = "1";
        } elseif ( is_array( $val ) ) {
            foreach ($val as $line) {
                $return = lfCheckNull( $line );
            }
        }
        return $return;
    }
}
?>
