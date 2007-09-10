<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * アンケート管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Contents_Inquiry extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/inquiry.tpl';
        $this->tpl_mainno = 'contents';
        $this->tpl_subnavi = 'contents/subnavi.tpl';
        $this->tpl_subno = "inquiry";
        $this->tpl_subtitle = 'アンケート管理';
        $this->arrCVSCOL = array(

                );

        $this->arrCVSTITLE = array(
                             '回答ID',
                             '質問ID',
                             '回答日時',
                             '回答名',
                             '顧客名1',
                             '顧客名2',
                             '顧客名カナ1',
                             '顧客名カナ2',
                             '郵便番号1',
                             '郵便番号2',
                             '都道府県',
                             '住所1',
                             '住所2',
                             '電話番号1',
                             '電話番号2',
                             '電話番号3',
                             'メールアドレス',
                             '回答1',
                             '回答2',
                             '回答3',
                             '回答4',
                             '回答5',
                             '回答6'
                             );
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

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        $arrActive = array( "0"=>"稼働", "1"=>"非稼働" );
        $arrQuestion = array( "0"=>"使用しない", "1"=>"テキストエリア", "2"=>"テキストボックス"
                              , "3"=>"チェックボックス", "4"=>"ラジオボタン"
                              );

        $sql = "SELECT *, cast(substring(create_date, 1, 10) as date) as disp_date FROM dtb_question WHERE del_flg = 0 ORDER BY question_id";
        $result = $conn->getAll($sql);
        $this->list_data = $result;

        if (!isset($_GET['mode'])) $_GET['mode'] = "";

        if ( $_GET['mode'] == 'regist' ){

            for ( $i=0; $i<count($_POST["question"]); $i++ ) {
                $_POST['question'][$i]['name'] = mb_convert_kana( trim ( $_POST['question'][$i]['name'] ), "K" );
                for ( $j=0; $j<count( $_POST['question'][$i]['option'] ); $j++ ){
                    $_POST['question'][$i]['option'][$j] = mb_convert_kana( trim ( $_POST['question'][$i]['option'][$j] ) );
                }
            }

            $error = $this->lfErrCheck();

            if ( ! $error  ){

                if ( ! is_numeric($_POST['question_id']) ){
                    $objQuery = new SC_Query();

                    //登録
                    $value = serialize($_POST);
                    if (DB_TYPE == "pgsql") {
                        $question_id = $objQuery->nextval('dtb_question', 'question_id');
                    }

                    $sql_val = array( $value, $_POST['title'] ,$question_id );
                    $conn->query("INSERT INTO dtb_question ( question, question_name, question_id, create_date) VALUES (?, ?, ?, now())", $sql_val );
                    $this->MESSAGE = "登録が完了しました";

                    if (DB_TYPE == "mysql") {
                        $question_id = $objQuery->nextval('dtb_question', 'question_id');
                    }

                    $this->QUESTION_ID = $question_id;
                    $this->reload();
                } else {
                    //編集
                    $value = serialize($_POST);
                    $sql_val = array( $value, $_POST['title'] ,$_POST['question_id'] );
                    $conn->query("UPDATE dtb_question SET question = ?, question_name = ? WHERE question_id = ?", $sql_val );
                    $this->MESSAGE = "編集が完了しました";
                    $this->QUESTION_ID = $_POST['question_id'];
                    $this->reload();
                }
            } else {

                //エラー表示
                $this->ERROR = $error;
                $this->QUESTION_ID = $_REQUEST['question_id'];
                $this->ERROR_COLOR = $this->lfGetErrColor($error, ERR_COLOR);

            }
        } elseif ( ( $_GET['mode'] == 'delete' ) && ( SC_Utils_Ex::sfCheckNumLength($_GET['question_id']) )  ){

            $sql = "UPDATE dtb_question SET del_flg = 1 WHERE question_id = ?";
            $conn->query( $sql, array( $_GET['question_id'] ) );
            $this->reload();

        } elseif ( ( $_GET['mode'] == 'csv' ) && ( SC_Utils_Ex::sfCheckNumLength($_GET['question_id']) ) ){
            require_once(CLASS_PATH . "helper_extends/SC_Helper_CSV_Ex.php");

            $objCSV = new SC_Helper_CSV_Ex();
            $head = SC_Utils_Ex::sfGetCSVList($this->arrCVSTITLE);
            $list_data = $conn->getAll("SELECT result_id,question_id,question_date,question_name,name01,name02,kana01,kana02,zip01,zip02,pref,addr01,addr02,tel01,tel02,tel03,mail01,question01,question02,question03,question04,question05,question06 FROM dtb_question_result WHERE del_flg = 0 AND question_id = ? ORDER BY result_id ASC",array($_GET['question_id']));
            $data = "";
            for($i = 0; $i < count($list_data); $i++) {
                // 各項目をCSV出力用に変換する。
                $data .= $objCSV->lfMakeCSV($list_data[$i]);
            }
            // CSVを送信する
            SC_Utils_Ex::sfCSVDownload($head.$data);
            exit;

        } else {
            if (!isset($_GET['question_id'])) $_GET['question_id'] = "";

            if ( is_numeric($_GET['question_id']) ){

                $sql = "SELECT question FROM dtb_question WHERE question_id = ?";
                $result = $conn->getOne($sql, array($_GET['question_id']));

                if ( $result ){
                    $_POST = unserialize( $result );
                    $this->QUESTION_ID = $_GET['question_id'];
                }
            }
        }

        //各ページ共通
        $this->cnt_question = 6;
        $this->arrActive = $arrActive;
        $this->arrQuestion = $arrQuestion;

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

    // ------------  エラーチェック処理部 ------------

    function lfGetErrColor( $arr, $err_color ){

        foreach ( $arr as $key=>$val ) {
            if ( is_string($val) && strlen($val) > 0 ){
                $return[$key] = $err_color;
            } elseif ( is_array( $val ) ) {
                $return[$key] = $this->lfGetErrColor ( $val, $err_color);
            }
        }
        return $return;
    }


    // ------------  エラーチェック処理部 ------------

    function lfErrCheck (){

        $objErr = new SC_CheckError();
        $errMsg = "";

        $objErr->doFunc( array( "稼働・非稼働", "active" ), array( "SELECT_CHECK" ) );

        $_POST["title"] = mb_convert_kana( trim (  $_POST["title"] ), "K" );
        $objErr->doFunc( array( "アンケート名", "title" ), array( "EXIST_CHECK" ) );

        $_POST["contents"] = mb_convert_kana( trim (  $_POST["contents"] ), "K" );
        $objErr->doFunc( array( "アンケート内容" ,"contents", "3000" ), array( "EXIST_CHECK", "MAX_LENGTH_CHECK" ) );


        if ( ! $_POST['question'][0]["name"] ){
            $objErr->arrErr['question'][0]["name"] = "１つめの質問名が入力されていません";
        }

        //　チェックボックス、ラジオボタンを選択した場合は最低1つ以上項目を記入させる。
        for( $i = 0; $i < count( $_POST["question"] ); $i++ ) {

            if ( $_POST["question"][$i]["kind"] ) {
                if (strlen($_POST["question"][$i]["name"]) == 0) {
                    $objErr->arrErr["question"][$i]["name"] = "タイトルを入力して下さい。";
                } else if ( strlen($_POST["question"][$i]["name"]) > STEXT_LEN ) {
                    $objErr->arrErr["question"][$i]["name"] = "タイトルは". STEXT_LEN  ."字以内で入力して下さい。";
                }
            }

            if( $_POST["question"][$i]["kind"] == 3 || $_POST["question"][$i]["kind"] == 4  ) {

                $temp_data = array();
                for( $j = 0; $j < count( $_POST["question"][$i]["option"] ); $j++ ) {

                    // 項目間（テキストボックス）があいていたら詰めていく
                    if( strlen( $_POST["question"][$i]["option"][$j] ) > 0 ) $temp_data[] = mb_convert_kana( trim ( $_POST["question"][$i]["option"][$j]  ), "asKVn" );

                }

                $_POST["question"][$i]["option"] = $temp_data;

                if( ( strlen( $_POST["question"][$i] ["option"][0] ) == 0 ) || ( strlen( $_POST["question"][$i] ["option"][0] ) > 0
                                                                                 && strlen( $_POST["question"][$i] ["option"][1] ) == 0 ) ) $objErr->arrErr["question"][$i]['kind'] = "下記の2つ以上の項目に記入してください。";
            }
        }

        return $this->lfGetArrInput( $objErr->arrErr );

    }


    function lfGetArrInput( $arr ){
        // 値が入力された配列のみを返す

        if ( is_array($arr) ){
            foreach ( $arr as $key=>$val ) {
                if ( is_string($val) && strlen($val) > 0 ){
                    $return[$key] = $val;
                } elseif ( is_array( $val ) ) {
                    $data = $this->lfGetArrInput ( $val );
                    if ( $data ){
                        $return[$key] = $data;
                    }
                }
            }
        }
        return $return;
    }
}
?>
