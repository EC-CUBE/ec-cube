<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'shopping/complete.tpl';
		$this->tpl_css = '/css/layout/shopping/complete.css';
		$this->tpl_title = "����ʸ��λ";
		global $arrCONVENIENCE;
		$this->arrCONVENIENCE = $arrCONVENIENCE;
		global $arrCONVENIMESSAGE;
		$this->arrCONVENIMESSAGE = $arrCONVENIMESSAGE;
		global $arrCONVENIENCE;
		global $arrCONVENIMESSAGE;
		$objPage->arrCONVENIENCE = $arrCONVENIENCE;
		$objPage->arrCONVENIMESSAGE = $arrCONVENIMESSAGE;
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');		

	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_MobileView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objSiteInfo = $objView->objSiteInfo;
$arrInfo = $objSiteInfo->data;
$objCustomer = new SC_Customer();

// ���Υڡ�������������Ͽ��³�����Ԥ�줿��Ƚ��
sfIsPrePage($objSiteSess, true);
// �桼����ˡ���ID�μ����ȹ������֤�������������å�
$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);
if ($uniqid != "") {
	
	// ��λ����
	$objQuery = new SC_Query();
	$objQuery->begin();
	$order_id = lfDoComplete($objQuery, $uniqid);
	$objQuery->commit();
	
	// ���å������ݴɤ���Ƥ������򹹿�����
	$objCustomer->updateSession();

	// ��λ�᡼������ 4�Ϸ�����
	if($order_id != "") {
		$order_email = $objQuery->select("order_email", "dtb_order", "order_id = ?", array($order_id));
    
    //��Ͽ����Ƥ���᡼�륢�ɥ쥹�����Ӥ�PC���˱�������ʸ��λ�᡼��Υƥ�ץ졼�Ȥ��Ѥ���
    if(ereg("(ezweb.ne.jp$|docomo.ne.jp$|softbank.ne.jp$|vodafone.ne.jp$)",$order_email[0]['order_email'])){
              sfSendOrderMail($order_id, '1', '', '');
        }else{
              sfSendOrderMail($order_id, '0', '', '');
        }
	}

	//����¾����μ���
	$other_data = $objQuery->get("dtb_order", "memo02", "order_id = ? ", array($order_id));
	if($other_data != "") {
		$arrOther = unserialize($other_data);
		
		// �ǡ������Խ�
		foreach($arrOther as $key => $val){
			// URL�ξ��ˤϥ�󥯤Ĥ���ɽ��������
			if (preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $val["value"])) {
				$arrOther[$key]["value"] = "<a href='#' onClick=\"window.open('". $val["value"] . "'); \" >" . $val["value"] ."</a>";
			}
		}
				
		$objPage->arrOther = $arrOther;
		
	}
	
	// ���ե��ꥨ�����ѥ���С�����󥿥�������
	$objPage->tpl_conv_page = AFF_SHOPPING_COMPLETE;
	$objPage->tpl_aff_option = "order_id=$order_id";
	//��ײ��ʤμ���
	$total = $objQuery->get("dtb_order", "total", "order_id = ? ", array($order_id));
	if($total != "") {
		$objPage->tpl_aff_option.= "|total=$total";
	}
}

$objPage->arrInfo = $arrInfo;

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//--------------------------------------------------------------------------------------------------------------------------
// ���ӥ��������Ϥ��ѥǡ�������������
function lfGetEbisData($order_id) {
	$objQuery = new SC_Query();
	$col = "customer_id, total, order_sex, order_job, to_number(to_char(age(current_timestamp, order_birth), 'YYY'), 999) AS order_age";
	$arrRet = $objQuery->select($col, "dtb_order", "order_id = ?", array($order_id));
	
	if($arrRet[0]['customer_id'] > 0) {
		// ����ֹ�
		$arrEbis['m1id'] = $arrRet[0]['customer_id'];
		// ����or���
		$arrEbis['o5id'] = '1';
	} else {
		// ����ֹ�
		$arrEbis['m1id'] = '';
		// ����or���
		$arrEbis['o5id'] = '2';	
	}
	
	// �������
	$arrEbis['a1id'] = $arrRet[0]['total'];
	// ����
	$arrEbis['o2id'] = $arrRet[0]['order_sex'];
	// ǯ��
	$arrEbis['o3id'] = $arrRet[0]['order_age'];
	// ����
	$arrEbis['o4id'] = $arrRet[0]['order_job'];
		
	$objQuery->setgroupby("product_id");
	$arrRet = $objQuery->select("product_id", "dtb_order_detail", "order_id = ?", array($order_id));
	$arrProducts = sfSwapArray($arrRet);
	
	$line = "";
	// ����ID�򥢥�����С�����³���롣
	foreach($arrProducts['product_id'] as $val) {
		if($line != "") {
			$line .= "_$val";		
		} else {
			$line .= "$val";
		}
	}
	
	// ����ID	
	$arrEbis['o1id'] = $line;
	
	return $arrEbis;
}

// ��λ����
function lfDoComplete($objQuery, $uniqid) {
	global $objCartSess;
	global $objSiteSess;
	global $objCustomer;
	global $arrInfo;
	
	// �������ơ��֥���ɹ�
	$arrData = sfGetOrderTemp($uniqid);
	
	// ���������Ͽ����
	if ($objCustomer->isLoginSuccess()) {
		// �����Ϥ������Ͽ
		lfSetNewAddr($uniqid, $objCustomer->getValue('customer_id'));
		// �������פ�ܵҥơ��֥��ȿ��
		lfSetCustomerPurchase($objCustomer->getValue('customer_id'), $arrData, $objQuery);
	} else {
		//���������������Ͽ
		switch(PURCHASE_CUSTOMER_REGIST) {
		//̵��
		case '0':
			// �����������Ͽ
			if($arrData['member_check'] == '1') {
				// �������Ͽ
				$customer_id = lfRegistPreCustomer($arrData, $arrInfo);
				// �������פ�ܵҥơ��֥��ȿ��
				lfSetCustomerPurchase($customer_id, $arrData, $objQuery);
			}
			break;
		//ͭ��
		case '1':
			// �������Ͽ
			$customer_id = lfRegistPreCustomer($arrData, $arrInfo);
			// �������פ�ܵҥơ��֥��ȿ��
			lfSetCustomerPurchase($customer_id, $arrData, $objQuery);
			break;
		}
		
	}
	// ����ơ��֥�����ơ��֥�˳�Ǽ����
	$order_id = lfRegistOrder($objQuery, $arrData);
	// �����Ⱦ��ʤ����ܺ٥ơ��֥�˳�Ǽ����
	lfRegistOrderDetail($objQuery, $order_id, $objCartSess);
	// �������ơ��֥�ξ���������롣
	lfDeleteTempOrder($objQuery, $uniqid);
	
	// ���å���󥫡�����ξ��ʤ������롣
	$objCartSess->delAllProducts();
	// ��ʸ���ID�������롣
	$objSiteSess->unsetUniqId();
	
	return $order_id;
}

// �����Ͽ�ʲ���Ͽ��
function lfRegistPreCustomer($arrData, $arrInfo) {
	// �������β����Ͽ
	$sqlval['name01'] = $arrData['order_name01'];
	$sqlval['name02'] = $arrData['order_name02'];
	$sqlval['kana01'] = $arrData['order_kana01'];
	$sqlval['kana02'] = $arrData['order_kana02'];
	$sqlval['zip01'] = $arrData['order_zip01'];
	$sqlval['zip02'] = $arrData['order_zip02'];
	$sqlval['pref'] = $arrData['order_pref'];
	$sqlval['addr01'] = $arrData['order_addr01'];
	$sqlval['addr02'] = $arrData['order_addr02'];
	$sqlval['email'] = $arrData['order_email'];
	$sqlval['tel01'] = $arrData['order_tel01'];
	$sqlval['tel02'] = $arrData['order_tel02'];
	$sqlval['tel03'] = $arrData['order_tel03'];
	$sqlval['fax01'] = $arrData['order_fax01'];
	$sqlval['fax02'] = $arrData['order_fax02'];
	$sqlval['fax03'] = $arrData['order_fax03'];
	$sqlval['sex'] = $arrData['order_sex'];
	$sqlval['password'] = $arrData['password'];
	$sqlval['reminder'] = $arrData['reminder'];
	$sqlval['reminder_answer'] = $arrData['reminder_answer'];

	// ���ޥ��ۿ��ѥե饰��Ƚ��
	switch($arrData['mail_flag']) {
	case '1':	// HTML�᡼��
		$mail_flag = 4;
		break;
	case '2':	// TEXT�᡼��
		$mail_flag = 5;
		break;
	case '3':	// ��˾�ʤ�
		$mail_flag = 6;
		break;
	default:
		$mail_flag = 6;
		break;
	}
	$sqlval['mailmaga_flg'] = $mail_flag;
		
	// �������Ͽ
	$sqlval['status'] = 1;
	// URLȽ���ѥ���
	$sqlval['secret_key'] = sfGetUniqRandomId("t"); 
	
	$objQuery = new SC_Query();
	$sqlval['create_date'] = "now()";
	$sqlval['update_date'] = "now()";
	$objQuery->insert("dtb_customer", $sqlval);
	
	// �ܵ�ID�μ���
	$arrRet = $objQuery->select("customer_id", "dtb_customer", "secret_key = ?", array($sqlval['secret_key']));
	$customer_id = $arrRet[0]['customer_id'];

	//������Ͽ��λ�᡼������
	$objMailPage = new LC_Page();
	$objMailPage->to_name01 = $arrData['order_name01'];
	$objMailPage->to_name02 = $arrData['order_name02'];
	$objMailPage->CONF = $arrInfo;
	$objMailPage->uniqid = $sqlval['secret_key'];
	$objMailView = new SC_SiteView();
	$objMailView->assignobj($objMailPage);
	$body = $objMailView->fetch("/mail_templates/customer_mail.tpl");
	
	$objMail = new GC_SendMail();
	$objMail->setItem(
						''										//������
						, sfMakeSubject("�����Ͽ�Τ���ǧ")		//�����֥�������
						, $body									//����ʸ
						, $arrInfo['email03']					//�����������ɥ쥹
						, $arrInfo['shop_name']					//����������̾��
						, $arrInfo["email03"]					//��reply_to
						, $arrInfo["email04"]					//��return_path
						, $arrInfo["email04"]					//  Errors_to
						, $arrInfo["email01"]					//  Bcc
														);
	// ���������
	$name = $arrData['order_name01'] . $arrData['order_name02'] ." ��";
	$arrData['order_email'] = $objQuery->select("email_mobile", "dtb_customer", "secret_key = ?", array($sqlval['secret_key']));
	$objMail->setTo($arrData['order_email'], $name);			
	$objMail->sendMail();
	
	return $customer_id;
}

// �������ơ��֥�Τ��Ϥ���򥳥ԡ�����
function lfCopyDeliv($uniqid, $arrData) {
	$objQuery = new SC_Query();
	
	// �̤Τ��Ϥ������ꤷ�Ƥ��ʤ���硢���������Ͽ����򥳥ԡ����롣
	if($arrData["deliv_check"] != "1") {
		$sqlval['deliv_name01'] = $arrData['order_name01'];
		$sqlval['deliv_name02'] = $arrData['order_name02'];
		$sqlval['deliv_kana01'] = $arrData['order_kana01'];
		$sqlval['deliv_kana02'] = $arrData['order_kana02'];
		$sqlval['deliv_pref'] = $arrData['order_pref'];
		$sqlval['deliv_zip01'] = $arrData['order_zip01'];
		$sqlval['deliv_zip02'] = $arrData['order_zip02'];
		$sqlval['deliv_addr01'] = $arrData['order_addr01'];
		$sqlval['deliv_addr02'] = $arrData['order_addr02'];
		$sqlval['deliv_tel01'] = $arrData['order_tel01'];
		$sqlval['deliv_tel02'] = $arrData['order_tel02'];
		$sqlval['deliv_tel03'] = $arrData['order_tel03'];
		$where = "order_temp_id = ?";
		$objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
	}
}

// ����ơ��֥����Ͽ
function lfRegistOrder($objQuery, $arrData) {
	$sqlval = $arrData;

	// ����ơ��֥�˽񤭹��ޤʤ�������
	unset($sqlval['mail_flag']);		// ���ޥ������å�
	unset($sqlval['deliv_check']);		// �̤Τ��Ϥ�������å�
	unset($sqlval['point_check']);		// �ݥ�������ѥ����å�
	unset($sqlval['member_check']);		// ��������������å�
	unset($sqlval['password']);			// ������ѥ����
	unset($sqlval['reminder']);			// ��ޥ����������
	unset($sqlval['reminder_answer']);	// ��ޥ����������
	unset($sqlval['session']);	        // ���å����

	// ��ʸ���ơ�����:���̵꤬����п������դ�����
	if($sqlval["status"] == ""){
		$sqlval['status'] = '1';			
	}
	
	// �̤Τ��Ϥ������ꤷ�Ƥ��ʤ���硢���������Ͽ����򥳥ԡ����롣
	if($arrData["deliv_check"] != "1") {
		$sqlval['deliv_name01'] = $arrData['order_name01'];
		$sqlval['deliv_name02'] = $arrData['order_name02'];
		$sqlval['deliv_kana01'] = $arrData['order_kana01'];
		$sqlval['deliv_kana02'] = $arrData['order_kana02'];
		$sqlval['deliv_pref'] = $arrData['order_pref'];
		$sqlval['deliv_zip01'] = $arrData['order_zip01'];
		$sqlval['deliv_zip02'] = $arrData['order_zip02'];
		$sqlval['deliv_addr01'] = $arrData['order_addr01'];
		$sqlval['deliv_addr02'] = $arrData['order_addr02'];
		$sqlval['deliv_tel01'] = $arrData['order_tel01'];
		$sqlval['deliv_tel02'] = $arrData['order_tel02'];
		$sqlval['deliv_tel03'] = $arrData['order_tel03'];
	}
	
	$order_id = $arrData['order_id'];		// ��������ID
	$sqlval['create_date'] = 'now()';		// ������
	
	// ���åȤ��ͤ򥤥󥵡���
	//$sqlval = lfGetInsParam($sqlval);
	
	// INSERT�μ¹�
	$objQuery->insert("dtb_order", $sqlval);

	return $order_id;
}

// ����ܺ٥ơ��֥����Ͽ
function lfRegistOrderDetail($objQuery, $order_id, $objCartSess) {
	// �����������μ���
	$arrCart = $objCartSess->getCartList();
	$max = count($arrCart);
	
	// ����¸�ߤ���ܺ٥쥳���ɤ�ä��Ƥ�����
	$objQuery->delete("dtb_order_detail", "order_id = $order_id");

	// ����̾����
	$arrClassName = sfGetIDValueList("dtb_class", "class_id", "name");
	// ����ʬ��̾����
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
			
	for ($i = 0; $i < $max; $i++) {
		// ���ʵ��ʾ���μ���	
		$arrData = sfGetProductsClass($arrCart[$i]['id']);
		
		// ¸�ߤ��뾦�ʤΤ�ɽ�����롣
		if($arrData != "") {
			$sqlval['order_id'] = $order_id;
			$sqlval['product_id'] = $arrCart[$i]['id'][0];
			$sqlval['classcategory_id1'] = $arrCart[$i]['id'][1];
			$sqlval['classcategory_id2'] = $arrCart[$i]['id'][2];
			$sqlval['product_name'] = $arrData['name'];
			$sqlval['product_code'] = $arrData['product_code'];
			$sqlval['classcategory_name1'] = $arrClassCatName[$arrData['classcategory_id1']];
			$sqlval['classcategory_name2'] = $arrClassCatName[$arrData['classcategory_id2']];
			$sqlval['point_rate'] = $arrCart[$i]['point_rate'];			
			$sqlval['price'] = $arrCart[$i]['price'];
			$sqlval['quantity'] = $arrCart[$i]['quantity'];
			lfReduceStock($objQuery, $arrCart[$i]['id'], $arrCart[$i]['quantity']);
			// INSERT�μ¹�
			$objQuery->insert("dtb_order_detail", $sqlval);
		} else {
			sfDispSiteError(CART_NOT_FOUND, "", false, "", true);
		}
	}
}

/* �������ơ��֥�κ�� */
function lfDeleteTempOrder($objQuery, $uniqid) {
	$where = "order_temp_id = ?";
	$sqlval['del_flg'] = 1;
	$objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
	// $objQuery->delete("dtb_order_temp", $where, array($uniqid));
}

// �������ơ��֥�ν��꤬��Ͽ�Ѥߥơ��֥�Ȱۤʤ���ϡ��̤Τ��Ϥ�����ɲä���
function lfSetNewAddr($uniqid, $customer_id) {
	$objQuery = new SC_Query();
	$diff = false;
	$find_same = false;
	
	$col = "deliv_name01,deliv_name02,deliv_kana01,deliv_kana02,deliv_tel01,deliv_tel02,deliv_tel03,deliv_zip01,deliv_zip02,deliv_pref,deliv_addr01,deliv_addr02";
	$where = "order_temp_id = ?";
	$arrRet = $objQuery->select($col, "dtb_order_temp", $where, array($uniqid));
	
	// ����̾��deliv_�������롣
	foreach($arrRet[0] as $key => $val) {
		$keyname = ereg_replace("^deliv_", "", $key);
		$arrNew[$keyname] = $val;
	}
	
	// �������ơ��֥�Ȥ����
	$col = "name01,name02,kana01,kana02,tel01,tel02,tel03,zip01,zip02,pref,addr01,addr02";
	$where = "customer_id = ?";
	$arrCustomerAddr = $objQuery->select($col, "dtb_customer", $where, array($customer_id));
	
	// �������ν���Ȱۤʤ���
	if($arrNew != $arrCustomerAddr[0]) {
		// �̤Τ��Ϥ���ơ��֥�ν������Ӥ���
		$col = "name01,name02,kana01,kana02,tel01,tel02,tel03,zip01,zip02,pref,addr01,addr02";
		$where = "customer_id = ?";
		$arrOtherAddr = $objQuery->select($col, "dtb_other_deliv", $where, array($customer_id));

		foreach($arrOtherAddr as $arrval) {
			if($arrNew == $arrval) {
				// ���Ǥ�Ʊ�����꤬��Ͽ����Ƥ���
				$find_same = true;
			}
		}
		
		if(!$find_same) {
			$diff = true;
		}
	}
	
	// ���������Ϥ��褬��Ͽ�ѤߤΤ�ΤȰۤʤ�����̤Τ��Ϥ���ơ��֥����Ͽ����
	if($diff) {
		$sqlval = $arrNew;
		$sqlval['customer_id'] = $customer_id;
		$objQuery->insert("dtb_other_deliv", $sqlval);
	}
}

/* ������������ơ��֥����Ͽ���� */
function lfSetCustomerPurchase($customer_id, $arrData, $objQuery) {
	$col = "first_buy_date, last_buy_date, buy_times, buy_total, point";
	$where = "customer_id = ?";
	$arrRet = $objQuery->select($col, "dtb_customer", $where, array($customer_id));
	$sqlval = $arrRet[0];
	
	if($sqlval['first_buy_date'] == "") {
		$sqlval['first_buy_date'] = "Now()";
	}
	$sqlval['last_buy_date'] = "Now()";
	$sqlval['buy_times']++;
	$sqlval['buy_total']+= $arrData['total'];
	$sqlval['point'] = ($sqlval['point'] + $arrData['add_point'] - $arrData['use_point']);
	
	// �ݥ���Ȥ���­���Ƥ�����
	if($sqlval['point'] < 0) {
		$objQuery->rollback();
		sfDispSiteError(LACK_POINT);
	}
	
	$objQuery->update("dtb_customer", $sqlval, $where, array($customer_id));
}

// �߸ˤ򸺤餹����
function lfReduceStock($objQuery, $arrID, $quantity) {
	$where = "product_id = ? AND classcategory_id1 = ? AND classcategory_id2 = ?";
	$arrRet = $objQuery->select("stock, stock_unlimited", "dtb_products_class", $where, $arrID);
	
	// ����ڤ쥨�顼
	if(($arrRet[0]['stock_unlimited'] != '1' && $arrRet[0]['stock'] < $quantity) || $quantity == 0) {
		$objQuery->rollback();
		sfDispSiteError(SOLD_OUT, "", true);
	// ̵���¤ξ�硢�߸ˤ�NULL
	} elseif($arrRet[0]['stock_unlimited'] == '1') {
		$sqlval['stock'] = null;
		$objQuery->update("dtb_products_class", $sqlval, $where, $arrID);
	// �߸ˤ򸺤餹
	} else {
		$sqlval['stock'] = ($arrRet[0]['stock'] - $quantity);
		if($sqlval['stock'] == "") {
			$sqlval['stock'] = '0';
		}		
		$objQuery->update("dtb_products_class", $sqlval, $where, $arrID);
	}
}

// GET���ͤ򥤥󥵡����Ѥ�������
function lfGetInsParam($sqlVal){
	
	foreach($_GET as $key => $val){
		// ������¸�ߥ����å�
		if(sfColumnExists("dtb_order", $key)) $sqlVal[$key] = $val;
	}
	
	return $sqlVal;
}

?>