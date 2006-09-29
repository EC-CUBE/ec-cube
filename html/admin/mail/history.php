<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	function LC_Page() {
		$this->tpl_mainpage = 'mail/history.tpl';
		$this->tpl_mainno = 'mail';
		$this->tpl_subnavi = 'mail/subnavi.tpl';
		$this->tpl_subno = "history";
		$this->tpl_subtitle = '�ۿ�����';
	}
}

//---- �ڡ����������
$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objDate = new SC_Date();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �����
if ( sfCheckNumLength($_GET['send_id']) && ($_GET['mode']=='delete') ){
	
	$sql = "UPDATE dtb_send_history SET del_flg = 1 WHERE send_id = ?";
	$conn->query($sql, array($_GET['send_id']) );
	sfReload();

}	
	$col = "*";
	$from = "dtb_send_history";
	
	$where .= " del_flg = ?";
	$arrval[] = "0";
	
	$objQuery = new SC_Query();
	// �Կ��μ���
	$linemax = $objQuery->count($from, $where, $arrval);
	$objPage->tpl_linemax = $linemax;				// ���郎�������ޤ�����ɽ����
	
	// �ڡ�������μ���
	$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, SEARCH_PMAX, "fnNaviSearchPage", NAVI_PMAX);
	$objPage->tpl_strnavi = $objNavi->strnavi;		// ɽ��ʸ����
	$startno = $objNavi->start_row;
	
	// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
	$objQuery->setlimitoffset(SEARCH_PMAX, $startno);
	
	// ɽ�����
	$order = "start_date DESC, send_id DESC";
	$objQuery->setorder($order);
	
	// ������̤μ���
	$objPage->arrDataList = $objQuery->select($col, $from, $where, $arrval);
	
//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
