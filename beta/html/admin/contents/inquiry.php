<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

require_once("../order/index_csv.php");

$arrCVSCOL = array( 
		
				);
						
$arrCVSTITLE = array(
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


class LC_Page {
	var $cnt_question;

	var $ERROR;
	var $ERROR_COLOR;
	var $MESSAGE;
	
	var $QUESTION_ID;
	
	var $arrActive;
	var $arrQuestion;
	var $arrSession;
	
	function LC_Page() {
		$this->tpl_mainpage = 'contents/inquiry.tpl';
		$this->tpl_mainno = 'contents';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "inquiry";
		$this->tpl_subtitle = 'アンケート管理';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

$arrActive = array( "0"=>"稼働", "1"=>"非稼働" );
$arrQuestion = array( "0"=>"使用しない", "1"=>"テキストエリア", "2"=>"テキストボックス"
					, "3"=>"チェックボックス", "4"=>"ラジオボタン" 
				);
				
$sql = "SELECT *, cast(substring(create_date, 1, 10) as date) as disp_date FROM dtb_question WHERE del_flg = 0 ORDER BY question_id";
$result = $conn->getAll($sql);
$objPage->list_data = $result;


if ( $_GET['mode'] == 'regist' ){

	for ( $i=0; $i<count($_POST["question"]); $i++ ) {
		$_POST['question'][$i]['name'] = mb_convert_kana( trim ( $_POST['question'][$i]['name'] ), "K" );
		for ( $j=0; $j<count( $_POST['question'][$i]['option'] ); $j++ ){
			$_POST['question'][$i]['option'][$j] = mb_convert_kana( trim ( $_POST['question'][$i]['option'][$j] ) );
		}
	}
	
	$error = lfErrCheck();

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
			$objPage->MESSAGE = "登録が完了しました";

			if (DB_TYPE == "mysql") {
				$question_id = $objQuery->nextval('dtb_question', 'question_id');
			}
			
			$objPage->QUESTION_ID = $question_id;
			sfReload();
		} else {
			//編集
			$value = serialize($_POST);
			$sql_val = array( $value, $_POST['title'] ,$_POST['question_id'] );
			$conn->query("UPDATE dtb_question SET question = ?, question_name = ? WHERE question_id = ?", $sql_val );
			$objPage->MESSAGE = "編集が完了しました";
			$objPage->QUESTION_ID = $_POST['question_id'];
			sfReload();
		}
	} else {
		
		//エラー表示
		$objPage->ERROR = $error;
		$objPage->QUESTION_ID = $_REQUEST['question_id'];
		$objPage->ERROR_COLOR = lfGetErrColor($error, ERR_COLOR);

	}
} elseif ( ( $_GET['mode'] == 'delete' ) && ( sfCheckNumLength($_GET['question_id']) )  ){

	$sql = "UPDATE dtb_question SET del_flg = 1 WHERE question_id = ?";
	$conn->query( $sql, array( $_GET['question_id'] ) );
	sfReload();
	
} elseif ( ( $_GET['mode'] == 'csv' ) && ( sfCheckNumLength($_GET['question_id']) ) ){ 

			$head = sfGetCSVList($arrCVSTITLE);
			$list_data = $conn->getAll("SELECT result_id,question_id,question_date,question_name,name01,name02,kana01,kana02,zip01,zip02,pref,addr01,addr02,tel01,tel02,tel03,mail01,question01,question02,question03,question04,question05,question06 FROM dtb_question_result WHERE del_flg = 0 AND question_id = ? ORDER BY result_id ASC",array($_GET['question_id']));
			$data = "";
			for($i = 0; $i < count($list_data); $i++) {
				// 各項目をCSV出力用に変換する。
				$data .= lfMakeCSV($list_data[$i]);
			}
			// CSVを送信する
			sfCSVDownload($head.$data);
			exit;

} else {
	
	if ( is_numeric($_GET['question_id']) ){
	
		$sql = "SELECT question FROM dtb_question WHERE question_id = ?";
		$result = $conn->getOne($sql, array($_GET['question_id']));
		
		if ( $result ){
			$_POST = unserialize( $result );
			$objPage->QUESTION_ID = $_GET['question_id'];
		}
	}
} 




//各ページ共通
$objPage->cnt_question = 6;
$objPage->arrActive = $arrActive;
$objPage->arrQuestion = $arrQuestion;


//----　ページ表示
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


// ------------  エラーチェック処理部 ------------  

function lfGetErrColor( $arr, $err_color ){
	
	foreach ( $arr as $key=>$val ) {
		if ( is_string($val) && strlen($val) > 0 ){
			$return[$key] = $err_color;
		} elseif ( is_array( $val ) ) {
			$return[$key] = lfGetErrColor ( $val, $err_color);
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

	return lfGetArrInput( $objErr->arrErr );

}


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
?>