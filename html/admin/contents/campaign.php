<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");
require_once("./campaign_csv.php");
require_once(DATA_PATH . "include/file_manager.inc");

//---- ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

//---- �ڡ���ɽ�����饹
class LC_Page {
	
	function LC_Page() {
		$this->tpl_mainpage = 'contents/campaign.tpl';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = "campaign";
		$this->tpl_mainno = 'contents';
		$this->tpl_subtitle = '�����ڡ������';
		// �����Ȥ˾��ʤ����äƤ���˥����å������äƤ��뤫�����å�
		$this->tpl_onload = "fnIsCartOn();";
	}
}


$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();
$objFormParam = new SC_FormParam();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �ѥ�᡼������ν����
lfInitParam();
// �ե�������ͤ򥻥å�
$objFormParam->setParam($_POST);

// �Խ������ξ��Ͼ��֤��ݻ�
$objPage->is_update = $_POST['is_update'];

// �ե�������ͤ�ƥ�ץ졼�Ȥ��Ϥ�
$objPage->arrForm = $objFormParam->getHashArray();
$campaign_id = $_POST['campaign_id'];

switch($_POST['mode']) {
	// ������Ͽ/�Խ���Ͽ
	case 'regist':
		// ���顼�����å�
		$objPage->arrErr = lfErrorCheck($campaign_id);
		
		if(count($objPage->arrErr) <= 0) {
			// ��Ͽ
			lfRegistCampaign($campaign_id);
			
			// �����ڡ���TOP�إ�����쥯��
			header("location: ".URL_CAMPAIGN_TOP);
		}
	
		break;
	// �Խ�������
	case 'update':
		// �����ڡ����������
		$objPage->arrForm = lfGetCampaign($campaign_id);
		$objPage->is_update = true;
		break;
	// ���������
	case 'delete':
		// ���
		lfDeleteCampaign($campaign_id);
		// �����ڡ���TOP�إ�����쥯��
		header("location: ".URL_CAMPAIGN_TOP);
		break;
	// CSV����
	case 'csv':
		// ���ץ����λ���
		$option = "ORDER BY create_date DESC";
			
		// CSV���ϥ����ȥ�Ԥκ���
		$arrCsvOutput = sfSwapArray(sfgetCsvOutput(4, " WHERE csv_id = 4 AND status = 1"));
			
		if (count($arrCsvOutput) <= 0) break;
			
		$arrCsvOutputCols = $arrCsvOutput['col'];
		$arrCsvOutputTitle = $arrCsvOutput['disp_name'];
		$head = sfGetCSVList($arrCsvOutputTitle);
		$data = lfGetCSV("dtb_campaign_order", "campaign_id = ?", $option, array($campaign_id), $arrCsvOutputCols);
			
		// CSV���������롣
		sfCSVDownload($head.$data);
		exit;
		break;
	default:
		break;
}

// �����ڡ����������
$objPage->arrCampaign = lfGetCampaignList();
$objPage->campaign_id = $campaign_id;

// �����ڡ��������
$objDate = new SC_Date();
$objPage->arrYear = $objDate->getYear();
$objPage->arrMonth = $objDate->getMonth();
$objPage->arrDay = $objDate->getDay();
$objPage->arrHour = $objDate->getHour();
$objPage->arrMinutes = $objDate->getMinutes();

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);


//---------------------------------------------------------------------------------------------------------------------------------------------------------
/* 
 * �ؿ�̾��lfInitParam
 * �����������Ͼ���ν����
 */
function lfInitParam() {
	global $objFormParam;
		
	$objFormParam->addParam("�����ڡ���̾", "campaign_name", MTEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	
	$objFormParam->addParam("��������", "start_year", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��������", "start_month", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��������", "start_day", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��������", "start_hour", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��������", "start_minute", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));

	$objFormParam->addParam("�������", "end_year", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�������", "end_month", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�������", "end_day", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�������", "end_hour", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�������", "end_minute", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));

	$objFormParam->addParam("�ǥ��쥯�ȥ�̾", "directory_name", MTEXT_LEN, "KVa", array("EXIST_CHECK","ALNUM_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objFormParam->addParam("����������", "limit_count", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("��ʣ��������", "orverlapping_flg", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�����Ȥ˾��ʤ������", "cart_flg", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("����̵������", "deliv_free_flg", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));

}

/* 
 * �ؿ�̾��lfErrorCheck()
 * ����1 �������ڡ���ID
 * �����������顼�����å�
 * ����͡����顼ʸ����Ǽ����
 */
function lfErrorCheck($campaign_id = "") {
	
	global $objQuery;
	global $objFormParam;

	$arrList = $objFormParam->getHashArray();
	$objErr = new SC_CheckError($arrList);
	$objErr->arrErr = $objFormParam->checkError();
	
	$objErr->doFunc(array("��������", "start_year", "start_month", "start_day", "start_hour", "start_minute", "0"), array("CHECK_DATE2"));
	$objErr->doFunc(array("�������", "end_year", "end_month", "end_day", "end_hour", "end_minute", "0"), array("CHECK_DATE2"));
	$objErr->doFunc(array("��������", "�������", "start_year", "start_month", "start_day", "start_hour", "start_minute", "00", "end_year", "end_month", "end_day", "end_hour", "end_minute", "59"), array("CHECK_SET_TERM2"));
	
	if(count($objErr->arrErr) <= 0) {

		// �Խ����Ѥ˸��Υǥ��쥯�ȥ�̾��������롣
		if($campaign_id != "") {
			$directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($campaign_id));
		} else {
			$directory_name = "";
		}

		// Ʊ̾�Υե������¸�ߤ�����ϥ��顼
		if(file_exists(CAMPAIGN_TEMPLATE_PATH.$arrList['directory_name']) && $directory_name != $arrList['directory_name']) {
			$objErr->arrErr['directory_name'] = "�� Ʊ̾�Υǥ��쥯�ȥ꤬���Ǥ�¸�ߤ��ޤ���<br/>";
		}
		$ret = $objQuery->get("dtb_campaign", "directory_name", "directory_name = ? AND del_flg = 0", array($arrList['directory_name']));				
		// DB�ˤ��Ǥ���Ͽ����Ƥ��ʤ��������å�
		if($ret != "" && $directory_name != $arrList['directory_name']) {
			$objErr->arrErr['directory_name'] = "�� ���Ǥ���Ͽ����Ƥ���ǥ��쥯�ȥ�̾�Ǥ���<br/>";
		}
	}
	
	return $objErr->arrErr;
}

/* 
 * �ؿ�̾��lfRegistCampaign()
 * ����1 �������ڡ���ID(���åץǡ��Ȼ��˻���)
 * �������������ڡ�����Ͽ/����
 * ����͡�̵��
 */
function lfRegistCampaign($campaign_id = "") {

	global $objFormParam;
	global $objQuery;
	
	$objSiteInfo = new SC_SiteInfo();
	$arrInfo = $objSiteInfo->data;
	$arrList = $objFormParam->getHashArray();	

	// ������������λ��������
	$start_date = $arrList['start_year']."-".sprintf("%02d", $arrList['start_month'])."-".sprintf("%02d", $arrList['start_day'])." ".sprintf("%02d", $arrList['start_hour']).":".sprintf("%02d", $arrList['start_minute']).":00";
	$end_date = $arrList['end_year']."-".sprintf("%02d", $arrList['end_month'])."-".sprintf("%02d", $arrList['end_day'])." ".sprintf("%02d", $arrList['end_hour']).":".sprintf("%02d", $arrList['end_minute']).":00";

	// �ݥ���ȥ졼�Ȥ����ꤵ��Ƥ��ʤ����0������
	if($arrInfo['point_rate'] == "") $arrInfo['point_rate'] = "0";
	// �ե饰�����ꤵ��Ƥ��ʤ����0������
	if(!$arrList['limit_count']) $arrList['limit_count'] = "0";
	if(!$arrList['orverlapping_flg']) $arrList['orverlapping_flg'] = "0";
	if(!$arrList['cart_flg']) $arrList['cart_flg'] = "0";
	if(!$arrList['deliv_free_flg']) $arrList['deliv_free_flg'] = "0";
	
	$sqlval['campaign_name'] = $arrList['campaign_name'];
	$sqlval['campaign_point_rate'] = $arrInfo['point_rate'];
	$sqlval['start_date'] = $start_date;
	$sqlval['end_date'] = $end_date;
	$sqlval['directory_name'] = $arrList['directory_name'];
	$sqlval['limit_count'] = $arrList['limit_count'];
	$sqlval['orverlapping_flg'] = $arrList['orverlapping_flg'];
	$sqlval['cart_flg'] = $arrList['cart_flg'];
	$sqlval['deliv_free_flg'] = $arrList['deliv_free_flg'];
	$sqlval['update_date'] = "now()";
	
	// �����ڡ���ID�ǻ��ꤵ��Ƥ������update
	if($campaign_id != "") {

		// ���Υǥ��쥯�ȥ�̾�����̾		
		$directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($campaign_id));
		// �ե�����̾���ѹ�
		@rename(CAMPAIGN_TEMPLATE_PATH . $directory_name , CAMPAIGN_TEMPLATE_PATH . $arrList['directory_name']);
		@rename(CAMPAIGN_PATH . $directory_name , CAMPAIGN_PATH . $arrList['directory_name']);

		// update
		$objQuery->update("dtb_campaign", $sqlval, "campaign_id = ?", array($campaign_id));	
		
	} else {

		// �����ڡ���ڡ����ǥ��쥯�ȥ����
		lfCreateTemplate(CAMPAIGN_TEMPLATE_PATH, $arrList['directory_name']);

		$sqlval['create_date'] = "now()";	
		// insert
		$objQuery->insert("dtb_campaign", $sqlval);		
	}
}

/* 
 * �ؿ�̾��lfGetCampaignList()
 * �������������ڡ�����������
 * ����͡������ڡ����������
 */
function lfGetCampaignList() {
	
	global $objQuery;
	
	$col = "campaign_id,campaign_name,directory_name,total_count";
	$objQuery->setorder("update_date DESC");
	$arrRet = $objQuery->select($col, "dtb_campaign", "del_flg = 0");

	return $arrRet;
}

/* 
 * �ؿ�̾��lfGetCampaign()
 * ����1 �������ڡ���ID
 * �������������ڡ���������
 * ����͡������ڡ����������
 */
function lfGetCampaign($campaign_id) {
	
	global $objQuery;
	
	$col = "campaign_id,campaign_name,start_date,end_date,directory_name,limit_count,orverlapping_flg,cart_flg,deliv_free_flg";
	$arrRet = $objQuery->select($col, "dtb_campaign", "campaign_id = ?", array($campaign_id));

	// �������������������ʬ��
	$start_date = (date("Y/m/d/H/i/s" , strtotime($arrRet[0]['start_date'])));
	list($arrRet[0]['start_year'],$arrRet[0]['start_month'],$arrRet[0]['start_day'],$arrRet[0]['start_hour'], $arrRet[0]['start_minute'], $arrRet[0]['start_second']) = split("/", $start_date);
	$end_date = (date("Y/m/d/H/i/s" , strtotime($arrRet[0]['end_date'])));
	list($arrRet[0]['end_year'],$arrRet[0]['end_month'],$arrRet[0]['end_day'],$arrRet[0]['end_hour'], $arrRet[0]['end_minute'], $arrRet[0]['end_second']) = split("/", $end_date);
	
	return $arrRet[0];
}

/* 
 * �ؿ�̾��lfDeleteCampaign()
 * ����1 �������ڡ���ID
 * �������������ڡ�����
 * ����͡�̵��
 */
function lfDeleteCampaign($campaign_id) {

	global $objQuery;
	
	// �ǥ��쥯�ȥ�̾�����̾		
	$directory_name = $objQuery->get("dtb_campaign", "directory_name", "campaign_id = ?", array($campaign_id));
	// �ե��������
	sfDeleteDir(CAMPAIGN_TEMPLATE_PATH . $directory_name);
	sfDeleteDir(CAMPAIGN_PATH . $directory_name);

	$sqlval['del_flg'] = 1;	
	$sqlval['update_date'] = "now()";	
	// delete
	$objQuery->update("dtb_campaign", $sqlval, "campaign_id = ?", array($campaign_id));		
}

/* 
 * �ؿ�̾��lfCreateTemplate()
 * ����1 ���ǥ��쥯�ȥ�ѥ�
 * ����2 �������ե�����̾
 * �������������ڡ���ν���ƥ�ץ졼�Ⱥ���
 * ����͡�̵��
 */
function lfCreateTemplate($dir, $file) {
	
	global $objFormParam;
	$arrRet = $objFormParam->getHashArray();

	
	// �����ե�����ǥ��쥯�ȥ�
	$create_dir = $dir . $file;
	$create_active_dir = $create_dir . "/" . CAMPAIGN_TEMPLATE_ACTIVE;
	$create_end_dir = $create_dir . "/" . CAMPAIGN_TEMPLATE_END;
	// �ǥե���ȥե�����ǥ��쥯�ȥ�
	$default_dir = $dir . "default";
	$default_active_dir = $default_dir . "/" . CAMPAIGN_TEMPLATE_ACTIVE;
	$default_end_dir = $default_dir . "/" . CAMPAIGN_TEMPLATE_END;
	
	$ret = sfCreateFile($create_dir, 0755);	
	$ret = sfCreateFile($create_active_dir, 0755);	
	$ret = sfCreateFile($create_end_dir, 0755);

	// �����ڡ���¹�PHP�򥳥ԡ�
	$ret = sfCreateFile(CAMPAIGN_PATH . $file);
	copy($default_dir . "/src/index.php", CAMPAIGN_PATH . $file . "/index.php");
	copy($default_dir . "/src/application.php", CAMPAIGN_PATH . $file . "/application.php");
	copy($default_dir . "/src/complete.php", CAMPAIGN_PATH . $file . "/complete.php");
	copy($default_dir . "/src/entry.php", CAMPAIGN_PATH . $file . "/entry.php");

	// �ǥե���ȥƥ�ץ졼�Ⱥ���(�����ڡ�����)
	$header = lfGetFileContents($default_active_dir."header.tpl");
	sfWriteFile($header, $create_active_dir."header.tpl", "w");
	$contents = lfGetFileContents($default_active_dir."contents.tpl");
	if(!$arrRet['cart_flg']) {
		$contents .= "\n" . '<!--{*������ե�����*}-->' . "\n";
		$contents .= lfGetFileContents(CAMPAIGN_BLOC_PATH . "login.tpl");
		$contents .= '<!--{*�����Ͽ�ե�����*}-->'."\n";
		$contents .= lfGetFileContents(CAMPAIGN_BLOC_PATH . "entry.tpl");
	}
	sfWriteFile($contents, $create_active_dir."contents.tpl", "w");
	$footer = lfGetFileContents($default_active_dir."footer.tpl");
	sfWriteFile($footer, $create_active_dir."footer.tpl", "w");
	
	// �����ȥե졼�����
	$site_frame  = $header."\n";
	$site_frame .= '<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>'."\n";
	$site_frame .= '<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>'."\n";
	$site_frame .= '<!--{include file=$tpl_mainpage}-->'."\n";
	$site_frame .= $footer."\n";
	sfWriteFile($site_frame, $create_active_dir."site_frame.tpl", "w");

	/* �ǥե���ȥƥ�ץ졼�Ⱥ���(�����ڡ���λ) */
	$header = lfGetFileContents($default_end_dir."header.tpl");
	sfWriteFile($header, $create_end_dir."header.tpl", "w");
	$contents = lfGetFileContents($default_end_dir."contents.tpl");
	sfWriteFile($contents, $create_end_dir."contents.tpl", "w");
	$footer = lfGetFileContents($default_end_dir."footer.tpl");
	sfWriteFile($footer, $create_end_dir."footer.tpl", "w");
}

/* 
 * �ؿ�̾��lfGetFileContents()
 * ����1 ���ե�����ѥ�
 * ���������ե������ɹ�
 * ����͡�̵��
 */
function lfGetFileContents($read_file) {
	
	if(file_exists($read_file)) {
		$contents = file_get_contents($read_file);
	} else {
		$contents = "";		
	}
	
	return $contents;
}
?>