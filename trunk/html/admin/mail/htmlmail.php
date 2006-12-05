<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	
	var $arrForm;
	
	var $arrTempProduct;
	var $subProductNum;
	var $arrFileName;
	
	
	function LC_Page() {
		$this->tpl_mainpage = 'mail/htmlmail.tpl';
		$this->tpl_mainno = 'mail';
		$this->tpl_subnavi = 'mail/subnavi.tpl';
		$this->tpl_subno = "template";
	}
}


class LC_Products{
	
	var $conn;
	var $arrProduct;
	var $arrProductKey;
	
	function LC_Products ($conn=""){
		
		$DB_class_name = "SC_DbConn";
		if ( is_object($conn)){
			if ( is_a($conn, $DB_class_name)){
				// $conn��$DB_class_name�Υ��󥹥��󥹤Ǥ���
				$this->conn = $conn;
			}
		} else {
			if (class_exists($DB_class_name)){
				//$DB_class_name�Υ��󥹥��󥹤��������
				$this->conn = new SC_DbConn();			
			}
		}
	}
	
	function setProduct($keyname, $id) {
		
		if ( sfCheckNumLength($id) ){
			$result = $this->getProductData($id);
		}
		
		if ( $result && (in_array($keyname, $this->arrProductKey) ) ){
	
			$this->arrProduct["${keyname}"] = $result;
		}
	}	
	
	function getProductData($id){
		$conn = $this->conn;
		// ���ʾ�����������
		$sql = "SELECT * FROM dtb_products WHERE product_id = ?";
		$result = $conn->getAll($sql, array($id));
		if ( is_array($result) ){
			$return = $result[0];
		}
		return $return;	
	}

	function getProductImageData($id){
		$conn = $this->conn;
		// ���ʲ���������������
		$sql = "SELECT main_image FROM dtb_products WHERE product_id = ?";
		$result = $conn->getAll($sql, array($id));
		if ( is_array($result) ){
			$return = $result[0]["main_image"];
		}
		return $return;	
	}
	function setHiddenList($arrPOST) {
		foreach($this->arrProductKey as $val) {
			$key = "temp_" . $val;
			if($arrPOST[$key] != "") {
				$this->setProduct($val, $arrPOST[$key]);
			}
		}
	}
}

// ��Ͽ�����
$arrRegist = array(
					  "subject", "charge_image", "mail_method", "header", "main_title", "main_comment", "main_product_id", "sub_title", "sub_comment"
					, "sub_product_id01", "sub_product_id02", "sub_product_id03", "sub_product_id04", "sub_product_id05", "sub_product_id06", "sub_product_id07"
					, "sub_product_id08", "sub_product_id09", "sub_product_id10", "sub_product_id11", "sub_product_id12"
					);
					
// ��¸����Ͽ�Ѥ߾��ʤ������ɽ����ɬ�פȤ�����ܥꥹ��					
$arrFileList = array(
						"main_product_id", "sub_product_id01", "sub_product_id02", "sub_product_id03", "sub_product_id04", "sub_product_id05"
						, "sub_product_id06", "sub_product_id07", "sub_product_id08", "sub_product_id09", "sub_product_id10", "sub_product_id11", "sub_product_id12"
					);

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);


// �����������饹����
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
$objUpFile->addFile("�᡼��ô���̿�", 'charge_image', array('jpg'),IMAGE_SIZE, true, HTMLMAIL_IMAGE_WIDTH, HTMLMAIL_IMAGE_HEIGHT);

// POST�ͤΰ��Ѥ�&�����ͤ��Ѵ�
$objPage->arrForm = lfConvData($_POST);

// Hidden����Υǡ���������Ѥ�
$objUpFile->setHiddenFileList($_POST);

switch ($_POST['mode']){
	
	//�������åץ���
	case 'upload_image':
	// ������¸����
	$objPage->arrErr[$_POST['image_key']] = $objUpFile->makeTempFile($_POST['image_key']);
	break;
	
	//��ǧ
	case 'confirm':
	
	// ���顼�����å�
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	//�ե�����¸�ߥ����å�
	$objPage->arrErr = array_merge((array)$objPage->arrErr, (array)$objUpFile->checkEXISTS());
		
	//���顼�ʤ��ξ�硢��ǧ�ڡ�����
	 if (!$objPage->arrErr){
		// 	���åץ��ɥե��������������Ϥ���
		$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
		//����׵�Τ��ä�������ɽ�����ʤ�
		for($i = 1; $i <= HTML_TEMPLATE_SUB_MAX; $i++) {
			if($_POST['delete_sub'.$i] == "1") {
				$arrSub['delete'][$i] = "on";
			}else{
				$arrSub['delete'][$i] = "";
			}
		}
		$objPage->arrSub = $arrSub;
		$objPage->tpl_mainpage = 'mail/htmlmail_confirm.tpl';
	 }
	break;
	
	// ��ǧ�ڡ�����������
	case 'return':
	break;
	
	//���ƥ�ץ졼����Ͽ
	case 'complete':
	// �����ͤ��Ѵ�
	$objPage->arrForm = lfConvData($_POST);
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);	// ���ϥ��顼�����å�

	// ���åץ��ɲ����򥻡��֥ǥ��쥯�ȥ�˰ܹ�
	$objUpFile->moveTempFile();

	// DB��Ͽ
	if (is_numeric($objPage->arrForm["template_id"])) {	//���Խ���
		lfUpdateData($arrRegist);
	} else {
		ifRegistData($arrRegist);
	}
	$objPage->tpl_mainpage = 'mail/htmlmail_complete.tpl';
	break;
}

// ������̤�����Խ���
if ($_GET["mode"] == "edit" && is_numeric($_GET["template_id"])) {
	$objPage->edit_mode = "on";
	//�ƥ�ץ졼�Ⱦ����ɤ߹���
	lfSetRegistData($_GET["template_id"]);
	// DB�ǡ�����������ե�����̾���ɹ�
	$objUpFile->setDBFileList($objPage->arrForm);

}

if ($_GET['mode'] != 'edit'){
//��Ͽ������ɤ߹���
$objPage->arrFileName = lfGetProducts();
}

// HIDDEN�Ѥ�������Ϥ���
$objPage->arrHidden = array_merge((array)$objPage->arrHidden, (array)$objUpFile->getHiddenFileList());
// ���åץ��ɥե��������������Ϥ���
$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-------------------------------------------------------------------------------------------------------------------------

/* ���ʲ������ɤ߹��� */
function lfGetProducts() {
	global $objQuery;
	
	if ($_POST['main_product_id'] != ""){
	$MainFile = $objQuery->select("main_image, name, product_id", "dtb_products", "product_id=?", array($_POST['main_product_id']));
	$arrFileName[0] = $MainFile[0];
	}
	for($i = 1; $i <= HTML_TEMPLATE_SUB_MAX; $i++) {
		$sub_keyname = "sub_product_id" . sprintf("%02d", $i);
		if($_POST[$sub_keyname] != "") {
			$arrSubFile = $objQuery->select("main_image, name, product_id", "dtb_products", "product_id = ?", array($_POST[$sub_keyname]));
			$arrFileName[$i] = $arrSubFile[0];
		}
	}
	return $arrFileName;
}

/* ��Ͽ�Ѥߥǡ����ɤ߹��� */
function lfSetRegistData($template_id) {
	global $objQuery;
	global $objPage;
	$arrRet = $objQuery->select("*", "dtb_mailmaga_template", "template_id=?", array($template_id));
	$arrProductid = $arrRet[0];
	//�����ʳ��ξ������
	$objPage->arrForm = $arrRet[0];
		if ($arrProductid['main_product_id'] != ""){
			$MainFile = $objQuery->select("main_image, name, product_id", "dtb_products", "product_id=?", array($arrProductid['main_product_id']));
			$arrFileName[0] = $MainFile[0];
		}
	for ($i=1; $i<=HTML_TEMPLATE_SUB_MAX; $i++){
		if ($arrProductid['sub_product_id'.sprintf("%02d", $i)] != ""){
			$arrSubFile = $objQuery->select("main_image, name, product_id", "dtb_products", "product_id=?", array($arrProductid['sub_product_id'.sprintf("%02d", $i)]));
			$arrFileName[$i] = $arrSubFile[0];
		}
	}
	//�����ξ������
	$objPage->arrFileName = $arrFileName;
	
	return $objPage;
}

// �Խ��ǡ�������
function lfGetEditData($id, $arrIdData) {
	global $conn;

	// DB��Ͽ����
	$sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ? AND del_flg = 0";
	$result = $conn->getAll($sql, array($id));

	//�������ե�����̾
	for ($i = 0; $i < count($arrIdData); $i ++) {
		$data = "";
		if (is_numeric($result[0][ $arrIdData[$i] ]) ) {
			$sql = "SELECT name,product_id,main_image FROM dtb_products WHERE product_id = ?";
			$data = $conn->getAll($sql, array($result[0][ $arrIdData[$i] ]));
		}
		$arrFileName[] = $data[0];
	}
 	
	return array($result[0], $arrFileName);
}

// ��ǧ�ǡ�������
function lfGetConfirmData($arrPOST, $arrIdData) {
	global $conn;
	//�������ե�����̾
	for ($i = 0; $i < count($arrIdData); $i ++) {
		$data = "";
		if (is_numeric($arrPOST[ $arrIdData[$i] ]) ) {
			$sql = "SELECT name,product_id,main_image FROM dtb_products WHERE product_id = ?";
			$data = $conn->getAll($sql, array($arrPOST[ $arrIdData[$i] ]));
		}
		$arrFileName[] = $data[0];
	}
 	return array($arrPOST, $arrFileName);
}

// �ǡ����١�����Ͽ
function ifRegistData($arrRegist) {
	global $conn;
	global $objUpFile;

	foreach ($arrRegist as $data) {
		if (strlen($_POST[$data]) > 0) {
			$arrRegistValue[$data] = $_POST[$data];
		}
	}
	$arrRegistValue["creator_id"] = $_SESSION["member_id"];		// ��Ͽ��ID�ʴ������̡�
	$uploadfile = $objUpFile->getDBFileList();
	//����׵�Τ��ä����ʤ�������
	for ($i = 1; $i <= HTML_TEMPLATE_SUB_MAX; $i++){
		if ($_POST['delete_sub'.$i] == '1'){
			$arrRegistValue['sub_product_id'.sprintf("%02d", $i)] = NULL;
		}
	}
	$arrRegistValue = array_merge($arrRegistValue, $uploadfile);
	$conn->autoExecute("dtb_mailmaga_template", $arrRegistValue);
}

// �ǡ�������
function lfUpdateData($arrRegist) {
	global $conn;
	global $objUpFile;

	foreach ($arrRegist as $data) {
		if (strlen($_POST[$data]) > 0) {
			$arrRegistValue[$data] = $_POST[$data];
		}
	}
	$arrRegistValue["creator_id"] = $_SESSION["member_id"];	
	$arrRegistValue["update_date"] = "NOW()";
	$uploadfile = $objUpFile->getDBFileList();
	//����׵�Τ��ä����ʤ�������
	for ($i = 1; $i <= HTML_TEMPLATE_SUB_MAX; $i++){
		if ($_POST['delete_sub'.$i] == '1'){
			$arrRegistValue['sub_product_id'.sprintf("%02d", $i)] = NULL;
		}
	}
	$arrRegistValue = array_merge($arrRegistValue, $uploadfile);
	
	$conn->autoExecute("dtb_mailmaga_template", $arrRegistValue, "template_id = ". addslashes($_POST["template_id"]));
}

// �������Ѵ�
function lfConvData( $data ){
	
	 // ʸ������Ѵ���mb_convert_kana���Ѵ����ץ�����							
	$arrFlag = array(
					  "header" => "aKV"
					 ,"subject" => "aKV"
					 ,"main_title" => "aKV"
 					 ,"main_comment" => "aKV"
 					 ,"main_product_id" => "aKV"
 					 ,"sub_title" => "aKV"
					 ,"sub_comment" => "aKV"
				);
		
	if ( is_array($data) ){
		foreach ($arrFlag as $key=>$line) {
			$data[$key] = mb_convert_kana($data[$key], $line);
		}
	}

	return $data;
}

// ���ϥ��顼�����å�
function lfErrorCheck($array) {
	$objErr = new SC_CheckError($array);
	
	$objErr->doFunc(array("�᡼�����", "mail_method"), array("EXIST_CHECK", "ALNUM_CHECK"));
	$objErr->doFunc(array("Subject", "subject", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�إå����ƥ�����", 'header', LTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK") );
	$objErr->doFunc(array("�ᥤ���ʥ����ȥ�", 'main_title', STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK") );
	$objErr->doFunc(array("�ᥤ���ʥ�����", 'main_comment', LTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�ᥤ���ʲ���", "main_product_id"), array("EXIST_CHECK"));
	$objErr->doFunc(array("���־��ʷ������ȥ�", "sub_title", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("���־��ʷ�������", "sub_comment", LTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	
	return $objErr->arrErr;
}

?>