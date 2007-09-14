<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");

//---- �ڡ���ɽ�����饹
class LC_Page {
	
	function LC_Page() {
		$this->tpl_mainpage = TEMPLATE_DIR . '/campaign/application.tpl';
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');	
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView(false);
$objQuery = new SC_Query();
$objCustomer = new SC_Customer();
$objCampaignSess = new SC_CampaignSession();
// ���å����������饹
$objCookie = new SC_Cookie(COOKIE_EXPIRE);

$objLoginFormParam = new SC_FormParam();	// ������ե�������
lfInitLoginFormParam();						// �������
$objLoginFormParam->setParam($_POST);		// POST�ͤμ���

// �ǥ��쥯�ȥ�̾�����
$dir_name = dirname($_SERVER['PHP_SELF']);
$arrDir = split('/', $dir_name);
$dir_name = $arrDir[count($arrDir) -1];

/* ���å����˥����ڡ���ǡ�����񤭹��� */
// �����ڡ��󤫤�����ܤȤ���������ݻ�
$objCampaignSess->setIsCampaign();
// �����ڡ���ID���ݻ�
$campaign_id = $objQuery->get("dtb_campaign", "campaign_id", "directory_name = ? AND del_flg = 0", array($dir_name));
$objCampaignSess->setCampaignId($campaign_id);
// �����ڡ���ǥ��쥯�ȥ�̾���ݻ�
$objCampaignSess->setCampaignDir($dir_name);

// �����ڡ��󤬳����椫������å�
if(lfCheckActive($dir_name)) {
	$status = CAMPAIGN_TEMPLATE_ACTIVE;
	$objPage->is_active = true;
} else {
	$status = CAMPAIGN_TEMPLATE_END;
	$objPage->is_active = false;
}

switch($_POST['mode']) {
// ����������å�
case 'login':
	$objLoginFormParam->toLower('login_email');
	$objPage->arrErr = $objLoginFormParam->checkError();
	$arrForm =  $objLoginFormParam->getHashArray();
	// ���å�����¸Ƚ��
	if($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
		$objCookie->setCookie('login_email', $_POST['login_email']);
	} else {
		$objCookie->setCookie('login_email', '');
	}

	if(count($objPage->arrErr) == 0) {
		// ������Ƚ��
		if(!$objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'])) {
			// ����Ͽ��Ƚ��
			$objQuery = new SC_Query;
			$where = "email = ? AND status = 1 AND del_flg = 0";
			$ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email']));
			
			if($ret > 0) {
				sfDispSiteError(TEMP_LOGIN_ERROR);
			} else {
				sfDispSiteError(SITE_LOGIN_ERROR);
			}
		} else {
			// ��ʣ���������å�
			$orverlapping_flg = $objQuery->get("dtb_campaign", "orverlapping_flg", "campaign_id = ?", array($objCampaignSess->getCampaignId()));

			if($orverlapping_flg) {
				if(lfOverlappingCheck($objCustomer->getValue('customer_id'), $campaign_id)) {
					$objPage->arrErr['login_email'] = "�� ʣ���󤴱��礹�뤳�ȤϽ���ޤ���";
				}
			}
	
			if(count($objPage->arrErr) == 0) {
				// �����������Ͽ
				lfRegistCampaignOrder($objCustomer->getValue('customer_id'));
				// ��λ�ڡ����إ�����쥯��
				header("location: ". CAMPAIGN_URL . "$dir_name/complete.php");
			}
		}
	}
	break;
default :
	break;
}
// ���Ͼ�����Ϥ�
$objPage->arrForm = $_POST;
$objPage->dir_name = $dir_name;
$objPage->tpl_dir_name = CAMPAIGN_TEMPLATE_PATH . $dir_name  . "/" . $status;

//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);


//---------------------------------------------------------------------------------------------------------------------------------------------------------

/* 
 * �ؿ�̾��lfInitLoginFormParam()
 * ��������������ե����������
 * ����͡�̵��
 */
function lfInitLoginFormParam() {
	global $objLoginFormParam;
	$objLoginFormParam->addParam("��������", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
	$objLoginFormParam->addParam("�᡼�륢�ɥ쥹", "login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
	$objLoginFormParam->addParam("�ѥ����", "login_pass", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
}

/* 
 * �ؿ�̾��lfCheckActive()
 * ����1 ���ǥ��쥯�ȥ�̾
 * �������������ڡ����椫�����å�
 * ����͡������ڡ�����ʤ� true ��λ�ʤ� false
 */
function lfCheckActive($directory_name) {
	
	global $objQuery;
	$is_active = false;
	
	$col = "limit_count, total_count, start_date, end_date";
	$arrRet = $objQuery->select($col, "dtb_campaign", "directory_name = ? AND del_flg = 0", array($directory_name));

	// �����������������������
	$start_date = (date("YmdHis", strtotime($arrRet[0]['start_date'])));
	$end_date = (date("YmdHis", strtotime($arrRet[0]['end_date'])));
	$now_date = (date("YmdHis"));

	// �����ڡ��󤬳��Ŵ��֤ǡ����Ŀ���������Ǥ���
	if($now_date > $start_date && $now_date < $end_date
			&& ($arrRet[0]['limit_count'] > $arrRet[0]['total_count'] || $arrRet[0]['limit_count'] < 1)) {
		$is_active = true;
	}
		
	return $is_active;
}

/* 
 * �ؿ�̾��lfRegistCampaignOrder()
 * �������������ڡ������������¸
 * ����1 ���ܵ�ID
 * ����͡�̵��
 */
function lfRegistCampaignOrder($customer_id) {

	global $objQuery;
	global $objCampaignSess;
	$campaign_id = $objCampaignSess->getCampaignId();

	// ����ǡ��������
	$cols = "
			customer_id,
			name01 as order_name01,
			name02 as order_name02,
			kana01 as order_kana01,
			kana02 as order_kana02,
			zip01 as order_zip01,
			zip02 as order_zip02,
			pref as order_pref,
			addr01 as order_addr01,
			addr02 as order_addr02,
			email as order_email,
			tel01 as order_tel01,
			tel02 as order_tel02,
			tel03 as order_tel03,
			fax01 as order_fax01,
			fax02 as order_fax02,
			fax03 as order_fax03,
			sex as order_sex,
			job as order_job,
			birth as order_birth
			";
			
	$arrCustomer = $objQuery->select($cols, "dtb_customer", "customer_id = ?", array($customer_id)); 

	$sqlval = $arrCustomer[0];
	$sqlval['campaign_id'] = $campaign_id;
    $sqlval['create_date'] = 'now()';
		
	// INSERT�μ¹�
	$objQuery->insert("dtb_campaign_order", $sqlval);
	
	// �������߿��ι���
	$total_count = $objQuery->get("dtb_campaign", "total_count", "campaign_id = ?", array($campaign_id));
	$arrCampaign['total_count'] = $total_count += 1;
	$objQuery->update("dtb_campaign", $arrCampaign, "campaign_id = ?", array($campaign_id));
	
}

/* 
 * �ؿ�̾��lfOverlappingCheck()
 * ����������ʣ��������å�
 * ����1 ���ܵ�ID
 * ����2 �������ڡ���ID
 * ����͡��ե饰 (��ʣ�����ä��� true ��ʣ���ʤ��ä��� false)
 */
function lfOverlappingCheck($customer_id, $campaign_id) {
	
	global $objQuery;
	
	$count = $objQuery->count("dtb_campaign_order", "customer_id = ? AND campaign_id = ?", array($customer_id, $campaign_id));
	if($count > 0) {
		return true;
	}
	
	return false;
}
?>