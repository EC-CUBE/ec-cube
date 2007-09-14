<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'design/bloc.tpl';
		$this->tpl_subnavi = 'design/subnavi.tpl';
		$this->tpl_subno_edit = 'bloc';
		$this->text_row = 13;
		$this->tpl_subno = "bloc";	
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = '�֥�å��Խ�';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// �֥�å����������
$objPage->arrBlocList = lfgetBlocData();

// �֥�å�ID�����
if (isset($_POST['bloc_id'])) {
	$bloc_id = $_POST['bloc_id'];
}else if ($_GET['bloc_id']){
	$bloc_id = $_GET['bloc_id'];
}else{
	$bloc_id = '';
}
$objPage->bloc_id = $bloc_id;

// bloc_id �����ꤵ��Ƥ�����ˤϥ֥�å��ǡ����μ���
if ($bloc_id != '') {
	$arrBlocData = lfgetBlocData(" bloc_id = ? " , array($bloc_id));
	$arrBlocData[0]['tpl_path'] = USER_PATH . $arrBlocData[0]['tpl_path'];

	// �ƥ�ץ졼�ȥե�������ɤ߹���
	$arrBlocData[0]['tpl_data'] = file_get_contents($arrBlocData[0]['tpl_path']);
	$objPage->arrBlocData = $arrBlocData[0];
}

// ��å�����ɽ��
if ($_GET['msg'] == "on") {
	// ��λ��å�����
	$objPage->tpl_onload="alert('��Ͽ����λ���ޤ�����');";
}

// �ץ�ӥ塼ɽ��
if ($_POST['mode'] == "preview") {
	// �ץ�ӥ塼�ե��������
	$prev_path = USER_INC_PATH . 'preview/bloc_preview.tpl';
	$fp = fopen($prev_path,"w");
	fwrite($fp, $_POST['bloc_html']);
	fclose($fp);
	
	// �ץ�ӥ塼�ǡ���ɽ��
	$objPage->preview = "on";
	$objPage->arrBlocData['tpl_data'] = $_POST['bloc_html'];
	$objPage->arrBlocData['tpl_path'] = $prev_path;
	$objPage->arrBlocData['bloc_name'] = $_POST['bloc_name'];
	$objPage->arrBlocData['filename'] = $_POST['filename'];
	$objPage->text_row = $_POST['html_area_row'];
}else{
	$objPage->preview = "off";
}

// �ǡ�����Ͽ����
if ($_POST['mode'] == 'confirm') {
	
	// ���顼�����å�
	$objPage->arrErr = lfErrorCheck($_POST);

	// ���顼���ʤ���й���������Ԥ�	
	if (count($objPage->arrErr) == 0) {
		// DB�إǡ����򹹿�����
		lfEntryBlocData($_POST);
		
		// �ե�����κ��
		$del_file=BLOC_PATH . $arrBlocData[0]['filename']. '.tpl';
		if (file_exists($del_file)) {
			unlink($del_file);
		}
		
		// �ե��������
		$fp = fopen(BLOC_PATH . $_POST['filename'] . '.tpl',"w");
		fwrite($fp, $_POST['bloc_html']);
		fclose($fp);
		
		$arrBlocData = lfgetBlocData(" filename = ? " , array($_POST['filename']));
			
		$bloc_id = $arrBlocData[0]['bloc_id'];	
		header("location: ./bloc.php?bloc_id=$bloc_id&msg=on");
	}else{
		// ���顼����������ϻ��Υǡ�����ɽ������
		$objPage->arrBlocData = $_POST;
	}
}

// �ǡ����������
if ($_POST['mode'] == 'delete') {
	
	// DB�إǡ����򹹿�����
	$objDBConn = new SC_DbConn;		// DB���֥�������
	$sql = "";						// �ǡ�������SQL������
	$ret = ""; 						// �ǡ���������̳�Ǽ��
	$arrDelData = array();			// �����ǡ���������
	
	// �����ǡ�������
	$arrUpdData = array($arrData['bloc_name'], BLOC_DIR . $arrData['filename'] . '.tpl', $arrData['filename']);
	
	// bloc_id �����Ǥʤ����ˤ�delete��¹�
	if ($_POST['bloc_id'] !== '') {
		// SQL����
		$sql = " DELETE FROM dtb_bloc WHERE bloc_id = ?";
		// SQL�¹�
		$ret = $objDBConn->query($sql,array($_POST['bloc_id']));
		
		// �ڡ��������֤���Ƥ���ǡ�����������
		$sql = "DELETE FROM dtb_blocposition WHERE bloc_id = ?";
		// SQL�¹�
		$ret = $objDBConn->query($sql,array($_POST['bloc_id']));
	
		// �ե�����κ��
		$del_file = BLOC_PATH . $arrBlocData[0]['filename']. '.tpl';
		if(file_exists($del_file)){
			unlink($del_file);
		}
	}

	header("location: ./bloc.php");
}


// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

/**************************************************************************************************************
 * �ؿ�̾	��lfgetBlocData
 * ��������	���֥�å�������������
 * ����1	��$where  ������ Where��ʸ
 * ����2	��$arrVal ������ Where��ιʹ������
 * �����	���֥�å�����
 **************************************************************************************************************/
function lfgetBlocData($where = '', $arrVal = ''){
	$objDBConn = new SC_DbConn;		// DB���֥�������
	$sql = "";						// �ǡ�������SQL������
	$arrRet = array();				// �ǡ���������
	
	// SQL����
	$sql = " SELECT ";
	$sql .= "	bloc_id";
	$sql .= "	,bloc_name";
	$sql .= "	,tpl_path";
	$sql .= "	,filename";
	$sql .= " 	,create_date";
	$sql .= " 	,update_date";
	$sql .= " 	,php_path";
	$sql .= " 	,del_flg";
	$sql .= " FROM ";
	$sql .= " 	dtb_bloc";

	// where��λ��꤬������ɲ�	
	if ($where != '') {
		$sql .= " WHERE " . $where;
	}
	
	$sql .= " ORDER BY 	bloc_id";
	
	$arrRet = $objDBConn->getAll($sql, $arrVal);
	
	return $arrRet;
}

/**************************************************************************************************************
 * �ؿ�̾	��lfEntryBlocData
 * ��������	���֥�å�����򹹿�����
 * ����1	��$arrData  ������ �����ǡ���
 * �����	���������
 **************************************************************************************************************/
function lfEntryBlocData($arrData){
	$objDBConn = new SC_DbConn;		// DB���֥�������
	$sql = "";						// �ǡ�������SQL������
	$ret = ""; 						// �ǡ���������̳�Ǽ��
	$arrUpdData = array();			// �����ǡ���������
	$arrChk = array();				// ��¾�����å���
	
	// �����ǡ�������
	$arrUpdData = array($arrData['bloc_name'], BLOC_DIR . $arrData['filename'] . '.tpl', $arrData['filename']);
	
	// �ǡ�����¸�ߤ��Ƥ��뤫�����å���Ԥ�
	if($arrData['bloc_id'] !== ''){
		$arrChk = lfgetBlocData("bloc_id = ?", array($arrData['bloc_id']));
	}
	
	// bloc_id ���� �㤷���� �ǡ�����¸�ߤ��Ƥ��ʤ����ˤ�INSERT��Ԥ�
	if ($arrData['bloc_id'] === '' or !isset($arrChk[0])) {
		// SQL����
		$sql = " INSERT INTO dtb_bloc";
		$sql .= " ( ";
		$sql .= "     bloc_name ";		// �֥�å�̾��
		$sql .= "     ,tpl_path ";		// �ƥ�ץ졼����¸��
		$sql .= "     ,filename ";		// �ե�����̾��
		$sql .= "     ,create_date ";	// ������
		$sql .= "     ,update_date ";	// ������
		$sql .= " ) VALUES ( ?,?,?,now(),now() )";
		$sql .= " ";
	}else{
		// �ǡ�����¸�ߤ��Ƥ���ˤϥ��åץǡ��Ȥ�Ԥ�
		// SQL����
		$sql = " UPDATE dtb_bloc";
		$sql .= " SET";
		$sql .= "     bloc_name = ? ";	// �֥�å�̾��
		$sql .= "     ,tpl_path = ? ";	// �ƥ�ץ졼����¸��
		$sql .= "     ,filename = ? ";	// �ƥ�ץ졼�ȥե�����̾
		$sql .= "     ,update_date = now()";
		$sql .= " WHERE bloc_id = ?";
		$sql .= " ";
		
		// �����ǡ����˥֥�å�ID���ɲ�
		array_push($arrUpdData, $arrData['bloc_id']);
	}
	
	// SQL�¹�
	$ret = $objDBConn->query($sql,$arrUpdData);
	
	return $ret;

}

/**************************************************************************************************************
 * �ؿ�̾	��lfErrorCheck
 * ��������	�����Ϲ��ܤΥ��顼�����å���Ԥ�
 * ����1	��$arrData  ������ ���ϥǡ���
 * �����	�����顼����
 **************************************************************************************************************/
function lfErrorCheck($array) {
	global $objPage;
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("�֥�å�̾", "bloc_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�ե�����̾", "filename", STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK","FILE_NAME_CHECK"));
	
	// Ʊ��Υե�����̾��¸�ߤ��Ƥ�����ˤϥ��顼
	if(!isset($objErr->arrErr['filename']) and $array['filename'] !== ''){
		$arrChk = lfgetBlocData("filename = ?", array($array['filename']));
		
		if (count($arrChk[0]) >= 1 and $arrChk[0]['bloc_id'] != $array['bloc_id']) {
			$objErr->arrErr['filename'] = '�� Ʊ���ե�����̾�Υǡ�����¸�ߤ��Ƥ��ޤ����̤�̾�Τ��դ��Ƥ���������';
		}
	}
	
	return $objErr->arrErr;
}
