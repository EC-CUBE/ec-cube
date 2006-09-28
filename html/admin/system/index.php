<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $tpl_category;	// ʬ��(HOME:1,��ʪ��Ͽ:2,��ʪ����:3,�����ƥ�:4,��������:5)
	var $list_data;		// �ơ��֥�ǡ���������
	var $arrAUTHORITY;
	var $tpl_onload;
	var $tpl_disppage;	// ɽ����Υڡ����ֹ�
	var $tpl_strnavi;
	function LC_Page() {
		$this->tpl_mainpage = 'system/index.tpl';
		$this->tpl_subnavi = 'system/subnavi.tpl';
		$this->tpl_mainno = 'system';
		$this->tpl_subno = 'index';
		$this->tpl_onload = 'fnGetRadioChecked();';
		$this->tpl_subtitle = '���С�����';
		global $arrAUTHORITY;
		$this->arrAUTHORITY = $arrAUTHORITY;
	}
}

// ���å���󥯥饹
$objSess = new SC_Session();
// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

$conn = new SC_DbConn();

// �ƥ�ץ졼���ѿ����ݻ����饹
$objPage = new LC_Page();
// SQL�����ѥ��֥�����������
$objSql = new SC_SelectSql();
$objSql->setSelect("SELECT member_id,name,department,login_id,authority,rank,work FROM dtb_member");
$objSql->setOrder("rank DESC");
$objSql->setWhere("del_flg <> 1 AND member_id <> ". ADMIN_ID);

//�ʰץ�����¹ԥ��֥�������
$oquery = new SC_Query();
// �Կ��μ���
$linemax = $oquery->count("dtb_member", "del_flg <> 1 AND member_id <>".ADMIN_ID);

// ��ư��η�������
$workmax = $oquery->count("dtb_member", "work = 1 AND del_flg <> 1 AND member_id <>".ADMIN_ID);
$objPage->workmax= $workmax;

// �ڡ�������ν���
$objNavi = new SC_PageNavi($_GET['pageno'], $linemax, MEMBER_PMAX, "fnMemberPage", NAVI_PMAX);
$objPage->tpl_strnavi = $objNavi->strnavi;
$objPage->tpl_disppage = $objNavi->now_page;
$objPage->tpl_pagemax = $objNavi->max_page;
$startno = $objNavi->start_row;

// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
$objSql->setLimitOffset(MEMBER_PMAX, $startno);
$objPage->list_data = $conn->getAll($objSql->getSql());

// �ڡ�����ɽ��
$objView = new SC_AdminView();
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

?>