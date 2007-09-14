<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'order/mail_view.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

if(sfIsInt($_GET['send_id'])) {
	$objQuery = new SC_Query();
	$col = "subject, mail_body";
	$where = "send_id = ?";
	$arrRet = $objQuery->select($col, "dtb_mail_history", $where, array($_GET['send_id']));
	$objPage->tpl_subject = $arrRet[0]['subject'];
	$objPage->tpl_body = $arrRet[0]['mail_body'];
}

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
