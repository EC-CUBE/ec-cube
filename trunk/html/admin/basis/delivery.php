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
		$this->tpl_mainpage = 'basis/delivery.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'delivery';
		$this->tpl_mainno = 'basis';
		global $arrPref;
		$this->arrPref = $arrPref;
		global $arrTAXRULE;
		$this->arrTAXRULE = $arrTAXRULE;
		$this->tpl_subtitle = '�����ȼ�����';

	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

switch($_POST['mode']) {
case 'delete':
	// ����դ��쥳���ɤκ��
	sfDeleteRankRecord("dtb_deliv", "deliv_id", $_POST['deliv_id']);
	// ��ɽ��
	sfReload();
	break;
case 'up':
	sfRankUp("dtb_deliv", "deliv_id", $_POST['deliv_id']);
	// ��ɽ��
	sfReload();
	break;
case 'down':
	sfRankDown("dtb_deliv", "deliv_id", $_POST['deliv_id']);
	// ��ɽ��
	sfReload();
	break;
default:
	break;
}

// �����ȼ԰����μ���
$col = "deliv_id, name, service_name";
$where = "del_flg = 0";
$table = "dtb_deliv";
$objQuery->setorder("rank DESC");
$objPage->arrDelivList = $objQuery->select($col, $table, $where);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//--------------------------------------------------------------------------------------------------------------------------------------
