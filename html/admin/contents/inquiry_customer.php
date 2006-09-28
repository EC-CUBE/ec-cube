<?
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $errmsg;
	var $arrPref;
	
	var $QUESTION;
	var $question_id;
	
	function LC_Page() {
		$this->tpl_mainpage = 'contents/inquiry_customer.tpl';
		$this->tpl_mainno = 'contents';
		$this->tpl_subnavi = 'contents/sub_navi.tpl';
		$this->tpl_subno = "inquiry";
		$this->tpl_subtitle = '���󥱡��ȴ���';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();


// ��ƻ�ܸ��ץ������������
$objPage->arrPref = $arrPref;	


// CSV��¸����
$ITEM = array(   "name"
				,"name_kana"
				,"zip"
				,"state"
				,"address01"
				,"address02"
				,"tel"
				,"mail01"
		);

		
		
if ( ( ! $_POST['mode'] == 'confirm' ) && ( ! is_numeric($_REQUEST['question_id']) ) ){
	echo "������������";
	exit;
}
		
		
// �ƥ�ץ졼����Ͽ���ܼ���
$sql = "SELECT question_id, question FROM dtb_question WHERE question_id = ?";
$result = $conn->getAll( $sql, array($_REQUEST['question_id']) );
$objPage->QUESTION = lfGetArrInput( unserialize( $result[0]['question'] ) );
$objPage->question_id = $result[0]['question_id'];


if ( (int)$objPage->QUESTION["delete"] !== 0 ){

	$objPage->tpl_mainpage = "question/closed.tpl";
	
} elseif( $_POST['mode'] == "confirm" ) {

	$errmsg  = errCheck();

	if( $errmsg ) {

		$objPage->errmsg = $errmsg;

	} else {
		$page_title = "�ե����ƥ�餯�餯����åԥ� - ���󥱡��� ���Ƴ�ǧ-";
		$page_file_name = "question/confirm.tpl";
	}
	

} elseif( $_POST['mode'] == "complete" )  {

	//��λ����
	$page_file_name = "question/complete.tpl";
	
	// �������ա����������֤��������
	$date = getDateLocal();
	
	
	//�ǡ�������
	$SQLDATA = $_POST;
	$SQLDATA['zip'] = $SQLDATA['zip01'] ."-". $SQLDATA['zip02'];
	$SQLDATA['tel'] = $SQLDATA['tel01'] ."-". $SQLDATA['tel02'] ."-". $SQLDATA['tel03'];
	
	//--------- �� SQL ---------//
		$sql = "INSERT INTO dtb_question_result ( question_name, question_id";
		$sql_val = " ?, ? ";
		$value[] = $objPage->QUESTION[title];
		$value[] = $objPage->QUESTION[question_id];
		
		foreach ($ITEM as $val) {
			if ( strlen( $SQLDATA[$val] ) > 0  ){
				$sql .= "," .$val;
				$sql_val .= ",? ";
				$value[] = $SQLDATA[$val];
			}
		}
		
		for ( $i=0; $i<(count($objPage->QUESTION)+1); $i++ ){
			
			$tmpVal = "";
			
			if ( $objPage->QUESTION[question][$i][kind] == 1 or $objPage->QUESTION[question][$i][kind] == 2 ) {
				
				//�ƥ����ȥ��ꥢ���ܥå���
				$tmpVal = $SQLDATA['option'][$i];
			
			} elseif ( $objPage->QUESTION[question][$i][kind] == 4 ){
				
				//��¥��ܥ���
				$tmpVal = Array_Search_key ( $SQLDATA['option'][$i] , $objPage->QUESTION[question][$i][option] );
			
			} elseif ( $objPage->QUESTION[question][$i][kind] == 3 )  {
				
				//�����å��ܥå���
				if ( is_array( $SQLDATA['option'][$i] ) ) {
					foreach ($SQLDATA['option'][$i] as $data) {
						if ( strlen($data) ) {
							if ( $tmpVal ) $tmpVal .= "\n";
							$tmpVal .= Array_Search_key ( $data , $objPage->QUESTION[question][$i][option] );
						}
					}
				}
			}


			if ( strlen($tmpVal) > 0 ) {
				$value[] = $tmpVal;
				$sql .= "," ."question".($i+1);
				$sql_val .= ",? ";
			}
		}
	
		
		$sql = $sql .") VALUES ( ".$sql_val.")";
		$conn->query( $sql, $value );
		
	//--------- �� SQL ---------//

}


$objPage->cnt_question = 6;
$objPage->arrActive = $arrActive;
$objPage->arrQuestion = $arrQuestion;


//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);













// ------------  ���顼�����å������� ------------  
function errCheck() {

	$objErr = new ERROR_CHECK();
	$errmsg = array();


	//����̾��
	$_POST["name"] = mb_convert_kana( trim( $_POST["name"] ) ,"asKVn" );	
	$errmsg["name"] = $objErr->doFunc( array( $_POST["name"] , "��̾��", NAME_VOL, "BIG" ), array( "EXIST_CHECK", "MAX_LENGTH_CHECK" ) );

	//���եꥬ��
	$_POST["name_kana"] = mb_convert_kana( trim( $_POST["name_kana"]) , "sCKV" ); 
	$errmsg["name_kana"] = $objErr->doFunc( array( $_POST["name_kana"] , "�եꥬ��", NAME_VOL, "BIG" ), array( "EXIST_CHECK", "MAX_LENGTH_CHECK" , "KANA_CHECK") );

	//��͹���ֹ�
	if( strlen( $_POST["zip01"] ) > 0 && strlen( $_POST["zip02"] ) > 0 ) {
		
		$_POST["zip01"] = mb_convert_kana( trim( $_POST["zip01"] ) , "n");
		$_POST["zip02"] = mb_convert_kana( trim( $_POST["zip02"] ) , "n");
		$zip = $_POST["zip01"] . $_POST["zip02"];
		$errmsg["zip"] = $objErr->doFunc( array( $zip , "͹���ֹ�", ZIP_NO ), array( "NUM_CHECK", "NUM_COUNT_CHECK2" ) );

		//Ʊ��ʸ���Ф����͹���ֹ�ϵ��Ĥ��ʤ�
		if ( ereg('^(0+|1+|2+|3+|4+|5+|6+|7+|8+|9+)$', $zip ) ) $errmsg['zip'] .= "�� ͹���ֹ�����������Ϥ��Ƥ���������<br>";
	}

	//��������
	if( strlen( $_POST["address01"] ) > 0 || strlen( $_POST["address02"] ) > 0 ) {

		$_POST["address01"] = mb_convert_kana( trim($_POST["address01"] ), "asKVn" );
		$_POST["address02"] = mb_convert_kana( trim($_POST["address02"] ), "asKVn" );

		if( $_POST["state"] == "" ) $errmsg["address"] = "�� ��ƻ�ܸ������򤷤Ƥ���������<br>";
		$errmsg["address"] .= $objErr->doFunc( array( $_POST["address01"], "������ʻԶ�Į¼��", ADDRESS_VOL, "BIG" ), array( "EXIST_CHECK", "MAX_LENGTH_CHECK" ) );
		$errmsg["address"] .= $objErr->doFunc( array( $_POST["address02"], "����������Ϥʤɡ�", ADDRESS_VOL, "BIG" ), array( "EXIST_CHECK", "MAX_LENGTH_CHECK" ) );
	}

	//���������ֹ�
	if( strlen( $_POST["tel01"] ) > 0 ||  strlen( $_POST["tel03"] ) > 0 ||  strlen( $_POST["tel03"] ) > 0 ) {

		$_POST["tel01"] = mb_convert_kana( trim($_POST["tel01"] ), "n" );
		$_POST["tel02"] = mb_convert_kana( trim($_POST["tel02"] ), "n" );
		$_POST["tel03"] = mb_convert_kana( trim($_POST["tel03"] ), "n" );
		$tel = $_POST["tel01"] . $_POST["tel02"] . $_POST["tel03"];
		$errmsg["tel"] = $objErr->doFunc( array( $tel, "�����ֹ�", TEL_MIN_NO, TEL_MAX_NO ), array( "NUM_CHECK", "NUM_COUNT_CHECK" ) );
	
		//Ʊ��ʸ���Ф���������ֹ�ϵ��Ĥ��ʤ�
		if ( ereg('^(0+|1+|2+|3+|4+|5+|6+|7+|8+|9+)$',$_POST['tel01'].$_POST['tel02'].$_POST['tel03']) ) $errmsg['tel'] .= "�� �����ֹ�����������Ϥ��Ƥ���������<br>";

	}

	//���᡼�륢�ɥ쥹
	$_POST["mail01"] = mb_convert_kana( trim( $_POST["mail01"] ), "a" );
	$_POST["mail02"] = mb_convert_kana( trim( $_POST["mail02"] ), "a" );
	$errmsg_mail =  $objErr->doFunc( array( $_POST["mail01"] , "�᡼�륢�ɥ쥹", EMAIL_VOL, "small" )
									,array( "EXIST_CHECK", "EMAIL_CHECK", "MAX_LENGTH_CHECK"  ) );

	$errmsg_mail .= $objErr->doFunc( array( $_POST["mail02"] , "��ǧ�ѤΥ᡼�륢�ɥ쥹", EMAIL_VOL, "small" )
									,array( "EXIST_CHECK", "EMAIL_CHECK", "MAX_LENGTH_CHECK"   ) );
	if( ! $errmsg_mail ) {

		$errmsg["mail"] = $objErr->doFunc( array( $_POST["mail01"] , "�᡼�륢�ɥ쥹", $_POST["mail02"] , "��ǧ�ѤΥ᡼�륢�ɥ쥹" ), array( "EQUAL_CHECK" ) );

	} else {

		$errmsg["mail"] = $errmsg_mail;
	}

	$errmsg['option'] =  array_map( "checkNull", $_POST['option'] );
	for( $i = 0; $i < count( $_POST["option"] ) ; $i ++ ) {

		$_POST["option"][$i] = mb_convert_kana( trim( $_POST["option"][$i] ), "asKVn" );

	}
	
	return $returnMsg = lfGetArrInput($errmsg);

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