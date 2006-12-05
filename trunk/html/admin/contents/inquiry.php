<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

require_once("../order/index_csv.php");

$arrCVSCOL = array( 
		
				);
						
$arrCVSTITLE = array(
				'����ID',
				'����ID',
				'��������',
				'����̾',
				'�ܵ�̾1',
				'�ܵ�̾2',
				'�ܵ�̾����1',
				'�ܵ�̾����2',
				'͹���ֹ�1',
				'͹���ֹ�2',
				'��ƻ�ܸ�',
				'����1',
				'����2',
				'�����ֹ�1',
				'�����ֹ�2',
				'�����ֹ�3',
				'�᡼�륢�ɥ쥹',
				'����1',
				'����2',
				'����3',
				'����4',
				'����5',
				'����6'				
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
		$this->tpl_subtitle = '���󥱡��ȴ���';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

$arrActive = array( "0"=>"��Ư", "1"=>"���Ư" );
$arrQuestion = array( "0"=>"���Ѥ��ʤ�", "1"=>"�ƥ����ȥ��ꥢ", "2"=>"�ƥ����ȥܥå���"
					, "3"=>"�����å��ܥå���", "4"=>"�饸���ܥ���" 
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
			
			//��Ͽ
			$value = serialize($_POST);
			if (DB_TYPE == "pgsql") {
				$question_id = $objQuery->nextval('dtb_question', 'question_id');
			}
			
			$sql_val = array( $value, $_POST['title'] ,$question_id );
			$conn->query("INSERT INTO dtb_question ( question, question_name, question_id, create_date) VALUES (?, ?, ?, now())", $sql_val );
			$objPage->MESSAGE = "��Ͽ����λ���ޤ���";

			if (DB_TYPE == "mysql") {
				$question_id = $objQuery->nextval('dtb_question', 'question_id');
			}
			
			$objPage->QUESTION_ID = $question_id;
			sfReload();
		} else {
			//�Խ�
			$value = serialize($_POST);
			$sql_val = array( $value, $_POST['title'] ,$_POST['question_id'] );
			$conn->query("UPDATE dtb_question SET question = ?, question_name = ? WHERE question_id = ?", $sql_val );
			$objPage->MESSAGE = "�Խ�����λ���ޤ���";
			$objPage->QUESTION_ID = $_POST['question_id'];
			sfReload();
		}
	} else {
		
		//���顼ɽ��
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
			$list_data = $conn->getAll("SELECT result_id,question_id,question_date,question_name,name01,name02,kana01,kana02,zip01,zip02,pref,addr01,addr02,tel01,tel02,tel03,mail01,question01,question02,question03,question04,question05,question06 FROM dtb_question_result WHERE del_flg = 0 ORDER BY result_id ASC");
			$data = "";
			for($i = 0; $i < count($list_data); $i++) {
				// �ƹ��ܤ�CSV�����Ѥ��Ѵ����롣
				$data .= lfMakeCSV($list_data[$i]);
			}
			// CSV����������
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




//�ƥڡ�������
$objPage->cnt_question = 6;
$objPage->arrActive = $arrActive;
$objPage->arrQuestion = $arrQuestion;


//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


// ------------  ���顼�����å������� ------------  

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


// ------------  ���顼�����å������� ------------  

function lfErrCheck (){

	$objErr = new SC_CheckError();
	$errMsg = "";

	$objErr->doFunc( array( "��Ư�����Ư", "active" ), array( "SELECT_CHECK" ) );
	
	$_POST["title"] = mb_convert_kana( trim (  $_POST["title"] ), "K" );
	$objErr->doFunc( array( "���󥱡���̾", "title" ), array( "EXIST_CHECK" ) );

	$_POST["contents"] = mb_convert_kana( trim (  $_POST["contents"] ), "K" );
	$objErr->doFunc( array( "���󥱡�������" ,"contents", "3000" ), array( "EXIST_CHECK", "MAX_LENGTH_CHECK" ) );

	
	if ( ! $_POST['question'][0]["name"] ){
		$objErr->arrErr['question'][0]["name"] = "���Ĥ�μ���̾�����Ϥ���Ƥ��ޤ���";
	}
	
	//�������å��ܥå������饸���ܥ�������򤷤����Ϻ���1�İʾ���ܤ��������롣
	for( $i = 0; $i < count( $_POST["question"] ); $i++ ) {
		
		if ( $_POST["question"][$i]["kind"] ) {
			if (strlen($_POST["question"][$i]["name"]) == 0) {
				$objErr->arrErr["question"][$i]["name"] = "�����ȥ�����Ϥ��Ʋ�������";
			} else if ( strlen($_POST["question"][$i]["name"]) > STEXT_LEN ) {
				$objErr->arrErr["question"][$i]["name"] = "�����ȥ��". STEXT_LEN  ."����������Ϥ��Ʋ�������";
			}
		}
		
		if( $_POST["question"][$i]["kind"] == 3 || $_POST["question"][$i]["kind"] == 4  ) {

			$temp_data = array();
			for( $j = 0; $j < count( $_POST["question"][$i]["option"] ); $j++ ) {	

				// ���ܴ֡ʥƥ����ȥܥå����ˤ������Ƥ�����ͤ�Ƥ���
				if( strlen( $_POST["question"][$i]["option"][$j] ) > 0 ) $temp_data[] = mb_convert_kana( trim ( $_POST["question"][$i]["option"][$j]  ), "asKVn" );

			}

			 $_POST["question"][$i]["option"] = $temp_data;

			if( ( strlen( $_POST["question"][$i] ["option"][0] ) == 0 ) || ( strlen( $_POST["question"][$i] ["option"][0] ) > 0
			 && strlen( $_POST["question"][$i] ["option"][1] ) == 0 ) ) $objErr->arrErr["question"][$i]['kind'] = "������2�İʾ�ι��ܤ˵������Ƥ���������";
		}
	}

	return lfGetArrInput( $objErr->arrErr );

}


function lfGetArrInput( $arr ){
	// �ͤ����Ϥ��줿����Τߤ��֤�
	
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