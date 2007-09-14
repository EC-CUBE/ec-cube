<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	
	var $arrSession;
	var $site_info;
	var $objDate;
	var $arrForm;
	var $mode;
	var $arrMagazineType;
	var $title;
	
	function LC_Page() {
		$this->tpl_mainpage = 'mail/template_input.tpl';
		$this->tpl_mainno = 'mail';
		$this->tpl_subnavi = 'mail/subnavi.tpl';
		$this->tpl_subno = "template";
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

$objPage->arrMagazineType = $arrMagazineType;
$objPage->mode = "regist";

// id�����ꤵ��Ƥ���Ȥ��ϡ��Խ���ɽ��
if ( $_REQUEST['template_id'] ){
	$objPage->title = "�Խ�";
} else {
	$objPage->title = "������Ͽ";
}

// �⡼�ɤˤ�����ʬ��
if ( $_GET['mode'] == 'edit' && sfCheckNumLength($_GET['template_id'])===true ){
	
	// �Խ�
	$sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ? AND del_flg = 0";
	$result = $conn->getAll($sql, array($_GET['template_id']));
	$objPage->arrForm = $result[0];
	
		
} elseif ( $_POST['mode'] == 'regist' ) {
	
	// ������Ͽ
	$objPage->arrForm = lfConvData( $_POST );
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	
	if ( ! $objPage->arrErr ){
		// ���顼��̵���Ȥ�����Ͽ���Խ�
		lfRegistData( $objPage->arrForm, $_POST['template_id']);	
		sfReload("mode=complete");	// ��ʬ����ɹ����ơ���λ���̤�����
	}
	
} elseif ( $_GET['mode'] == 'complete' ) {		
	
	// ��λ����ɽ��
	$objPage->tpl_mainpage = 'mail/template_complete.tpl';
	
} 






$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


function lfRegistData( $arrVal, $id = null ){
	
	$query = new SC_Query();
	
	$sqlval['subject'] = $arrVal['subject'];
	$sqlval['mail_method'] = $arrVal['mail_method'];
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['body'] = $arrVal['body'];
	$sqlval['update_date'] = "now()";

	if ( $id ){
		$query->update("dtb_mailmaga_template", $sqlval, "template_id=".$id );
	} else {
		$sqlval['create_date'] = "now()";
		$query->insert("dtb_mailmaga_template", $sqlval);
	}
}

function lfConvData( $data ){
	
	 // ʸ������Ѵ���mb_convert_kana���Ѵ����ץ�����							
	$arrFlag = array(
					  "subject" => "KV"
					 ,"body" => "KV"
					);
		
	if ( is_array($data) ){
		foreach ($arrFlag as $key=>$line) {
			$data[$key] = mb_convert_kana($data[$key], $line);
		}
	}

	return $data;
}

// ���ϥ��顼�����å�
function lfErrorCheck() {
	$objErr = new SC_CheckError();
	
	$objErr->doFunc(array("�᡼�����", "mail_method"), array("EXIST_CHECK", "ALNUM_CHECK"));
	$objErr->doFunc(array("Subject", "subject", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��ʸ", 'body', LLTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));

	return $objErr->arrErr;
}



?>