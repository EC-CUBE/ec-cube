<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'products/product_rank.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'product_rank';
		$this->tpl_subtitle = '�����¤��ؤ�';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

$objPage->tpl_pageno = $_POST['pageno'];

// �̾���Ͽƥ��ƥ����0�����ꤹ�롣
$objPage->arrForm['parent_category_id'] = $_POST['parent_category_id'];

switch($_POST['mode']) {
case 'up':
	$where = "category_id = " . addslashes($_POST['parent_category_id']);
	sfRankUp("dtb_products", "product_id", $_POST['product_id'], $where);
	break;
case 'down':
	$where = "category_id = " . addslashes($_POST['parent_category_id']);
	sfRankDown("dtb_products", "product_id", $_POST['product_id'], $where);
	break;
case 'move':
	$key = "pos-".$_POST['product_id'];
	$input_pos = mb_convert_kana($_POST[$key], "n");
	if(sfIsInt($input_pos)) {
		$where = "category_id = " . addslashes($_POST['parent_category_id']);
		sfMoveRank("dtb_products", "product_id", $_POST['product_id'], $input_pos, $where);
	}
	break;
case 'tree':
	// ���ƥ�������ؤϡ��ڡ����ֹ�򥯥ꥢ���롣
	$objPage->tpl_pageno = "";
	break;
default:
	break;
}

$objPage->arrTree = sfGetCatTree($_POST['parent_category_id']);
$objPage->arrProductsList = lfGetProduct($_POST['parent_category_id']);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------
/* �����ɤ߹��� */
function lfGetProduct($category_id) {
	global $objPage;
	
	$objQuery = new SC_Query();
	$col = "product_id, name, main_list_image, rank, product_code";
	$table = "vw_products_nonclass AS noncls ";
	$where = "del_flg = 0 AND category_id = ?";
	
	// �Կ��μ���
	$linemax = $objQuery->count("dtb_products", $where, array($category_id));
	// ��̡��������ɽ����
	$objPage->tpl_linemax = $linemax;
	
	$objNavi = new SC_PageNavi($objPage->tpl_pageno, $linemax, SEARCH_PMAX, "fnNaviPage", NAVI_PMAX);
	$startno = $objNavi->start_row;
	$objPage->tpl_strnavi = $objNavi->strnavi;		// Naviɽ��ʸ����
	$objPage->tpl_pagemax = $objNavi->max_page;		// �ڡ���������ʡ־�ز��ء�ɽ��Ƚ���ѡ�
	$objPage->tpl_disppage = $objNavi->now_page;	// ɽ���ڡ����ֹ�ʡ־�ز��ء�ɽ��Ƚ���ѡ�
			
	// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
	if(DB_TYPE != "mysql") $objQuery->setlimitoffset(SEARCH_PMAX, $startno);
	
	$objQuery->setorder("rank DESC");
	
	// view��ʹ��ߤ򤫤���(mysql��)
	sfViewWhere("&&noncls_where&&", $where, array($category_id), $objQuery->order . " " .  $objQuery->setlimitoffset(SEARCH_PMAX, $startno, true));
	
	$arrRet = $objQuery->select($col, $table, $where, array($category_id));
	return $arrRet;
}

?>