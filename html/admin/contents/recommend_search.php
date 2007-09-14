<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	
	function LC_Page() {
		$this->tpl_mainpage = 'contents/recomend_search.tpl';
		$this->tpl_mainno = 'contents';
		$this->tpl_subnavi = '';
		$this->tpl_subno = "";
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

if ($_POST['mode'] == "search") {
	
	// POST�ͤΰ����Ѥ�
	$objPage->arrForm = $_POST;
	// ����ʸ���ζ����Ѵ�
	lfConvertParam();
	
	
	$where = "del_flg = 0";
	
	/* ���ϥ��顼�ʤ� */
	foreach ($objPage->arrForm as $key => $val) {
		if($val == "") {
			continue;
		}
		
		switch ($key) {
			case 'search_name':
				
				$where .= " AND name ILIKE ?";
				$arrval[] = "%$val%";
				break;
			case 'search_category_id':
				// �ҥ��ƥ���ID�μ���
				$arrRet = sfGetChildsID("dtb_category", "parent_category_id", "category_id", $val);
				$tmp_where = "";
				foreach ($arrRet as $val) {
					if($tmp_where == "") {
						$tmp_where.= " AND ( category_id = ?";
					} else {
						$tmp_where.= " OR category_id = ?";
					}
					$arrval[] = $val;
				}
				$where.= $tmp_where . " )";
				break;
			case 'search_product_code':
				$where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? GROUP BY product_id)";
				$where .= " OR product_code ILIKE ?";
				$arrval[] = "%$val%";
				$arrval[] = "%$val%";
				break;
			default:
				break;
		}
	}
	
	$order = "update_date DESC, product_id DESC";
	
	// �ɤ߹�����ȥơ��֥�λ���
	$col = "product_id, name, category_id, main_list_image, status, product_code, price01, stock, stock_unlimited";
	$from = "vw_products_nonclass AS noncls ";
		
	$objQuery = new SC_Query();
	// �Կ��μ���
	$linemax = $objQuery->count("dtb_products", $where, $arrval);
	$objPage->tpl_linemax = $linemax;				// ���郎�������ޤ�����ɽ����

	// �ڡ�������ν���
	if(is_numeric($_POST['search_page_max'])) {	
		$page_max = $_POST['search_page_max'];
	} else {
		$page_max = SEARCH_PMAX;
	}

	// �ڡ�������μ���
	$objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, $page_max, "fnNaviSearchOnlyPage", NAVI_PMAX);
	$objPage->tpl_strnavi = $objNavi->strnavi;		// ɽ��ʸ����
	$startno = $objNavi->start_row;

	// �����ϰϤλ���(���Ϲ��ֹ桢�Կ��Υ��å�)
	if(DB_TYPE != "mysql") $objQuery->setlimitoffset($page_max, $startno);
	// ɽ�����
	$objQuery->setorder($order);

	// view��ʹ��ߤ򤫤���(mysql��)
	sfViewWhere("&&noncls_where&&", $where, $arrval, $objQuery->order . " " .  $objQuery->setlimitoffset($page_max, $startno, true));

	// ������̤μ���
	$objPage->arrProducts = $objQuery->select($col, $from, $where, $arrval);
}

// ���ƥ������
$objPage->arrCatList = sfGetCategoryList();






//----���ڡ���ɽ��
$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);



//---------------------------------------------------------------------------------------------------------------------------------------------------------

/* ����ʸ������Ѵ� */
function lfConvertParam() {
	global $objPage;
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 */
	$arrConvList['search_name'] = "KVa";
	$arrConvList['search_product_code'] = "KVa";
	
	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(isset($objPage->arrForm[$key])) {
			$objPage->arrForm[$key] = mb_convert_kana($objPage->arrForm[$key] ,$val);
		}
	}
}


?>