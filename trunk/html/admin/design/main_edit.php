<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");
require_once(DATA_PATH . "include/page_layout.inc");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'design/main_edit.tpl';
		$this->tpl_subnavi 	= 'design/subnavi.tpl';
		$this->user_URL	 	= USER_URL;
		$this->text_row 	= 13;
		$this->tpl_subno = "main_edit";
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = '�ڡ����ܺ�����';
	}
}
$objPage = new LC_Page();
$objView = new SC_AdminView();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// �ڡ������������
$objPage->arrPageList = lfgetPageData();

// �֥�å�ID�����
if (isset($_POST['page_id'])) {
	$page_id = $_POST['page_id'];
}else if ($_GET['page_id']){
	$page_id = $_GET['page_id'];
}else{
	$page_id = '';
}

$objPage->page_id = $page_id;

// ��å�����ɽ��
if ($_GET['msg'] == "on"){
	$objPage->tpl_onload="alert('��Ͽ����λ���ޤ�����');";
}

// page_id �����ꤵ��Ƥ�����ˤϥƥ�ץ졼�ȥǡ����μ���
if (is_numeric($page_id) and $page_id != '') {
	$arrPageData = lfgetPageData(" page_id = ? " , array($page_id));

	if ($arrPageData[0]['tpl_dir'] === "") {
		$objPage->arrErr['page_id_err'] = "�� ���ꤵ�줿�ڡ������Խ��Ǥ��ޤ���";
		// ���̤�ɽ��
		$objView->assignobj($objPage);
		$objView->display(MAIN_FRAME);
		exit;
	}

	// �ƥ�ץ졼�ȥե����뤬¸�ߤ��Ƥ�����ɤ߹���
	$tpl_file = HTML_PATH . $arrPageData[0]['tpl_dir'] . $arrPageData[0]['filename'] . ".tpl";
	if (file_exists($tpl_file)){
		$arrPageData[0]['tpl_data'] = file_get_contents($tpl_file);		
	}

	// �����å��ܥå��������ѹ�
	$arrPageData[0]['header_chk'] = sfChangeCheckBox($arrPageData[0]['header_chk'], true);
	$arrPageData[0]['footer_chk'] = sfChangeCheckBox($arrPageData[0]['footer_chk'], true);

	// �ǥ��쥯�ȥ�����ɽ���Ѥ��Խ�
	$arrPageData[0]['directory'] = str_replace( USER_DIR,'', $arrPageData[0]['php_dir']);
	
	$objPage->arrPageData = $arrPageData[0];
}

// �ץ�ӥ塼����
if ($_POST['mode'] == 'preview') {
	
	$page_id_old = $page_id;
	$page_id = "0";
	$url = uniqid("");

	$_POST['page_id'] = $page_id;
	$_POST['url'] = $url;
	
	$arrPreData = lfgetPageData(" page_id = ? " , array($page_id));

	// tpl�ե�����κ��
	$del_tpl = USER_PATH . "templates/" . $arrPreData[0]['filename'] . '.tpl';
	if (file_exists($del_tpl)){
		unlink($del_tpl);	
	}

	// DB�إǡ����򹹿�����
	lfEntryPageData($_POST);

	// TPL�ե��������
	$cre_tpl = USER_PATH . "templates/" . $url . '.tpl';
	lfCreateFile($cre_tpl);
	
	// blocposition ����
	$objDBConn = new SC_DbConn;		// DB���֥�������
	$sql = 'delete from dtb_blocposition where page_id = 0';
	$ret = $objDBConn->query($sql);
	
	if ($page_id_old != "") {
		// ��Ͽ�ǡ��������
		$sql = "SELECT 0, target_id, bloc_id, bloc_row FROM dtb_blocposition WHERE page_id = ?";
		$ret = $objDBConn->getAll($sql,array($page_id_old));
		
		if (count($ret) > 0) {
			
			// blocposition ��ʣ��
			$sql = " insert into dtb_blocposition (";
			$sql .= "     page_id,";
			$sql .= "     target_id,";
			$sql .= "     bloc_id,";
			$sql .= "     bloc_row";
			$sql .= "     )values(?, ?, ?, ?)";
			
			// �������ʸINSERT�¹�
			foreach($ret as $key => $val){
				$ret = $objDBConn->query($sql,$val);
			}
		}

	}
	
	$_SESSION['preview'] = "ON";
	
	header("location: " . URL_DIR . "preview/index.php");
}

// �ǡ�����Ͽ����
if ($_POST['mode'] == 'confirm') {
	
	// ���顼�����å�
	$objPage->arrErr = lfErrorCheck($_POST);

	// ���顼���ʤ���й���������Ԥ�	
	if (count($objPage->arrErr) == 0) {

		// DB�إǡ����򹹿�����
		lfEntryPageData($_POST);
		
		// �١����ǡ����Ǥʤ���Хե������������PHP�ե�������������
		if (!lfCheckBaseData($page_id)) {
			// �ե�������
			lfDelFile($arrPageData[0]);
			
			// PHP�ե��������
			$cre_php = USER_PATH . $_POST['url'] . ".php";
			lfCreatePHPFile($cre_php);
		}

		// TPL�ե��������
		$cre_tpl = dirname(USER_PATH . "templates/" . $_POST['url']) . "/" . basename($_POST['url']) . '.tpl';

		lfCreateFile($cre_tpl);

		// �Խ���ǽ�ڡ����ξ��ˤΤ߽�����Ԥ�
		if ($arrPageData[0]['edit_flg'] != 2) {
			// ���������������Τ���˲��˥ڡ���ID���������
			$arrPageData = lfgetPageData(" url = ? " , array(USER_URL.$_POST['url'].".php"));
			$page_id = $arrPageData[0]['page_id'];
		}

		header("location: ./main_edit.php?page_id=$page_id&msg=on");
	}else{
		// ���顼����������ϻ��Υǡ�����ɽ������
		$objPage->arrPageData = $_POST;
		$objPage->arrPageData['header_chk'] = sfChangeCheckBox(sfChangeCheckBox($_POST['header_chk']), true);
		$objPage->arrPageData['footer_chk'] = sfChangeCheckBox(sfChangeCheckBox($_POST['footer_chk']), true);
		$objPage->arrPageData['directory'] = $_POST['url'];
		$objPage->arrPageData['filename'] = "";
	}
}

// �ǡ���������� �١����ǡ����Ǥʤ���Хե��������
if ($_POST['mode'] == 'delete' and 	!lfCheckBaseData($page_id)) {
	lfDelPageData($_POST['page_id']);
}

// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**************************************************************************************************************
 * �ؿ�̾	��lfEntryPageData
 * ��������	���֥�å�����򹹿�����
 * ����1	��$arrData  ������ �����ǡ���
 * �����	���������
 **************************************************************************************************************/
function lfEntryPageData($arrData){
	$objDBConn = new SC_DbConn;		// DB���֥�������
	$sql = "";						// �ǡ�������SQL������
	$ret = ""; 						// �ǡ���������̳�Ǽ��
	$arrUpdData = array();			// �����ǡ���������
	$arrChk = array();				// ��¾�����å���

	// �����ǡ�������
	$arrUpdData = lfGetUpdData($arrData);
	
	// �ǡ�����¸�ߤ��Ƥ��뤫�����å���Ԥ�
	if($arrData['page_id'] !== ''){
		$arrChk = lfgetPageData(" page_id = ?", array($arrData['page_id']));
	}

	// page_id ���� �㤷���� �ǡ�����¸�ߤ��Ƥ��ʤ����ˤ�INSERT��Ԥ�
	if ($arrData['page_id'] === '' or !isset($arrChk[0])) {
		// SQL����
		$sql = " INSERT INTO dtb_pagelayout ";
		$sql .= " ( ";
		$sql .= " 	  page_name";
		$sql .= "	  ,url";
		$sql .= "	  ,php_dir";
		$sql .= "	  ,tpl_dir";
		$sql .= "	  ,filename";
		$sql .= "	  ,header_chk";
		$sql .= "	  ,footer_chk";
		$sql .= "	  ,update_url";
		$sql .= "	  ,create_date";
		$sql .= "	  ,update_date";
		$sql .= " ) VALUES ( ?,?,?,?,?,?,?,?,now(),now() )";
		$sql .= " ";
	}else{
		// �ǡ�����¸�ߤ��Ƥ���ˤϥ��åץǡ��Ȥ�Ԥ�
		// SQL����
		$sql = " UPDATE dtb_pagelayout ";
		$sql .= " SET";
		$sql .= "	  page_name = ? ";
		$sql .= "	  ,url = ? ";
		$sql .= "	  ,php_dir = ? ";
		$sql .= "	  ,tpl_dir = ? ";
		$sql .= "	  ,filename = ? ";
		$sql .= "	  ,header_chk = ? ";
		$sql .= "	  ,footer_chk = ? ";
		$sql .= "	  ,update_url = ? ";
		$sql .= "     ,update_date = now() ";
		$sql .= " WHERE page_id = ?";
		$sql .= " ";

		// �����ǡ����˥֥�å�ID���ɲ�
		array_push($arrUpdData, $arrData['page_id']);
	}

	// SQL�¹�
	$ret = $objDBConn->query($sql,$arrUpdData);
	
	return $ret;
}

/**************************************************************************************************************
 * �ؿ�̾	��lfGetUpdData
 * ��������	��DB�ع�����Ԥ��ǡ�������������
 * ����1	��$arrData  ������ �����ǡ���
 * �����	�������ǡ���
 **************************************************************************************************************/
function lfGetUpdData($arrData){
	
	// �١����ǡ����ξ��ˤ��ѹ����ʤ���
	if (lfCheckBaseData($arrData['page_id'])) {
		$arrPageData = lfgetPageData( ' page_id = ? ' , array($arrData['page_id']));

		$name = $arrPageData[0]['page_name'] ;
		$url = $arrPageData[0]['url'];
		$php_dir = $arrPageData[0]['php_dir'];
		$tpl_dir = $arrPageData[0]['tpl_dir'];
		$filename = $arrPageData[0]['filename'];
	}else{
		$name = $arrData['page_name'] ;
		$url = USER_URL.$arrData['url'].".php";
		$php_dir = dirname(USER_DIR.$arrData['url'])."/";
		$tpl_dir = dirname(USER_DIR."templates/".$arrData['url'])."/";
		$filename = basename($arrData['url']);
	}

	// �����ǡ�������κ���
	$arrUpdData = array(
					$name										// ̾��	
					,$url										// URL
					,$php_dir									// PHP�ǥ��쥯�ȥ�
					,$tpl_dir									// TPL�ǥ��쥯�ȥ�
					,$filename									// �ե�����̾
					,sfChangeCheckBox($arrData['header_chk'])	// �إå�������
					,sfChangeCheckBox($arrData['footer_chk'])	// �եå�������
					,$_SERVER['HTTP_REFERER']					// ����URL
					);
					
	return $arrUpdData;
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
	$objErr->doFunc(array("̾��", "page_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("URL", "url", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));

	// URL�����å�
	if (substr(strrev(trim($array['url'])),0,1) == "/") {
		$objErr->arrErr['url'] = "�� URL�����������Ϥ��Ƥ���������<br />";
	}
	
	$check_url = USER_URL . $array['url'] . ".php";
	if( strlen($array['url']) > 0 && !ereg( "^https?://+($|[a-zA-Z0-9_~=&\?\.\/-])+$", $check_url ) ) {
		$objErr->arrErr['url'] = "�� URL�����������Ϥ��Ƥ���������<br />";
	}

	// Ʊ���URL��¸�ߤ��Ƥ�����ˤϥ��顼
	if(!isset($objErr->arrErr['url']) and $array['url'] !== ''){
		$arrChk = lfgetPageData(" url = ? " , array(USER_URL . $array['url'].".php"));

		if (count($arrChk[0]) >= 1 and $arrChk[0]['page_id'] != $array['page_id']) {
			$objErr->arrErr['url'] = '�� Ʊ��URL�Υǡ�����¸�ߤ��Ƥ��ޤ����̤�URL���դ��Ƥ���������';
		}
	}
	
	return $objErr->arrErr;
}

/**************************************************************************************************************
 * �ؿ�̾	��lfCreateFile
 * ��������	���ե�������������
 * ����1	��$path�������ƥ�ץ졼�ȥե�����Υѥ�
 * �����	���ʤ�
 **************************************************************************************************************/
function lfCreateFile($path){
	
	// �ǥ��쥯�ȥ꤬¸�ߤ��Ƥ��ʤ���к�������		
	if (!is_dir(dirname($path))) {
		mkdir(dirname($path));
	}

	// �ե��������
	$fp = fopen($path,"w");
	fwrite($fp, $_POST['tpl_data']);
	fclose($fp);
}

/**************************************************************************************************************
 * �ؿ�̾	��lfCreatePHPFile
 * ��������	��PHP�ե�������������
 * ����1	��$path������PHP�ե�����Υѥ�
 * �����	���ʤ�
 **************************************************************************************************************/
function lfCreatePHPFile($path){

	// php��¸��ǥ��쥯�ȥ꤬¸�ߤ��Ƥ��ʤ���к�������
	if (!is_dir(dirname($path))) {
		mkdir(dirname($path));
	}
	
	// �١����Ȥʤ�PHP�ե�������ɤ߹���
	if (file_exists(USER_DEF_PHP)){
		$php_data = file_get_contents(USER_DEF_PHP);		
	}
	
	// require.php�ξ���񤭴�����
	$php_data = str_replace("###require###", HTML_PATH . "require.php", $php_data);
	
	// php�ե�����κ���
	$fp = fopen($path,"w");
	fwrite($fp, $php_data);
	fclose($fp);
}
