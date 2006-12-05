<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../admin/require.php");

class LC_Page {
	var $errmsg;
	var $arrPref;
	
	var $QUESTION;
	var $question_id;
	
	function LC_Page() {
		$this->tpl_mainpage = 'inquiry/index.tpl';
		$this->tpl_mainno = 'contents';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objSess = new SC_Session();


// ��ƻ�ܸ��ץ������������
$objPage->arrPref = $arrPref;	

// CSV��¸����
//---- ��Ͽ�ѥ�������� ���ץ����ʳ�
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
	echo "������������";
	exit;
}

// �ƥ�ץ졼����Ͽ���ܼ���
$sql = "SELECT question_id, question FROM dtb_question WHERE question_id = ?";
$result = $conn->getAll( $sql, array($_REQUEST['question_id']) );
$objPage->QUESTION = lfGetArrInput( unserialize( $result[0]['question'] ) );

$objPage->question_id = $_REQUEST['question_id'];

$objPage->arrHidden = sfMakeHiddenArray($_POST);
unset($objPage->arrHidden['mode']);

if ( (int)$objPage->QUESTION["delete"] !== 0 ){

	$objPage->tpl_mainpage = "inquiry/closed.tpl";
	
} elseif( $_POST['mode'] == "confirm" ) {
	
	//--�����ϥ��顼�����å�
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

	//--������ʸ�����Ѵ������顼�����å�
	$objPage->arrForm = $_POST;
	$objPage->arrForm = lfConvertParam($objPage->arrForm, $arrRegistColumn);
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	$objPage->arrErr = lfGetArrInput($objPage->arrErr);

	
	if( ! $objPage->arrErr ) {
	
		//��λ����
		$objPage->tpl_mainpage = "inquiry/complete.tpl";

		
		//--------- �� SQL ---------//
			
			// �ơ��֥�������褦����������
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
			// �ģ���Ͽ
			$objQuery = new SC_Query();
			$objQuery->insert("dtb_question_result", $objPage->arrForm );
			
		//--------- �� SQL ---------//

	}
}

$objPage->cnt_question = 6;
$objPage->arrActive = $arrActive;
$objPage->arrQuestion = $arrQuestion;


//----���ڡ���ɽ��
$objView->_smarty->register_function("lfArray_Search_key_Smarty","lfArray_Search_key_Smarty");
$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);

// ------------  ���顼�����å������� ------------  
function lfErrorCheck($array) {

	$objErr = new SC_CheckError($array);

	$objErr->doFunc(array("��̾��������", 'name01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾����̾��", 'name02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�եꥬ��(������", 'kana01', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("�եꥬ�ʡʥᥤ��", 'kana02', STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK")); 
	$objErr->doFunc(array("͹���ֹ�", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
	$objErr->doFunc(array("��ƻ�ܸ�", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
	$objErr->doFunc(array("������1", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("������2", "addr02", MTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������ֹ�1", 'tel01'), array("EXIST_CHECK", "NUM_CHECK"));
	$objErr->doFunc(array("�������ֹ�2", 'tel02'), array("EXIST_CHECK", "NUM_CHECK"));
	$objErr->doFunc(array("�������ֹ�3", 'tel03'), array("EXIST_CHECK", "NUM_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', "email", MTEXT_LEN) ,array("EXIST_CHECK", "SPTAB_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹(��ǧ)', "email02", MTEXT_LEN) ,array("EXIST_CHECK", "SPTAB_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', '�᡼�륢�ɥ쥹(��ǧ)', "email", "email02") ,array("EQUAL_CHECK"));
	
	$objErr->arrErr["option"] =  array_map( "lfCheckNull", (array)$_POST['option'] );
	
	return $objErr->arrErr;
}

//----������ʸ������Ѵ�
function lfConvertParam($array, $arrRegistColumn) {

	// �����̾�ȥ���С��Ⱦ���
	foreach ($arrRegistColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}
	// ʸ���Ѵ�
	$new_array = array();
	foreach ($arrConvList as $key => $val) {
		$new_array[$key] = $array[$key];
		if( strlen($val) > 0) {
			$new_array[$key] = mb_convert_kana($new_array[$key] ,$val);
		}
	}
	
	// ���ץ����������
	for ($i=0; $i<count($array['option']); $i++){
		if ( is_array($array['option'][$i]) ){
			$new_array['option'][$i] = $array['option'][$i];
		} else {
			$new_array['option'][$i] = mb_convert_kana($array['option'][$i] ,"aKV");
		}
	}
	

	return $new_array;
	
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

function lfArray_Search_key_Smarty ( $palams ){

	$val = $palams['val'];
	$arr = $palams['arr']; 
	
	$revers_arr = array_flip($arr);
	return array_search( $val ,$revers_arr );

	
}

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

?>