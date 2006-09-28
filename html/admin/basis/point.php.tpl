<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'basis/point.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'point';
		$this->tpl_mainno = 'basis';
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrDISP;
		$this->arrDISP = $arrDISP;
	}
}
$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();
$objDate = new SC_Date();

// ��Ͽ��������������ǯ
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrStartYear = $objDate->getYear();
$objPage->arrStartMonth = $objDate->getMonth();
$objPage->arrStartDay = $objDate->getDay();
$objPage->arrStartHour = $objDate->getHour();
// ��Ͽ������������λǯ
$objDate->setStartYear(RELEASE_YEAR);
$objDate->setEndYear(DATE("Y"));
$objPage->arrEndYear = $objDate->getYear();
$objPage->arrEndMonth = $objDate->getMonth();
$objPage->arrEndDay = $objDate->getDay();
$objPage->arrEndHour = $objDate->getHour();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();
// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

$cnt = $objQuery->count("dtb_baseinfo");

if ($cnt > 0) {
	$objPage->tpl_mode = "update";
} else {
	$objPage->tpl_mode = "insert";
}

// ������ɤΰ����Ѥ�
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key) || ereg("^campaign", $key)) {
		switch($key) {
			case 'search_product_flag':
			case 'search_status':
				$objPage->arrSearchHidden[$key] = sfMergeParamCheckBoxes($val);
				break;
			default:
				$objPage->arrSearchHidden[$key] = $val;
				break;
		}
	}
	
}

switch($_POST['mode']) {
//���Υݥ������Ͽ
case 'update':
case 'insert':
	// �����ͤ��Ѵ�
	$objFormParam->convParam();
	$objPage->arrErr = $objFormParam->checkError();
	if(count($objPage->arrErr) == 0) {
		switch($_POST['mode']) {
		case 'update':
			lfUpdateData(); // ��¸�Խ�
			break;
		case 'insert':
			lfInsertData(); // ��������
			break;
		default:
			break;
		}
		// ��ɽ��
		//sfReload();
		$objPage->tpl_onload = "window.alert('�ݥ�������꤬��λ���ޤ�����');";
	}
	break;
//�����ڡ���������ڡ�������ο��
case 'campaign_next':
	$objPage->tpl_mainpage = 'basis/campaign_regist.tpl';
	//�Խ���
	if(sfIsInt($_POST['campaign_id'])) {
		//�����ڡ������μ���
		$arrRet = $objQuery->select("*", "dtb_campaign", "delete = 0 AND campaign_id = ? ", array($_POST['campaign_id']));
		$arrCamp = $arrRet[0];
		//�����ڡ�����֤μ���
		if ($arrCamp['start_date'] != "" && $arrCamp['end_date']){
			$arrSDate = sfDispDBDate($arrCamp['start_date']);
			list($arrCamp['startyear'], $arrCamp['startmonth'], $arrCamp['startday'], $arrCamp['starthour']) = split("[/ :]", $arrSDate);
			$arrEDate = sfDispDBDate($arrCamp['end_date']);
			list($arrCamp['endyear'], $arrCamp['endmonth'], $arrCamp['endday'], $arrCamp['endhour']) = split("[/ :]", $arrEDate);
		}
		$objPage->arrCamp = $arrCamp;
	}
	break;
//�����ڡ�����Ͽ
case 'campaign_regist':
	$objPage->tpl_mainpage = 'basis/campaign_regist.tpl';
	$objPage->arrCamp = $_POST;
	$objPage->arrCamp['campaign_name'] = mb_convert_kana($objPage->arrCamp['campaign_name'], "KVa");
	$objPage->arrCamp['campaign_point_rate'] = mb_convert_kana($objPage->arrCamp['campaign_point_rate'], "n");
	//���顼�����å�
	$objPage->arrErr = lfErrorCheck($objPage->arrCamp);
	if(count($objPage->arrErr) == 0) {
		//�����ڡ�����Ͽ
		lfRegistCampaign($objPage->arrCamp);
		//�ݥ��������ڡ����ذ�ư
		header("Location: ./point.php");
		exit;
	}
	break;
default:
	$arrCol = $objFormParam->getKeyList(); // ����̾���������
	$col	= sfGetCommaList($arrCol);
	$arrRet = $objQuery->select($col, "dtb_baseinfo");
	// POST�ͤμ���
	$objFormParam->setParam($arrRet[0]);
	//�����ڡ���κ��
	if($_POST['mode'] == 'delete') {
		$sqlval['del_flg'] = '1';
		$sqlval['update_date'] = 'now()';
		$objQuery->begin();
		$objQuery->update("dtb_campaign", $sqlval, "campaign_id = ? ", array($_POST['campaign_id']));
		$objQuery->delete("dtb_campaign_detail", "campaign_id = ? ", array($_POST['campaign_id']));
		$objQuery->commit();
	}
	//�����ڡ���ǡ����μ���
	$objPage->arrCampData = lfGetCampaignData();
	// ���ƥ�����ɹ�
	$objPage->arrCatList = sfGetCategoryList();
	break;
}

$objPage->arrForm = $objFormParam->getFormParamList();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//--------------------------------------------------------------------------------------------------------------------------------------
/* �ѥ�᡼������ν���� */
function lfInitParam() {
	global $objFormParam;
	$objFormParam->addParam("�ݥ������ͿΨ", "point_rate", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objFormParam->addParam("�����Ͽ����Ϳ�ݥ����", "welcome_point", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
}

function lfUpdateData() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$sqlval = $objFormParam->getHashArray();
	$sqlval['update_date'] = 'Now()';
	$objQuery = new SC_Query();
	// UPDATE�μ¹�
	$ret = $objQuery->update("dtb_baseinfo", $sqlval);
}

function lfInsertData() {
	global $objFormParam;
	// ���ϥǡ������Ϥ���
	$sqlval = $objFormParam->getHashArray();
	$sqlval['update_date'] = 'Now()';
	$objQuery = new SC_Query();
	// INSERT�μ¹�
	$ret = $objQuery->insert("dtb_baseinfo", $sqlval);
}

//��Ͽ�Ѥߥ����ڡ���μ���
function lfGetCampaigndata() {
	$objQuery = new SC_Query;
	//��Ͽ���ս���¤٤�
	$objQuery->setorder('update_date DESC');
	$arrData = $objQuery->select("*", "dtb_campaign", "del_flg = 0");
	for($i = 0; $i < count($arrData); $i++) {
		if ($arrData[$i]['search_condition'] != "") {
			$arrRet[$i] = unserialize($arrData[$i]['search_condition']);
			foreach($arrRet[$i] as $key => $val) {
				switch($key) {
				case 'search_product_flag':
				case 'search_status':
					$arrData[$i][$key] = split("-", $val);
					break;
				default:
					$arrData[$i][$key] = $val;
					break;
				}
			}
		}
	}
	return $arrData;
}

//---- ���ϥ��顼�����å�
function lfErrorCheck($array) {
	
	foreach($array as $key => $val) {
		if(!ereg("^search", $key)) {
			$arrRet[$key] = $val;
		}
	}
	
	$objErr = new SC_CheckError($arrRet);
	
	$objErr->doFunc(array("������(ǯ)", "startyear"), array("SELECT_CHECK", "NUM_CHECK"));
	$objErr->doFunc(array("������(��)", "startmonth"), array("SELECT_CHECK", "NUM_CHECK"));	
	$objErr->doFunc(array("������(��)", "startday"), array("SELECT_CHECK", "NUM_CHECK"));	
	$objErr->doFunc(array("������(��)", "starthour"), array("SELECT_CHECK", "NUM_CHECK"));
	$objErr->doFunc(array("��λ��(ǯ)", "endyear"), array("SELECT_CHECK", "NUM_CHECK"));
	$objErr->doFunc(array("��λ��(��)", "endmonth"), array("SELECT_CHECK", "NUM_CHECK"));	
	$objErr->doFunc(array("��λ��(��)", "endday"), array("SELECT_CHECK", "NUM_CHECK"));	
	$objErr->doFunc(array("��λ��(��)", "endhour"), array("SELECT_CHECK", "NUM_CHECK"));	
	$objErr->doFunc(array("������", "startyear", "startmonth", "startday", "starthour"), array("CHECK_DATE2"));
	$objErr->doFunc(array("��λ��", "endyear", "endmonth", "endday", "endhour"), array("CHECK_DATE2"));	
	$objErr->doFunc(array("������","��λ��", "startyear", "startmonth", "startday", "starthour", "endyear", "endmonth", "endday", "endhour"), array("CHECK_SET_TERM2"));
	$objErr->doFunc(array("�����ڡ���̾", "campaign_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�����ڡ���ݥ������ͿΨ", "campaign_point_rate", INT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));	
	
	return $objErr->arrErr;
}

//�����ڡ�����Ͽ
function lfRegistCampaign($array) {
	$objQuery = new SC_Query;
	
	$objQuery->begin();
	$sqlval['campaign_name'] = $array['campaign_name'];
	$sqlval['campaign_point_rate'] = $array['campaign_point_rate'];
	$sqlval['start_date'] = $array['startyear']."-".$array['startmonth']."-".$array['startday']." ".$array['starthour'].":00:00";
	$sqlval['end_date'] = $array['endyear']."-".$array['endmonth']."-".$array['endday']." ".$array['endhour'].":00:00";
	//���������Ǽ���륭������ꤹ��
	foreach($array as $key => $val) {
		//�ڡ���NO�ϳ�Ǽ���ʤ�
		if(ereg("^search", $key) && !ereg("^search_page", $key)) {
			$arrRet[$key] = $val;
		}
	}
	$sqlval['search_condition'] = serialize($arrRet);
	$sqlval['create_date'] = 'now()';
	//�Խ���
	if(sfIsInt($array['campaign_id'])) {
		$sqlval['update_date'] = 'now()';
		//����
		$objQuery->update("dtb_campaign", $sqlval, "campaign_id = ?", array($array['campaign_id']));
		//�ܺ٥ơ��֥����
		$objQuery->delete("dtb_campaign_detail", "campaign_id = ? ", array($array['campaign_id']));
		$sqlvaldet['campaign_id'] = $array['campaign_id'];
	} else {
		//������Ͽ
		$campaign_id = $objQuery->nextval("dtb_campaign", "campaign_id");
		$sqlval['campaign_id'] = $campaign_id;
		$objQuery->insert("dtb_campaign", $sqlval);
		$sqlvaldet['campaign_id'] = $campaign_id;
	}
	$sqlvaldet['campaign_point_rate'] = $array['campaign_point_rate'];
	//�����ڡ�����ID������˳�Ǽ
	$arrID = explode("-", $array['campaign_product_id']);
	foreach($arrID as $val) {
		$sqlvaldet['product_id'] = $val;
		$objQuery->insert("dtb_campaign_detail", $sqlvaldet);
	}
	$objQuery->commit();
}
