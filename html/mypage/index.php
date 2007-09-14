<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

//�ǡ����١������龦�ʸ�����¹Ԥ��롣��EC���å�ư���Ѥγ�ȯ��
require_once("../require.php");

class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = USER_PATH . 'templates/mypage/index.tpl';
		$this->tpl_title = 'MY�ڡ���/�����������';
		$this->tpl_navi = USER_PATH . 'templates/mypage/navi.tpl';
		$this->tpl_mainno = 'mypage';
		$this->tpl_mypageno = 'index';
		session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();			
$objQuery = new SC_Query();             
$objCustomer = new SC_Customer();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

// ����������å�
if(!isset($_SESSION['customer'])) {
	sfDispSiteError(CUSTOMER_ERROR);
}else {
	//�ޥ��ڡ����ȥå׸ܵҾ���ɽ����
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}

//�ڡ���������
if (isset($_POST['pageno'])) {
    $objPage->tpl_pageno = htmlspecialchars($_POST['pageno'], ENT_QUOTES, CHAR_CODE);
}

$col = "order_id, create_date, payment_id, payment_total";
$from = "dtb_order";
$where = "del_flg = 0 AND customer_id=?";
$arrval = array($objCustomer->getvalue('customer_id'));
$order = "order_id DESC";

$linemax = $objQuery->count($from, $where, $arrval);
$objPage->tpl_linemax = $linemax;

// �ڡ�������μ���
$objNavi = new SC_PageNavi($_POST['pageno'], $linemax, SEARCH_PMAX, "fnNaviPage", NAVI_PMAX);
$objPage->tpl_strnavi = $objNavi->strnavi;		// ɽ��ʸ����
$startno = $objNavi->start_row;

// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
$objQuery->setlimitoffset(SEARCH_PMAX, $startno);
// ɽ�����
$objQuery->setorder($order);

//��������μ���
$objPage->arrOrder = $objQuery->select($col, $from, $where, $arrval);

// ��ʧ����ˡ�μ���
$objPage->arrPayment = sfGetIDValueList("dtb_payment", "payment_id", "payment_method");


$objView->assignobj($objPage);				//$objpage������ƤΥƥ�ץ졼���ѿ���smarty�˳�Ǽ
$objView->display(SITE_FRAME);				//�ѥ��ȥƥ�ץ졼���ѿ��θƤӽФ����¹�


//-------------------------------------------------------------------------------------------------------------------------

//���顼�����å�

function lfErrorCheck() {
	$objErr = new SC_CheckError();
			$objErr->doFunc(array("�᡼�륢�ɥ쥹", "login_email", STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","EMAIL_CHECK","MAX_LENGTH_CHECK"));
			$objErr->dofunc(array("�ѥ����", "login_password", PASSWORD_LEN2), array("EXIST_CHECK","ALNUM_CHECK"));
	return $objErr->arrErr;
}

?>