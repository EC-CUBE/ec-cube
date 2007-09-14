<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once(MODULE_PATH . "mdl_zero/mdl_zero.inc");

class LC_Page {
	function LC_Page() {
        $this->tpl_mainpage = MODULE_PATH . 'mdl_zero/card_mobile.tpl';
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');		
	} 
}

$objPage = new LC_Page();
$objView = new SC_MobileView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCampaignSess = new SC_CampaignSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;

// �桼����ˡ���ID�μ����ȹ������֤�������������å�
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);

// �����Ƚ��׽���
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);

// �������ơ��֥���ɹ�
$arrData = sfGetOrderTemp($uniqid);

// �����Ƚ��פ򸵤˺ǽ��׻�
$arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo);

// ��ʧ����������
$arrPayment = $objQuery->getall("SELECT module_id, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ? ", array($arrData["payment_id"]));

// �ǡ���������CGI ����ü���ξ��� �����Ѥ����Ф�
if(GC_MobileUserAgent::isMobile()) {
    $objPage = lfSendMobileCredit($arrData, $arrPayment, $objPage);
}else{
    lfSendPcCredit($arrData, $arrPayment);
}

$objPage = lfSendMobileCredit($arrData, $arrPayment, $objPage);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------

// �ǡ�����������(PC)
function lfSendPcCredit($arrData, $arrPayment){
    global $objCartSess;
    global $objSiteSess;
 
	// �����ǡ�������
	$arrSendData = array(
		'clientip' => $arrPayment[0]["memo02"],						                                // ���ȥ�����
		'custom' => SEND_PARAM_CUSTOM ,										                        // yes����
		'send' => SEND_PARAM_SEND,	                                                                // jpall����
		'money' => $arrData["payment_total"],							                            // ���
		'usrtel' => $arrData["order_tel01"] . $arrData["order_tel02"] . $arrData["order_tel03"],	// �����ֹ�
		'usrmail' => $arrData["order_email"],					                                    // �᡼�륢�ɥ쥹
		'sendid' => $arrData["order_temp_id"] . SEND_PARAM_DELIMITER . $arrData["payment_id"],      // ��������TEMPID , payment_id
		'sendpoint' => ECCUBE_PAYMENT	    									                    // EC-CUBE
	);
    
	// ���å���󥫡�����ξ��ʤ������롣
	$objCartSess->delAllProducts();
	// ��ʸ���ID�������롣
	$objSiteSess->unsetUniqId();
    
    $order_url = SEND_PARAM_PC_URL;
    $html = '';
    $html .= '<body onload="document.form1.submit();">';
    $html .= '<form name="form1" id="form1" method="post" action="' . $order_url . '">';
    foreach($arrSendData as $key => $val){
        $html .= '	<input type="hidden" name="' . $key . '" value="' . $val . '">';
    }
    $html .= '	</form>';
    $html .= '	</body>';
//    $html .= "<script type='text/javascript'>document.form1.submit();</script>";
    
    echo $html;
    exit();
}

// �ǡ�����������(MOBILE)
function lfSendMobileCredit($arrData, $arrPayment, $objPage){
    global $objCartSess;
    global $objSiteSess;
 
	// �����ΤȤ��� user_id �� not_member������
	($arrData["customer_id"] == 0) ? $user_id = "not_member" : $user_id = $arrData["customer_id"];	
	
	// �����ǡ�������
	$arrSendData = array(
		'clientip' => $arrPayment[0]["memo05"],						                                // ���ȥ�����
		'act' => SEND_PARAM_ACT ,										                            // imode����
		'send' => SEND_PARAM_SEND,	                                                                // jpall����
		'money' => $arrData["payment_total"],						                    	        // ���
		'usrtel' => $arrData["order_tel01"] . $arrData["order_tel02"] . $arrData["order_tel03"],	// �����ֹ�
		'usrmail' => $arrData["order_email"],					                                    // �᡼�륢�ɥ쥹
        'sendid' => $arrData["order_temp_id"] . SEND_PARAM_DELIMITER . $arrData["payment_id"],		                // ��������TEMPID , payment_id
		'sendpoint' => ECCUBE_PAYMENT,	    									                    // EC-CUBE
		'siteurl' => SITE_URL . "mobile/",	    							                        		    // �����URL
		'sitestr' => "TOP�����"                                						        	// �������̾
	);
    
	// ���å���󥫡�����ξ��ʤ������롣
	$objCartSess->delAllProducts();
	// ��ʸ���ID�������롣
	$objSiteSess->unsetUniqId();
    
    // �ǡ���������CGI ����ü���ξ��� �����Ѥ����Ф�
    $objPage->order_url = SEND_PARAM_MOBILE_URL;
    $objPage->arrSendData = $arrSendData;
    
    return $objPage;
}


?>
