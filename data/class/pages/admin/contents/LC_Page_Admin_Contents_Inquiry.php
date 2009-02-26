<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
                             'アンケートタイトル',
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
        $objQuery = new SC_Query();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        $arrActive = array( "0"=>"稼働", "1"=>"非稼働" );
        $arrQuestion = array( "0"=>"使用しない", "1"=>"テキストエリア", "2"=>"テキストボックス"
                              , "3"=>"チェックボックス", "4"=>"ラジオボタン"
                              );

        $result = $objQuery->select('*, cast(create_date as date) as disp_date', 'dtb_question', 'del_flg = 0 ORDER BY question_id');
        $this->list_data = $result;

        if (!isset($_GET['mode'])) $_GET['mode'] = "";

        // アンケートを作成ボタン押下時
        if ( $_GET['mode'] == 'regist' ){

            for ( $i=0; $i<count($_POST["question"]); $i++ ) {
                $_POST['question'][$i]['name'] = mb_convert_kana( trim ( $_POST['question'][$i]['name'] ), "K" );
                for ( $j=0; $j<count( $_POST['question'][$i]['option'] ); $j++ ){
                    $_POST['question'][$i]['option'][$j] = mb_convert_kana( trim ( $_POST['question'][$i]['option'][$j] ) );
                }
            }

            $error = $this->lfErrCheck();

            if ( ! $error  ){
                // 新規登録
                if ( ! is_numeric($_POST['question_id']) ){

                    //登録
                    $value = serialize($_POST);
                    if (DB_TYPE == "pgsql") {
                        $question_id = $objQuery->nextval('dtb_question', 'question_id');
                    }

                    $sql_val = array( 'question' => $value, 'question_name' => $_POST['title'] ,'question_id' => $question_id ,'create_date' => 'now()');
                    $objQuery->insert('dtb_question', $sql_val);
                    $this->MESSAGE = "登録が完了しました";

                    if (DB_TYPE == "mysql") {
                        $question_id = $objQuery->nextval('dtb_question', 'question_id');
                    }

                    $this->QUESTION_ID = $question_id;
                    $this->reload(null, true);

                // 編集
                } else {
                    //編集
                    $value = serialize($_POST);
                    $sql_val = array( 'question'=>$value, 'question_name'=>$_POST['title'] );
                    $objQuery->update('dtb_question', $sql_val, 'question_id = ?',  array($_POST['question_id']) );
                    $this->MESSAGE = "編集が完了しました";
                    $this->QUESTION_ID = $_POST['question_id'];
                    $this->reload(null, true);
                }
            } else {

                //エラー表示
                $this->ERROR = $error;
                $this->QUESTION_ID = $_REQUEST['question_id'];
                $this->ERROR_COLOR = $this->lfGetErrColor($error, ERR_COLOR);
            }

        // 削除ボタン押下時
        } elseif ( ( $_GET['mode'] == 'delete' ) && ( SC_Utils_Ex::sfCheckNumLength($_GET['question_id']) )  ){

            $sqlval = array('del_flg' => 1);
            $objQuery->update('dtb_question', $sqlval, 'question_id = ?', array( $_GET['question_id'] ) );
            $this->reload(null, true);

        // CSVダウンロードボタン押下時
        } elseif ( ( $_GET['mode'] == 'csv' ) && ( SC_Utils_Ex::sfCheckNumLength($_GET['question_id']) ) ){
            require_once(CLASS_EX_PATH . "helper_extends/SC_Helper_CSV_Ex.php");

            $objCSV = new SC_Helper_CSV_Ex();
            $head = SC_Utils_Ex::sfGetCSVList($this->arrCVSTITLE);
            $sql =<<<__EOS__
                    SELECT
                         dtb_question_result.result_id
                        ,dtb_question_result.question_id
                        ,dtb_question_result.create_date
                        ,dtb_question.question_name
                        ,dtb_question_result.name01
                        ,dtb_question_result.name02
                        ,dtb_question_result.kana01
                        ,dtb_question_result.kana02
                        ,dtb_question_result.zip01
                        ,dtb_question_result.zip02
                        ,dtb_question_result.pref
                        ,dtb_question_result.addr01
                        ,dtb_question_result.addr02
                        ,dtb_question_result.tel01
                        ,dtb_question_result.tel02
                        ,dtb_question_result.tel03
                        ,dtb_question_result.mail01
                        ,dtb_question_result.question01
                        ,dtb_question_result.question02
                        ,dtb_question_result.question03
                        ,dtb_question_result.question04
                        ,dtb_question_result.question05
                        ,dtb_question_result.question06
                    FROM dtb_question_result
                        LEFT JOIN dtb_question
                            ON dtb_question_result.question_id = dtb_question.question_id
                    WHERE 0=0
                        AND dtb_question_result.del_flg = 0
                        AND dtb_question_result.question_id = ?
                    ORDER BY dtb_question_result.result_id ASC
__EOS__;

            $list_data = $objQuery->getAll($sql, array($_GET['question_id']));
            $data = "";
            for($i = 0; $i < count($list_data); $i++) {
                // 各項目をCSV出力用に変換する。
                $data .= $objCSV->lfMakeCSV($list_data[$i]);
            }
            // CSVを送信する
            SC_Utils_Ex::sfCSVDownload($head.$data);
            exit;

        // 初回表示 or 編集ボタン押下時
        } else {
            if (!isset($_GET['question_id'])) $_GET['question_id'] = "";

            if ( is_numeric($_GET['question_id']) ){

                $sql = "SELECT question FROM dtb_question WHERE question_id = ?";
                $result = $objQuery->getOne($sql, array($_GET['question_id']));

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
