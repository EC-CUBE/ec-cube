<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_css = URL_DIR.'css/layout/contact/index.css';	// �ᥤ��CSS�ѥ�
		$this->tpl_mainpage = 'contact/index.tpl';
		$this->tpl_title = '���䤤��碌(���ϥڡ���)';
		$this->tpl_page_category = 'contact';
		global $arrPref;
		$this->arrPref = $arrPref;
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCampaignSess = new SC_CampaignSession();
$CONF = sf_getBasisData();			// Ź�޴��ܾ���
sfDomainSessionStart();	 

$objCustomer = new SC_Customer();

if ($objCustomer->isloginSuccess()){
	$objPage->arrData = $_SESSION['customer'];
}

//SSLURLȽ��
if (SSLURL_CHECK == 1){
	$ssl_url= sfRmDupSlash(SSL_URL.$_SERVER['REQUEST_URI']);
	if (!ereg("^https://", $non_ssl_url)){
		sfDispSiteError(URL_ERROR);
	}
}

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

//�ե��������Ѵ��ѥ����
$arrConvertColumn = array(
							 array(  "column" => "name01",		"convert" => "aKV" ),
							 array(  "column" => "name02",		"convert" => "aKV" ),
							 array(  "column" => "kana01",		"convert" => "CKV" ),
							 array(  "column" => "kana02",		"convert" => "CKV" ),
							 array(  "column" => "zip01",		"convert" => "n" ),
							 array(  "column" => "zip02",		"convert" => "n" ),
							 array(  "column" => "pref",		"convert" => "n" ),
							 array(  "column" => "addr01",		"convert" => "aKV" ),
							 array(  "column" => "addr02",		"convert" => "aKV" ),
							 array(  "column" => "email",		"convert" => "a" ),
							 array(  "column" => "tel01",		"convert" => "n" ),
							 array(  "column" => "tel02",		"convert" => "n" ),
							 array(  "column" => "tel03",		"convert" => "n" ),
							 array(  "column" => "contents",   "convert" => "aKV")
					      );

switch ($_POST['mode']){
	case 'confirm':
	// ���顼�����å�
	$objPage->arrForm = $_POST;
	$objPage->arrForm['email'] = strtolower($_POST['email']);
	$objPage->arrForm = lfConvertParam($objPage->arrForm,$arrConvertColumn);
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	if ( ! $objPage->arrErr ){
		// ���顼̵���Ǵ�λ����
		$objPage->tpl_mainpage = 'contact/confirm.tpl';
		$objPage->tpl_title = '���䤤��碌(��ǧ�ڡ���)';
	} else {
		foreach ($objPage->arrForm as $key => $val){
		$objPage->$key = $val;
		}
	}
	break;
	
	case 'return':
	foreach ($_POST as $key => $val){
		$objPage->$key = $val;
		}
	break;
	
	case 'complete':
	$objPage->arrForm = $_POST;
	$objPage->arrForm['email'] = strtolower($_POST['email']);
	$objPage->arrForm = lfConvertParam($objPage->arrForm,$arrConvertColumn);
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	if(!$objPage->arrErr) {
		lfSendMail($CONF, $objPage);
		//��λ�ڡ����ذ�ư����
		header("location: ./complete.php");
		exit;
	} else {
		sfDispSiteError(CUSTOMER_ERROR);
	}
	break;
	
	default:
	break;
}

//----���ڡ���ɽ��
$objView->assignobj($objPage);
// �ե졼�������(�����ڡ���ڡ����������ܤʤ��ѹ�)
$objCampaignSess->pageView($objView);
//------------------------------------------------------------------------------------------------------------------------------------------

//���顼�����å�������
function lfErrorCheck($array) {
    $objErr = new SC_CheckError($array);
	$objErr->doFunc(array("��̾��(��)", 'name01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��̾��(̾)", 'name02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�եꥬ��(����)", 'kana01', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("�եꥬ��(�ᥤ)", 'kana02', STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "KANA_CHECK"));
	$objErr->doFunc(array("͹���ֹ�1", "zip01", ZIP01_LEN ) ,array("SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("͹���ֹ�2", "zip02", ZIP02_LEN ) ,array("SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
	$objErr->doFunc(array("������1", "addr01", MTEXT_LEN), array("SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("������2", "addr02", MTEXT_LEN), array("SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("���䤤��碌����", "contents", MLTEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', "email", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹(��ǧ)', "email02", MTEXT_LEN) ,array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array('�᡼�륢�ɥ쥹', '�᡼�륢�ɥ쥹(��ǧ)', "email", "email02") ,array("EQUAL_CHECK"));
	$objErr->doFunc(array("�������ֹ�1", 'tel01', TEL_ITEM_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������ֹ�2", 'tel02', TEL_ITEM_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������ֹ�3", 'tel03', TEL_ITEM_LEN), array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	
    if (REVIEW_ALLOW_URL == false) {
        // URL�����Ϥ�ػ�
        global $arrReviewDenyURL;
        $objErr->doFunc(array("URL", "contents", $arrReviewDenyURL), array("PROHIBITED_STR_CHECK"));
    }
    
	return $objErr->arrErr;
}

//----������ʸ������Ѵ�
function lfConvertParam($array, $arrConvertColumn) {
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 *  a :  ���ѱѿ�����Ⱦ�ѱѿ������Ѵ�����
	 */
	// �����̾�ȥ���С��Ⱦ���
	foreach ($arrConvertColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}
	
	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(strlen(($array[$key])) > 0) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

// ------------  �᡼������ ------------ 

function lfSendMail($CONF, $objPage){
	//���ѥ�����ѹ����Τ餻�᡼������
	
	$objMailText = new SC_SiteView();
	$objSiteInfo = $objView->objSiteInfo;
	$arrInfo = $objSiteInfo->data;
	$objPage->tpl_shopname=$arrInfo['shop_name'];
	$objPage->tpl_infoemail = $arrInfo['email02'];
	$objMailText->assignobj($objPage);
	$toCustomerMail = $objMailText->fetch("mail_templates/contact_mail.tpl");
	$objMail = new GC_SendMail();
	
	if ( $objPage->arrForm['email'] ) {
		$fromMail_name = $objPage->arrForm['name01'] ." ��";
		$fromMail_address = $objPage->arrForm['email'];
	} else {
		$fromMail_name = $CONF["shop_name"];
		$fromMail_address = $CONF["email02"];
	}
	$subject = sfMakeSubject("���䤤��碌������ޤ�����");
	$objMail->setItem(
						  $CONF["email02"]					//������
						, $subject							//�����֥�������
						, $toCustomerMail					//����ʸ
						, $fromMail_address					//�����������ɥ쥹
						, $fromMail_name					//����������̾��
						, $fromMail_address					//��reply_to
						, $CONF["email04"]					//��return_path
						, $CONF["email04"]					//  Errors_to
														);
	$objMail->sendMail();
	
	$subject = sfMakeSubject("���䤤��碌������դ��ޤ�����");
	$objMail->setItem(
						  ''								//������
						, $subject							//�����֥�������
						, $toCustomerMail					//����ʸ
						, $CONF["email03"]					//�����������ɥ쥹
						, $CONF["shop_name"]				//����������̾��
						, $CONF["email02"]					//��reply_to
						, $CONF["email04"]					//��return_path
						, $CONF["email04"]					//  Errors_to
														);
	$objMail->setTo($objPage->arrForm['email'], $objPage->arrForm['name01'] ." ��");
	$objMail->sendMail();	
}
?>