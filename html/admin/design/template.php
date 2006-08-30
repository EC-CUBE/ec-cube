<?php

require_once("../../require.php");

class LC_Page {
	var $arrForm;
	var $arrHidden;
	var $arrSubnavi = array(
		1 => 'top',
		2 => 'product',
		3 => 'detail',
		4 => 'mypage',
	);

	function LC_Page() {
		$this->tpl_mainpage = 'design/template.tpl';
		$this->tpl_subnavi = 'design/subnavi.tpl';
		$this->tpl_subno = 'template';
		$this->tpl_subno_template = $this->arrSubnavi[1];
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
	if (in_array($get_tpl_subno_template,$objPage->arrSubnavi)){
		$tpl_subno_template = $get_tpl_subno_template;
	}else{
		$tpl_subno_template = $objPage->arrSubnavi[1];
	}
} else {
	// GET���ͤ��ʤ����POST���ͤ���Ѥ���
	if ($_POST['tpl_subno_template'] != ""){
		$tpl_subno_template = $_POST['tpl_subno_template'];
	}else{
		$tpl_subno_template = $objPage->arrSubnavi[1];
	}
}
$objPage->tpl_subno_template = $tpl_subno_template;

// ��Ͽ�򲡤��줿�Ф��ˤ�DB�إǡ����򹹿��˹Ԥ�
if ($_POST['mode'] == "confirm"){
	// DB�إǡ�������
	lfUpdData();
	
	// �ƥ�ץ졼�Ȥξ��
	lfChangeTemplate();
	
	sfprintr($_POST);
}

// POST�ͤΰ����Ѥ�
$objPage->arrForm = $_POST;

// ��������
$tpl_arrTemplate = array();
$objPage->arrTemplate = lfgetTemplate();

// �ǥե���ȥ����å�����
$objPage->MainImage = $objPage->arrTemplate['check'];
$objPage->arrTemplate['check'] = array($objPage->arrTemplate['check']=>"check");

sfprintr($objPage->arrTemplate);

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
	$filepath = "/test/kakinaka/";
	
	$arrTemplateImage = array();	// ����ɽ��������Ǽ��
	$Image = "";					// ���᡼������������̾��Ǽ��
	$disp = "";
	$arrDefcheck = array();			// radio�ܥ���Υǥե���ȥ����å���Ǽ��
	
	// DB���鸽�����򤵤�Ƥ���ǡ�������
	$arrDefcheck = lfgetTemplaeBaseData();
	
	// �ƥ�ץ졼�ȥǡ������������
	$objQuery = new SC_Query();
	$sql = "SELECT template_code,template_name,file_path FROM dtb_template ORDER BY create_date DESC";
	$arrTemplate = $objQuery->getall($sql);
	
	switch($objPage->tpl_subno_template) {
		// TOP
		case $objPage->arrSubnavi[1]:
			$Image = "TopImage.jpg";			// ���᡼������������̾��Ǽ��
			$disp = $objPage->arrSubnavi[1];
			break;
			
		// ���ʰ���
		case $objPage->arrSubnavi[2]:
			$Image = "ProdImage.jpg";			// ���᡼������������̾��Ǽ��
			$disp = $objPage->arrSubnavi[2];
			break;
			
		// ���ʾܺ�
		case $objPage->arrSubnavi[3]:
			$Image = "DetailImage.jpg";			// ���᡼������������̾��Ǽ��
			$disp = $objPage->arrSubnavi[3];
			break;
			
		// MY�ڡ���
		case $objPage->arrSubnavi[4]:
			$Image = "MypageImage.jpg";			//���᡼������������̾��Ǽ��
			$disp = $objPage->arrSubnavi[4];
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
		if ($objPage->tpl_subno_template == $objPage->arrSubnavi[1]){
			$arrVal = array($chk_tpl,$chk_tpl,$chk_tpl,$chk_tpl);
		}else{
			$arrVal[$objPage->tpl_subno_template] = $chk_tpl;
		}
		$sql= "update dtb_baseinfo set top_tpl = ?, product_tpl = ?, detail_tpl = ?, mypage_tpl = ?, update_date = now()";
	}else{
		// INSERT
		$arrVal = array(null,null,null,null);
		
		// TOP���ѹ��������ˤ��������ѹ�
		if ($objPage->tpl_subno_template == $objPage->arrSubnavi[1]){
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
	$tpl_path = "html/test/kakinaka/";
	
	$tpl_name = "";
	$tpl_element = "";
	
	$chk_tpl = $_POST['check_template'];
	
	// �ƥ�ץ졼�ȥǡ������������
	$objQuery = new SC_Query();
	$sql = "SELECT template_code,template_name,file_path FROM dtb_template WHERE template_code = ?";
	$arrTemplate = $objQuery->getall($sql, array($chk_tpl));	
	
	switch($objPage->tpl_subno_template) {
		// TOP
		case $objPage->arrSubnavi[1]:
			$tpl_element = "TopTemplate";			// ���᡼������������̾��Ǽ��
			$tpl_name = "top.tpl";
			break;
			
		// ���ʰ���
		case $objPage->arrSubnavi[2]:
			$tpl_element = "ProdTemplate";			// ���᡼������������̾��Ǽ��
			$tpl_name = "product.tpl";
			break;
			
		// ���ʾܺ�
		case $objPage->arrSubnavi[3]:
			$tpl_element = "DetailTemplate";			// ���᡼������������̾��Ǽ��
			$tpl_name = "detail.tpl";
			break;
			
		// MY�ڡ���
		case $objPage->arrSubnavi[4]:
			$tpl_element = "MypageTemplate";			//���᡼������������̾��Ǽ��
			$tpl_name = "mypage.tpl";
			break;

		default:
			break;
	}
	
	$taget_tpl_path = ROOT_DIR . $tpl_path . $arrTemplate[0]['template_code'] . "/";
	$save_tpl_path = ROOT_DIR . $tpl_path;
	
	// TOP���ѹ��������ˤ��������ѹ�
	if ($objPage->tpl_subno_template == $objPage->arrSubnavi[1]){
		// �ƥ�ץ졼�ȥե�����򥳥ԡ�
		copy($taget_tpl_path . "/top.tpl", $save_tpl_path . "top.tpl");
		copy($taget_tpl_path . "/list.tpl", $save_tpl_path . "list.tpl");
		copy($taget_tpl_path . "/detail.tpl", $save_tpl_path . "detail.tpl");
		
		// MYPAGE�Υե�������������
		$arrMypage=glob($taget_tpl_path."mypage/" . "*" );
		
		// �ե�������ʤ���к�������
		if(!is_dir($save_tpl_path."mypage")){
			mkdir($save_tpl_path."mypage");
		}
		
		foreach($arrMypage as $key => $val){
			$matches = array();
			mb_ereg("^(.*[\/])(.*)",$val, $matches);
			$data=$matches[2];
			
			sfprintr($matches);
			copy($val, $save_tpl_path . "mypage/" . $data);
		}

		// �֥�å��ǡ����Υ��ԡ�
	}else{
		// �ƥ�ץ졼�ȥե�����򥳥ԡ�
		copy($tpl_path . "/" . $tpl_name, $save_tpl_path . $tpl_name);
	}
}
