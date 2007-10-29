<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./inc_mailmagazine.php");

if(file_exists(MODULE_PATH . 'mdl_combz/mdl_combz.inc')) {
	require_once(MODULE_PATH . 'mdl_combz/mdl_combz.inc');
}

class LC_Page {
	var $arrSession;
	var $arrHtmlmail;
	var $arrNowDate;
	function LC_Page() {
		$this->tpl_mainpage = 'mail/index.tpl';
		$this->tpl_mainno = 'mail';
		$this->tpl_subnavi = 'mail/subnavi.tpl';
		$this->tpl_subno = "index";
		$this->tpl_pager = DATA_PATH . 'Smarty/templates/admin/pager.tpl';
		$this->tpl_subtitle = '�ۿ���������';
		
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrJob;
		$arrJob["����"] = "����";
		$this->arrJob = $arrJob;
		global $arrSex;		
		$this->arrSex = $arrSex;
		global $arrMailType;
		$this->arrMailType = $arrMailType;
		global $arrDomain;
		$this->arrDomain = $arrDomain;
		global $arrPageRows;
		$this->arrPageRows = $arrPageRows;
		// �ڡ����ʥ���
		$this->tpl_pageno = $_POST['search_pageno'];
		global $arrMAILMAGATYPE;
		$this->arrMAILMAGATYPE = $arrMAILMAGATYPE;
		$this->arrHtmlmail[''] = "���٤�";
		$this->arrHtmlmail[1] = $arrMAILMAGATYPE[1];
		$this->arrHtmlmail[2] = $arrMAILMAGATYPE[2];
		global $arrCustomerType;
		$this->arrCustomerType = $arrCustomerType;
		global $arrDOMAIN;
		$this->arrDomain = $arrDOMAIN;
		$this->arrDomain[''] = "���ꤷ�ʤ�";
		$this->arrDomain[1] = $arrDOMAIN[1];
		$this->arrDomain[2] = $arrDOMAIN[2]; 
	}
}

class LC_HTMLtemplate {
	var $list_data;
}

//---- �ڡ����������
$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objDate = new SC_Date();
$objQuery = new SC_Query();
$objPage->objDate = $objDate;
$objPage->arrTemplate = getTemplateList($conn);

$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

/*
	query:�ۿ�����ֳ�ǧ��
*/
if ($_GET["mode"] == "query" && sfCheckNumLength($_GET["send_id"])) {
	// ���������ꡢ��������ǧ����
	$sql = "SELECT search_data FROM dtb_send_history WHERE send_id = ?";
	$result = $conn->getOne($sql, array($_GET["send_id"]));
	$tpl_path = "mail/query.tpl";
		
	$list_data = unserialize($result);
	
	// ��ƻ�ܸ����Ѵ�
	$list_data['pref_disp'] = $objPage->arrPref[$list_data['pref']];
	
	//�ɥᥤ�������Ѵ�
	$list_data['domain_disp'] = $objPage->arrDomain[$list_data['domain']];
	
	// �ۿ�����
	$list_data['htmlmail_disp'] = $objPage->arrHtmlmail[$list_data['htmlmail']];
	
	// ���̤��Ѵ�
	if (count($list_data['sex']) > 0) {
		foreach($list_data['sex'] as $key => $val){
			$list_data['sex'][$key] = $objPage->arrSex[$val];
			$sex_disp .= $list_data['sex'][$key] . " ";
		}
		$list_data['sex_disp'] = $sex_disp;
	}
	
	// ���Ȥ��Ѵ�
	if (count($list_data['job']) > 0) {
		foreach($list_data['job'] as $key => $val){
			$list_data['job'][$key] = $objPage->arrJob[$val];
			$job_disp .= $list_data['job'][$key] . " ";
		}
		$list_data['job_disp'] = $job_disp;
	}
		
	// ���ƥ����Ѵ�
	$arrCatList = sfGetCategoryList();
	$list_data['category_name'] = $arrCatList[$list_data['category_id']];
	
	$objPage->list_data = $list_data;

	$objView->assignobj($objPage);
	$objView->display($tpl_path);
	exit;
}

if($_POST['mode'] == 'delete') {
}

switch($_POST['mode']) {
/*
	search:�ָ����ץܥ���
	back:������̲��̡����ץܥ���
*/
case 'delete':
case 'search':
case 'back':
// ����ӡ���Ϣ����
case 'combz':
	//-- �����ͥ���С���
	$objPage->list_data = lfConvertParam($_POST, $arrSearchColumn);
		
	//-- ���ϥ��顼�Υ����å�
	$objPage->arrErr = lfErrorCheck($objPage->list_data);

	//-- ��������
	if (!is_array($objPage->arrErr)) {
		$objPage->list_data['name'] = sfManualEscape($objPage->list_data['name']);
		// hidden���Ǻ���
		$objPage->arrHidden = lfGetHidden($objPage->list_data);

		//-- �����ǡ�������	
		$objSelect = new SC_CustomerList($objPage->list_data, "magazine");
		// �������줿WHEREʸ���������		
		list($where, $arrval) = $objSelect->getWhere();
		// ��WHERE����ʬ�������롣
		$where = ereg_replace("^WHERE", "", $where);

		// ������̤μ���
		$objQuery = new SC_Query();
		$from = "dtb_customer";

		// �Կ��μ���
		$linemax = $objQuery->count($from, $where, $arrval);
		$objPage->tpl_linemax = $linemax;				// ���郎�������ޤ�����ɽ����

		// �ڡ�������μ���
		$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, SEARCH_PMAX, "fnResultPageNavi", NAVI_PMAX);
		$objPage->arrPagenavi = $objNavi->arrPagenavi;	
		$startno = $objNavi->start_row;

		// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
		$objQuery->setlimitoffset(SEARCH_PMAX, $startno);
		// ɽ�����
		$objQuery->setorder("customer_id DESC");
		
		// ������̤μ���	
		$col = $objSelect->getMailMagazineColumn(lfGetIsMobile($_POST['mail_type']));
		$objPage->arrResults = $objQuery->select($col, $from, $where, $arrval);
		//���߻���μ���
		$objPage->arrNowDate = lfGetNowDate();
	}
	
	if($_POST['mode'] == 'combz' && function_exists('sfCombzPost')) {
		$objPage->combz_return = sfCombzPost($_POST['combz_type'], $where, $arrval);
	}
	break;
/*
	input:������̲��̡�htmlmail��������ץܥ���
*/
case 'input':
	//-- �����ͥ���С���
	$objPage->list_data = lfConvertParam($_POST, $arrSearchColumn);
	//-- ���ϥ��顼�Υ����å�
	$objPage->arrErr = lfErrorCheck($objPage->list_data);
	//-- ���顼�ʤ�
	if (!is_array($objPage->arrErr)) {
		//-- ���߻���μ���
		$objPage->arrNowDate = lfGetNowDate();
		$objPage->arrHidden = lfGetHidden($objPage->list_data); // hidden���Ǻ���
		$objPage->tpl_mainpage = 'mail/input.tpl';
	}
	break;
/*
	template:�ƥ�ץ졼������
*/
case 'template':
	//-- �����ͥ���С���
	$objPage->list_data = lfConvertParam($_POST, $arrSearchColumn);
	
	//-- ��������μ���
	$objPage->arrNowDate['year'] = $_POST['send_year'];
	$objPage->arrNowDate['month'] = $_POST['send_month'];
	$objPage->arrNowDate['day'] = $_POST['send_day'];
	$objPage->arrNowDate['hour'] = $_POST['send_hour'];
	$objPage->arrNowDate['minutes'] = $_POST['send_minutes'];
	
	//-- ���ϥ��顼�Υ����å�
	$objPage->arrErr = lfErrorCheck($objPage->list_data);

	//-- ��������
	if ( ! is_array($objPage->arrErr)) {
		$objPage->list_data['name'] = sfManualEscape($objPage->list_data['name']);
		$objPage->arrHidden = lfGetHidden($objPage->list_data); // hidden���Ǻ���
	
		$objPage->tpl_mainpage = 'mail/input.tpl';
		$template_data = getTemplateData($conn, $_POST['template_id']);
		if ( $template_data ){
			foreach( $template_data as $key=>$val ){
				$objPage->list_data[$key] = $val;
			}
		}

		//-- HTML�ƥ�ץ졼�Ȥ���Ѥ�����ϡ�HTML����������������BODY������
		if ( $objPage->list_data["mail_method"] == 3) {
			$objTemplate = new LC_HTMLtemplate;
			$objTemplate->list_data = lfGetHtmlTemplateData($_POST['template_id']);
			$objSiteInfo = new SC_SiteInfo();
			$objTemplate->arrInfo = $objSiteInfo->data;
			//�᡼��ô���̿���ɽ��
			$objUpFile = new SC_UploadFile(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
			$objUpFile->addFile("�᡼��ô���̿�", 'charge_image', array('jpg'), IMAGE_SIZE, true, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
			$objUpFile->setDBFileList($objTemplate->list_data);
			$objTemplate->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
			$objMakeTemplate = new SC_AdminView();
			$objMakeTemplate->assignobj($objTemplate);		
			$objPage->list_data["body"] = $objMakeTemplate->fetch("mail/html_template.tpl");
		}
	}
	break;
/*
	regist_confirm:���������Ƥ��ǧ��
	regist_back:�֥ƥ�ץ졼��������̤�����
	regist_complete:����Ͽ��
*/	
case 'regist_confirm':
case 'regist_back':
case 'regist_complete':
	//-- �����ͥ���С���
	$arrCheckColumn = array_merge( $arrSearchColumn, $arrRegistColumn );
	$objPage->list_data = lfConvertParam($_POST, $arrCheckColumn);
	
	//���߻���μ���
	$objPage->arrNowDate = lfGetNowDate();

	//-- ���ϥ��顼�Υ����å�
	$objPage->arrErr = lfErrorCheck($objPage->list_data, 1);
	$objPage->tpl_mainpage = 'mail/input.tpl';
	$objPage->arrHidden = lfGetHidden($objPage->list_data); // hidden���Ǻ���
	
	//-- ��������
	if ( ! is_array($objPage->arrErr)) {
			$objPage->list_data['name'] = sfManualEscape($objPage->list_data['name']);
		if ( $_POST['mode'] == 'regist_confirm'){
			$objPage->tpl_mainpage = 'mail/input_confirm.tpl';
		} else if( $_POST['mode'] == 'regist_complete' ){
			lfRegistData($objPage->list_data);
            //���ޥ���ǽ������ˤʤäƤ��뤫�ɤ�����Ƚ��
			if(MELMAGA_SEND == true) {
                //ͽ���ۿ��⡼�ɤ�ʬ��
				if(MELMAGA_BATCH_MODE) {
					header("Location: " . URL_DIR . "admin/mail/history.php");
				} else {	
					header("Location: " . URL_DIR . "admin/mail/sendmail.php?mode=now");
				}
				exit;
			} else {
				sfErrorHeader(">> �ܥ����ȤǤϥ��ޥ��ۿ��ϹԤ��ޤ���");
			}
		}
	}
	break;
default:
	$objPage->list_data['mail_type'] = 1;
	break;
}

// �ۿ����֤�ǯ�򡢡ָ���ǯ�������ǯ�ܣ��פ��ϰϤ�����
for ($year=date("Y"); $year<=date("Y") + 1;$year++){
	$arrYear[$year] = $year;
}

$objPage->arrBlaynEngine = lfGetBlayn();

$objPage->arrYear = $arrYear;

$objPage->arrCustomerOrderId = lfGetCustomerOrderId($_POST['buy_product_code']);

$objPage->arrCatList = sfGetCategoryList();

$objPage->arrCampaignList = lfGetCampaignList();

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-------------------------------------------------------------------------------------------------------------------------------

// ���ʥ����ɤǸ������줿���˥ҥåȤ��������ֹ��������롣
function lfGetCustomerOrderId($keyword) {
	if($keyword != "") {
		$col = "dtb_order.customer_id, dtb_order.order_id";
		$from = "dtb_order LEFT JOIN dtb_order_detail USING(order_id)";
		$where = "product_code LIKE ? AND del_flg = 0";
		$val = sfManualEscape($keyword);
		$arrVal[] = "%$val%";
		$objQuery = new SC_Query();
		$objQuery->setgroupby("customer_id, order_id");
		$arrRet = $objQuery->select($col, $from, $where, $arrVal);
		$arrCustomerOrderId = sfArrKeyValues($arrRet, "customer_id", "order_id");
	}
	return $arrCustomerOrderId;	
}

function lfMakeCsvData( $send_id ){
		
	global $conn;

	$arrTitle  = array(	 'name01','email');
				
	$sql = "SELECT name01,email FROM dtb_send_customer WHERE send_id = ? ORDER BY email";
	$result = $conn->getAll($sql, array($send_id) );
	
	if ( $result ){
		$return = lfGetCSVData( $result, $arrTitle);
	}
	return $return;	
}

//---- CSV�����ѥǡ�������
function lfGetCSVData( $array, $arrayIndex){	
	
	for ($i=0; $i<count($array); $i++){
		
		for ($j=0; $j<count($array[$i]); $j++ ){
			if ( $j > 0 ) $return .= ",";
			$return .= "\"";			
			if ( $arrayIndex ){
				$return .= mb_ereg_replace("<","��",mb_ereg_replace( "\"","\"\"",$array[$i][$arrayIndex[$j]] )) ."\"";	
			} else {
				$return .= mb_ereg_replace("<","��",mb_ereg_replace( "\"","\"\"",$array[$i][$j] )) ."\"";
			}
		}
		$return .= "\n";			
	}
	return $return;
}

//���߻���μ������ۿ����֥ǥե�����͡�
function lfGetNowDate(){
	$nowdate = date("Y/n/j/G/i");
	list($year, $month, $day, $hour, $minute) = split("[/]", $nowdate);
	$arrNowDate = array( 'year' => $year, 'month' => $month, 'day' => $day, 'hour' => $hour, 'minutes' => $minute);
	foreach ($arrNowDate as $key => $val){
		switch ($key){
			case 'minutes':
			$val = ereg_replace('^[0]','', $val);
			if ($val < 30){
			$list_date[$key] = '30';
			}else{
			$list_date[$key] = '00';
			}
			break;
			case 'year':
			case 'month':
			case 'day':
			$list_date[$key] = $val;
			break;
		}
	}
		if ($arrNowDate['minutes'] < 30){
			$list_date['hour'] = $hour;
		}else{
			$list_date['hour'] = $hour + 1;
		}
	return $list_date;
}

// �ۿ����Ƥ��ۿ��ꥹ�Ȥ�񤭹���
function lfRegistData($arrData){
	
	global $conn;
	global $arrSearchColumn;
	
	$objQuery = new SC_Query();
	$objSelect = new SC_CustomerList( lfConvertParam($arrData, $arrSearchColumn), "magazine" );
	
	$search_data = $conn->getAll($objSelect->getListMailMagazine(lfGetIsMobile($_POST['mail_type'])), $objSelect->arrVal);
	$dataCnt = count($search_data);
	$dtb_send_history = array();
	
    if(DB_TYPE == "pgsql"){
	   $dtb_send_history["send_id"] = $objQuery->nextval('dtb_send_history', 'send_id');
    }
    
    $dtb_send_history["mail_method"] = $arrData['mail_method'];
	$dtb_send_history["subject"] = $arrData['subject'];
	$dtb_send_history["body"] = $arrData['body'];
	if(MELMAGA_BATCH_MODE) {
		//���󥹥ȡ�����Υ����С���CRON��ͭ���Ǥ���ʤ���ꤵ�줿���֤˥᡼�������
        $dtb_send_history["start_date"] = $arrData['send_year'] ."/".$arrData['send_month']."/".$arrData['send_day']." ".$arrData['send_hour'].":".$arrData['send_minutes'];
	} else {
		//CRON��̵���Ǥ���Хꥢ�륿�������������
        $dtb_send_history["start_date"] = "now()";
	}
	$dtb_send_history["creator_id"] = $_SESSION['member_id'];
	$dtb_send_history["send_count"] = $dataCnt;
	$arrData['body'] = "";
	$dtb_send_history["search_data"] = serialize($arrData);
	$dtb_send_history["update_date"] = "now()";
	$dtb_send_history["create_date"] = "now()";
   
    //�ϥå���dtb_send_history��ǡ����١���dtb_send_history������
    $objQuery->insert("dtb_send_history", $dtb_send_history );
    if(DB_TYPE == "mysql"){
        $dtb_send_history["send_id"] = $objQuery->nextval('dtb_send_history','send_id');
    }
    
	if ( is_array( $search_data ) ){
        foreach( $search_data as $line ){
            
            $dtb_send_customer = array();
			$dtb_send_customer["customer_id"] = $line["customer_id"];
			$dtb_send_customer["send_id"] = $dtb_send_history["send_id"];
			$dtb_send_customer["email"] = $line["email"];
			$dtb_send_customer["name"] = $line["name01"] . " " . $line["name02"];            
            $conn->autoExecute("dtb_send_customer", $dtb_send_customer );					
            
        }	
	}
}

// �����ڡ������
function lfGetCampaignList() {
	
	global $objQuery;
	
	$sql = "SELECT campaign_id, campaign_name FROM dtb_campaign ORDER BY update_date DESC";
	$arrResult = $objQuery->getall($sql);

	foreach($arrResult as $arrVal) {
		$arrCampaign[$arrVal['campaign_id']] = $arrVal['campaign_name'];
	}
	return $arrCampaign;
}

function lfGetIsMobile($mail_type) {
	// ������̤μ���			
	$is_mobile = false;
	switch($mail_type) {
		case 1:
			$is_mobile = false;
			break;
		case 2:
			$is_mobile = true;		
			break;
		default:
			$is_mobile = false;
			break;
	}
	
	return $is_mobile;
}

// �֥쥤�󥨥󥸥�����Ѥߤ���ǧ
function lfGetBlayn() {
    
    global $objQuery;
    
    $arrRet[now_version] = $objQuery->count("dtb_module", "now_version = (SELECT now_version FROM dtb_module WHERE main_php='blayn/blayn.php')");
    $arrRet[blayn_ip] = $objQuery->count("dtb_blayn");
    return $arrRet;
}
?>