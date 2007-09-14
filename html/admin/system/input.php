<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrErr;		// ���顼��å�����������
	var $tpl_recv;		// ���Ͼ���POST��
	var $tpl_onload;	// �ڡ����ɤ߹��߻��Υ��٥��
	var $arrForm;		// �ե����������
	var $tpl_mode;		// ��������:new or �Խ�:edit
	var $tpl_member_id; // �Խ����˻��Ѥ��롣
	var $tpl_pageno;
	var $tpl_onfocus;	// �ѥ���ɹ���������Υ��٥����
	var $tpl_old_login_id;
	function LC_Page() {
		$this->tpl_recv =  'input.php';
		$this->tpl_pageno = $_REQUEST['pageno'];
		$this->SHORTTEXT_MAX = STEXT_LEN;
		$this->MIDDLETEXT_MAX = MTEXT_LEN;
		$this->LONGTEXT_MAX = LTEXT_LEN;
		global $arrAUTHORITY;
		$this->arrAUTHORITY = $arrAUTHORITY;
	}
}

$conn = new SC_DbConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// member_id�����ꤵ��Ƥ�����硢�Խ��⡼�ɤȤ��롣
if(sfIsInt($_GET['id'])) {
	$objPage->tpl_mode = 'edit';
	$objPage->tpl_member_id = $_GET['id'];
	$objPage->tpl_onfocus = "fnClearText(this.name);";
	// DB�Υ��С�������ɤ߽Ф�
	$data_list = fnGetMember($conn, $_GET['id']);
	// �����桼����ɽ��������
	$objPage->arrForm = $data_list[0];
	// ���ߡ��Υѥ���ɤ򥻥åȤ��Ƥ�����
	$objPage->arrForm['password'] = DUMMY_PASS;
	// ������ID���ݴɤ��Ƥ�����
	$objPage->tpl_old_login_id = $data_list[0]['login_id'];
    
    $objPage->tpl_uniqid = $objSess->getUniqId();
} else {
	// ���������⡼��
	$objPage->tpl_mode = "new";
	$objPage->arrForm['authority'] = -1;
}

// ���������⡼�� or �Խ��⡼��
if( $_POST['mode'] == 'new' || $_POST['mode'] == 'edit') {
    // �������ܤ������������å�
	if (sfIsValidTransition($objSess) == false) {
        sfDispError(INVALID_MOVE_ERRORR);
    }
    // ���ϥ��顼�����å�
	$objPage->arrErr = fnErrorCheck($conn);
	
	// ���Ϥ�����Ǥ��ä����ϡ�DB�˽񤭹���
	if(count($objPage->arrErr) == 0) {
		if($_POST['mode'] == 'new') {
			// ���С����ɲ�
			fnInsertMember();
			// ����ɤˤ�������Ͽ�к��Τ��ᡢƱ���ڡ��������Ф���
			header("Location: ". $_SERVER['PHP_SELF'] . "?mode=reload");	
			exit;
		}
		if($_POST['mode'] == 'edit') {
			// ���С����ɲ�
			if(fnUpdateMember($_POST['member_id'])) {
				// �ƥ�����ɥ��򹹿��塢��������ɥ����Ĥ��롣
				$url = URL_SYSTEM_TOP . "?pageno=".$_POST['pageno'];
				$objPage->tpl_onload="fnUpdateParent('".$url."'); window.close();";
			}
		}
	// ���ϥ��顼��ȯ���������
	} else {
		// �⡼�ɤ�����
		$objPage->tpl_mode = $_POST['mode'];
		$objPage->tpl_member_id = $_POST['member_id'];
		$objPage->tpl_old_login_id = $_POST['old_login_id'];
		// ���Ǥ����Ϥ����ͤ�ɽ�����롣
		$objPage->arrForm = $_POST;
		// �̾����ϤΥѥ���ɤϰ����Ѥ��ʤ���
		if($objPage->arrForm['password'] != DUMMY_PASS) {
			$objPage->arrForm['password'] = '';
		}
	}
}

// ����ɤλ��꤬���ä����
if( $_GET['mode'] == 'reload') {
	// �ƥ�����ɥ��򹹿�����褦�˥��åȤ��롣
	$url = URL_SYSTEM_TOP;
	$objPage->tpl_onload="fnUpdateParent('".$url."')";
}

// �������ܤ������������å��Ѥ�uniqid��������
$objPage->tpl_uniqid = $objSess->getUniqId();

// �ƥ�ץ졼�����ѿ��γ������
$objView->assignobj($objPage);
$objView->display('system/input.tpl');

/* ���ϥ��顼�Υ����å� */
function fnErrorCheck($conn) {
	
	$objErr = new SC_CheckError();
	
	$_POST["name"] = mb_convert_kana($_POST["name"] ,"KV");
	$_POST["department"] = mb_convert_kana($_POST["department"] ,"KV");
	
	// ̾�������å�
	$objErr->doFunc(array("̾��",'name'), array("EXIST_CHECK"));
	$objErr->doFunc(array("̾��",'name',STEXT_LEN,"BIG"), array("MAX_LENGTH_CHECK"));
	
	// �Խ��⡼�ɤǤʤ����ϡ���ʣ�����å�
	if (!isset($objErr->arrErr['name']) && $_POST['mode'] != 'edit') {
		$sql = "SELECT name FROM dtb_member WHERE del_flg <> 1 AND name = ?";
		$result = $conn->getOne($sql, array($_POST['name'])); 
		if ( $result ) {
			$objErr->arrErr['name'] = "������Ͽ����Ƥ���̾���ʤΤ����ѤǤ��ޤ���<br>";
		}
	}
		
	// ������ID�����å�
	$objErr->doFunc(array("������ID",'login_id'), array("EXIST_CHECK", "ALNUM_CHECK"));
	$objErr->doFunc(array("������ID",'login_id',ID_MIN_LEN , ID_MAX_LEN) ,array("NUM_RANGE_CHECK"));

	// �����⡼�ɤ⤷���ϡ��Խ��⡼�ɤǥ�����ID���ѹ�����Ƥ�����ϥ����å����롣
	if (!isset($objErr->arrErr['login_id']) && $_POST['mode'] != 'edit' || ($_POST['mode'] == 'edit' && $_POST['login_id'] != $_POST['old_login_id'])) {
		$sql = "SELECT login_id FROM dtb_member WHERE del_flg <> 1 AND login_id = ?";
		$result = $conn->getOne($sql, array($_POST['login_id'])); 
		if ( $result != "" ) {
			$objErr->arrErr['login_id'] = "������Ͽ����Ƥ���ID�ʤΤ����ѤǤ��ޤ���<br>";
		}
	}
	
	// �ѥ���ɥ����å�(�Խ��⡼�ɤ�DUMMY_PASS�����Ϥ���Ƥ�����ϡ����롼����)
	if(!($_POST['mode'] == 'edit' && $_POST['password'] == DUMMY_PASS)) { 
		$objErr->doFunc(array("�ѥ����",'password'), array("EXIST_CHECK", "ALNUM_CHECK"));
		if (!$arrErr['password']) {
			// �ѥ���ɤΥ����å�
			$objErr->doFunc( array("�ѥ����",'password',4 ,15 ) ,array( "NUM_RANGE_CHECK" ) );	
		}
	}
	
	// ���¥����å�
	$objErr->doFunc(array("����",'authority'),array("EXIST_CHECK"));
	return $objErr->arrErr;
}

/* DB�ؤΥǡ������� */
function fnInsertMember() {
	// �����꡼���饹�����
	$oquery = new SC_Query();
	// INSERT�����ͤ�������롣
	$sqlval['name'] = $_POST['name'];
	$sqlval['department'] = $_POST['department'];
	$sqlval['login_id'] = $_POST['login_id'];
	$sqlval['password'] = sha1($_POST['password'] . ":" . AUTH_MAGIC);
	$sqlval['authority'] = $_POST['authority'];
	$sqlval['rank']=  $oquery->max("dtb_member", "rank") + 1;
	$sqlval['work'] = "1"; // ��Ư������
	$sqlval['del_flg'] = "0";	// ����ե饰��OFF������
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['create_date'] = "now()";
	$sqlval['update_date'] = "now()";
	// INSERT�μ¹�
	$ret = $oquery->insert("dtb_member", $sqlval);
	return $ret;
}

/* DB�ؤΥǡ������� */
function fnUpdateMember($id) {
	// �����꡼���饹�����
	$oquery = new SC_Query();
	// INSERT�����ͤ�������롣
	$sqlval['name'] = $_POST['name'];
	$sqlval['department'] = $_POST['department'];
	$sqlval['login_id'] = $_POST['login_id'];
	if($_POST['password'] != DUMMY_PASS) {
		$sqlval['password'] = sha1($_POST['password'] . ":" . AUTH_MAGIC);
	}
	$sqlval['authority'] = $_POST['authority'];
	$sqlval['update_date'] = "now()";
	// UPDATE�μ¹�
	$where = "member_id = " . $id;
	$ret = $oquery->update("dtb_member", $sqlval, $where);
	return $ret;
}

/* DB����ǡ������ɤ߹��� */
function fnGetMember($conn, $id) {
	$sqlse = "SELECT name,department,login_id,authority FROM dtb_member WHERE member_id = ?";
	$ret = $conn->getAll($sqlse, Array($id));
	return $ret;
}
?>