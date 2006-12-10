<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");
require_once(DATA_PATH . "module/Tar.php");
require_once(DATA_PATH . "include/file_manager.inc");
require_once(DATA_PATH . "module/SearchReplace.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;
	var $arrSubnavi = array(
		'title' => array(
			1 => 'top',
			2 => 'product',
			3 => 'detail',
			4 => 'mypage' 
		),
		'name' =>array(
			1 => 'TOP�ڡ���',
			2 => '���ʰ����ڡ���',
			3 => '���ʾܺ٥ڡ���',
			4 => 'MY�ڡ���' 
		)
	);

	function LC_Page() {
		$this->tpl_mainpage = 'design/template.tpl';
		$this->tpl_subnavi = 'design/subnavi.tpl';
		$this->tpl_subno = 'template';
		$this->tpl_subno_template = $this->arrSubnavi['title'][1];
		$this->tpl_TemplateName = $this->arrTemplateName['name'][1];
		$this->tpl_mainno = "design";
		$this->tpl_subtitle = '�ƥ�ץ졼������';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// GET���ͤ�������
$get_tpl_subno_template = $_GET['tpl_subno_template'];

// GET���ͤ������Ƥ�����ˤϤ����ͤ򸵤˲���ɽ�����ڤ��ؤ���
if ($get_tpl_subno_template != ""){
	// �����Ƥ����ͤ��������Ͽ����Ƥ��ʤ����TOP��ɽ��
	if (in_array($get_tpl_subno_template,$objPage->arrSubnavi['title'])){
		$tpl_subno_template = $get_tpl_subno_template;
	}else{
		$tpl_subno_template = $objPage->arrSubnavi['title'][1];
	}
} else {
	// GET���ͤ��ʤ����POST���ͤ���Ѥ���
	if ($_POST['tpl_subno_template'] != ""){
		$tpl_subno_template = $_POST['tpl_subno_template'];
	}else{
		$tpl_subno_template = $objPage->arrSubnavi['title'][1];
	}
}
$objPage->tpl_subno_template = $tpl_subno_template;
$key = array_keys($objPage->arrSubnavi['title'], $tpl_subno_template);
$objPage->template_name = $objPage->arrSubnavi['name'][$key[0]];

// ��Ͽ�򲡤��줿�Ф��ˤ�DB�إǡ����򹹿��˹Ԥ�
switch($_POST['mode']) {
case 'confirm':
	// DB�إǡ�������
	lfUpdData();
	
	// �ƥ�ץ졼�Ȥξ��
	lfChangeTemplate();
	
	// ��λ��å�����
	$objPage->tpl_onload="alert('��Ͽ����λ���ޤ�����');";
	break;
case 'download':
	lfDownloadTemplate($_POST['check_template']);
	break;
default:
	break;
}

// POST�ͤΰ����Ѥ�
$objPage->arrForm = $_POST;

// ��������
$tpl_arrTemplate = array();
$objPage->arrTemplate = lfgetTemplate();

// �ǥե���ȥ����å�����
$objPage->MainImage = $objPage->arrTemplate['check'];
$objPage->arrTemplate['check'] = array($objPage->arrTemplate['check']=>"check");

// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

/**************************************************************************************************************
 * �ؿ�̾	��lfgetTemplate
 * ��������	�����̤�ɽ������������������
 * ����		���ʤ�
 * �����	�����̤�ɽ���������(����)
 **************************************************************************************************************/
function lfgetTemplate(){
	global $objPage;
	$filepath = "user_data/templates/";
	
	$arrTemplateImage = array();	// ����ɽ��������Ǽ��
	$Image = "";					// ���᡼������������̾��Ǽ��
	$disp = "";
	$arrDefcheck = array();			// radio�ܥ���Υǥե���ȥ����å���Ǽ��
	
	// DB���鸽�����򤵤�Ƥ���ǡ�������
	$arrDefcheck = lfgetTemplaeBaseData();
	
	// �ƥ�ץ졼�ȥǡ������������
	$objQuery = new SC_Query();
	$sql = "SELECT template_code,template_name FROM dtb_templates ORDER BY create_date DESC";
	$arrTemplate = $objQuery->getall($sql);
	
	switch($objPage->tpl_subno_template) {
		// TOP
		case $objPage->arrSubnavi['title'][1]:
			$Image = "TopImage.jpg";			// ���᡼������������̾��Ǽ��
			$disp = $objPage->arrSubnavi['title'][1];
			break;
			
		// ���ʰ���
		case $objPage->arrSubnavi['title'][2]:
			$Image = "ProdImage.jpg";			// ���᡼������������̾��Ǽ��
			$disp = $objPage->arrSubnavi['title'][2];
			break;
			
		// ���ʾܺ�
		case $objPage->arrSubnavi['title'][3]:
			$Image = "DetailImage.jpg";			// ���᡼������������̾��Ǽ��
			$disp = $objPage->arrSubnavi['title'][3];
			break;
			
		// MY�ڡ���
		case $objPage->arrSubnavi['title'][4]:
			$Image = "MypageImage.jpg";			//���᡼������������̾��Ǽ��
			$disp = $objPage->arrSubnavi['title'][4];
			break;
	}

	// ����ɽ���������
	foreach($arrTemplate as $key => $val){
		$arrTemplateImage['image'][$val['template_code']] = $filepath . $val['template_code'] . "/" . $Image;
		$arrTemplateImage['code'][$key] = $val['template_code'];
	}
	
	// ��������å�
	if (isset($arrDefcheck[$disp])){
		$arrTemplateImage['check'] = $arrDefcheck[$disp];
	}else{
		$arrTemplateImage['check'] = 1;
	}
	
	return $arrTemplateImage;
}

/**************************************************************************************************************
 * �ؿ�̾	��lfgetTemplaeBaseData
 * ��������	��DB����¸����Ƥ���ƥ�ץ졼�ȥǡ������������
 * ����		���ʤ�
 * �����	��DB����¸����Ƥ���ƥ�ץ졼�ȥǡ���(����)
 **************************************************************************************************************/
function lfgetTemplaeBaseData(){
	$objDBConn = new SC_DbConn;		// DB���֥�������
	$sql = "";						// �ǡ�������SQL������
	$arrRet = array();				// �ǡ���������
	
	$sql = "SELECT top_tpl AS top, product_tpl AS product, detail_tpl AS detail, mypage_tpl AS mypage FROM dtb_baseinfo";
	$arrRet = $objDBConn->getAll($sql);
	
	return $arrRet[0];
}

/**************************************************************************************************************
 * �ؿ�̾	��lfUpdData
 * ��������	��DB�˥ǡ�������¸����
 * ����		���ʤ�
 * �����	������ TRUE�����顼 FALSE
 **************************************************************************************************************/
function lfUpdData(){
	global $objPage;
	$objDBConn = new SC_DbConn;		// DB���֥�������
	$sql = "";						// �ǡ�������SQL������
	$arrRet = array();				// �ǡ���������(����Ƚ��)

	// �ǡ�������	
	$sql = "SELECT top_tpl AS top, product_tpl AS product, detail_tpl AS detail, mypage_tpl AS mypage FROM dtb_baseinfo";
	$arrRet = $objDBConn->getAll($sql);

	$chk_tpl = $_POST['check_template'];
	// �ǡ����������Ǥ��ʤ����INSERT���Ǥ����UPDATE
	if (isset($arrRet[0])){
		// UPDATE
		$arrVal = $arrRet[0];
		
		// TOP���ѹ��������ˤ��������ѹ�
		if ($objPage->tpl_subno_template == $objPage->arrSubnavi['title'][1]){
			$arrVal = array($chk_tpl,$chk_tpl,$chk_tpl,$chk_tpl);
		}else{
			$arrVal[$objPage->tpl_subno_template] = $chk_tpl;
		}
		$sql= "update dtb_baseinfo set top_tpl = ?, product_tpl = ?, detail_tpl = ?, mypage_tpl = ?, update_date = now()";
	}else{
		// INSERT
		$arrVal = array(null,null,null,null);
		
		// TOP���ѹ��������ˤ��������ѹ�
		if ($objPage->tpl_subno_template == $objPage->arrSubnavi['title'][1]){
			$arrVal = array($chk_tpl,$chk_tpl,$chk_tpl,$chk_tpl);
		}else{
			$arrVal[$chk_tpl-1] =$chk_tpl;
		}
		$sql= "insert into dtb_baseinfo (top_tpl,product_tpl,detail_tpl,mypage_tpl, update_date) values (?,?,?,?,now());";
	}

	// SQL�¹�	
	$arrRet = $objDBConn->query($sql,$arrVal);
	
	return $arrRet;
}

/**************************************************************************************************************
 * �ؿ�̾	��lfChangeTemplate
 * ��������	���ƥ�ץ졼�ȥե�������񤭤���
 * ����		���ʤ�
 * �����	������ TRUE�����顼 FALSE
 **************************************************************************************************************/
function lfChangeTemplate(){
	global $objPage;
	$tpl_path = USER_PATH . "templates/";
	$inc_path = USER_PATH . "include/";
	$css_path = USER_PATH . "css/";
	
	$tpl_name = "";
	$tpl_element = "";
	
	$chk_tpl = $_POST['check_template'];
	$path = $tpl_path . $chk_tpl . "/";
	$taget_tpl_path = $path . "/templates/";
	$taget_inc_path = $path . "/include/";
	$taget_css_path = $path . "/css/";
	$save_tpl_path = $tpl_path;
	
	switch($objPage->tpl_subno_template) {
		// TOP
		case $objPage->arrSubnavi['title'][1]:
			$tpl_element = "TopTemplate";			// ���᡼������������̾��Ǽ��
			$tpl_name = "top.tpl";
			break;
			
		// ���ʰ���
		case $objPage->arrSubnavi['title'][2]:
			$tpl_element = "ProdTemplate";			// ���᡼������������̾��Ǽ��
			$tpl_name = "list.tpl";
			break;
			
		// ���ʾܺ�
		case $objPage->arrSubnavi['title'][3]:
			$tpl_element = "DetailTemplate";			// ���᡼������������̾��Ǽ��
			$tpl_name = "detail.tpl";
			break;
			
		// MY�ڡ���
		case $objPage->arrSubnavi['title'][4]:
			$tpl_element = "MypageTemplate";			//���᡼������������̾��Ǽ��
			$tpl_name = "mypage.tpl";
			break;

		default:
			break;
	}

	// �����ѥ���񤭴���
	$img_path = '<!--{$smarty.const.URL_DIR}-->img/';
	$displace_path = '<!--{$smarty.const.URL_DIR}-->'. USER_DIR . 'templates/' . $chk_tpl . '/img/';
	$fs = new File_SearchReplace($img_path, $displace_path, "", $path, true); 
	$fs->doSearch(); 
	
	// TOP���ѹ��������ˤ��������ѹ�
	if ($objPage->tpl_subno_template == $objPage->arrSubnavi['title'][1]){
		// �ƥ�ץ졼�ȥե�����򥳥ԡ�
		copy($taget_tpl_path . "top.tpl", $save_tpl_path . "top.tpl");
		copy($taget_tpl_path . "list.tpl", $save_tpl_path . "list.tpl");
		copy($taget_tpl_path . "detail.tpl", $save_tpl_path . "detail.tpl");

		// mypage�ϥե�������ȥ��ԡ�
		lfFolderCopy($taget_tpl_path."mypage/", $save_tpl_path . "mypage/");

		// �֥�å��ǡ����Υ��ԡ�
		lfFolderCopy($taget_inc_path."bloc/", $inc_path . "bloc/");

		// �إå���,�եå������ԡ�
		copy($taget_inc_path . "header.tpl", $inc_path . "header.tpl");
		copy($taget_inc_path . "footer.tpl", $inc_path . "footer.tpl");
		
		// CSS�ե�����Υ��ԡ�
		copy($taget_css_path . "contents.css", $css_path . "contents.css");

	// mypage�ξ��ˤϥե�������ȥ��ԡ�����
	}elseif($objPage->tpl_subno_template == $objPage->arrSubnavi['title'][4]){
		lfFolderCopy($taget_tpl_path."mypage/", $save_tpl_path."mypage/");
	}else{
		// �ƥ�ץ졼�ȥե�����򥳥ԡ�
		copy($taget_tpl_path . $tpl_name, $save_tpl_path . $tpl_name);
	}

	// �����ѥ��򸵤��᤹	
	$fs = new File_SearchReplace($displace_path, $img_path, "", $path, true); 
	$fs->doSearch(); 
}

/**************************************************************************************************************
 * �ؿ�̾	��lfDownloadTemplate
 * ��������	���ƥ�ץ졼�ȥե����밵�̤��ƥ�������ɤ���
 * ����1	���ƥ�ץ졼�ȥ�����
 * �����	���ʤ�
 **************************************************************************************************************/
function lfDownloadTemplate($template_code){
	$filename = $template_code. ".tar.gz";
	$dl_file = USER_TEMPLATE_PATH.$filename;
	
	// IMG�ե�����򥳥ԡ�
	$mess = "";
	$mess = sfCopyDir(HTML_PATH."img/", USER_TEMPLATE_PATH.$template_code."/img/", $mess);
	
	// �ե�����ΰ���
	$tar = new Archive_Tar($dl_file, TRUE);
	// �ե������������
	$arrFileHash = sfGetFileList(USER_TEMPLATE_PATH.$template_code);
	foreach($arrFileHash as $val) {
		$arrFileList[] = $val['file_name'];
	}
	// �ǥ��쥯�ȥ���ư
	chdir(USER_TEMPLATE_PATH.$template_code);
	
	//���̤򤪤��ʤ�
	$zip = $tar->create($arrFileList);
		
	// ��������ɳ���
	Header("Content-disposition: attachment; filename=${filename}");
	Header("Content-type: application/octet-stream; name=${dl_file}");
	header("Content-Length: " .filesize($dl_file)); 
	readfile ($dl_file);
	// ���̥ե�������
	unlink($dl_file);
	
	exit();
}

/**************************************************************************************************************
 * �ؿ�̾	��lfFolderCopy
 * ��������	���ե�����򥳥ԡ�����
 * ����1	�����ԡ����ѥ�
 * ����2���������ԡ���ѥ�
 * �����	���ʤ�
 **************************************************************************************************************/
function lfFolderCopy($taget_path, $save_path){

	// �ե������Υե�������������
	$arrMypage=glob($taget_path . "*" );
	
	// �ե�������ʤ���к�������
	if(!is_dir($save_path)){
		mkdir($save_path);
	}

	// �ե����������ƥ��ԡ�
	foreach($arrMypage as $key => $val){
		$matches = array();
		mb_ereg("^(.*[\/])(.*)",$val, $matches);
		$data=$matches[2];
		copy($val, $save_path . $data);
	}
}
