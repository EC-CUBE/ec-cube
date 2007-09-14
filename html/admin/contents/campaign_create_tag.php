<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "include/file_manager.inc");

class LC_Page {
	
	function LC_Page() {
		$this->tpl_mainpage = 'contents/campaign_create_tag.tpl';
		$this->tpl_mainno = 'create';
		$this->tpl_subtitle = '��������';
		/*
		 session_start����no-cache�إå������������뤳�Ȥ�
		 �����ץܥ�����ѻ���ͭ�������ڤ�ɽ�����������롣
		 private-no-expire:���饤����ȤΥ���å������Ĥ��롣
		*/
		session_cache_limiter('private-no-expire');		
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

switch($_POST['mode']) {

// ���ʸ���
case  'search':
	// POST�ͤΰ����Ѥ�
	$objPage->arrForm = $_POST;
	// ����ʸ���ζ����Ѵ�
	lfConvertParam();
	
	$where = "del_flg = 0";
	
	// where������
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
			case 'search_product_id':
				if($val != "") {
					$where .= " AND product_id = ?";
					if(!sfIsInt($val)) $val = 0;
					$arrval[] = $val;
				}
				break;
			default:
				break;
		}
	}
	
	$order = "update_date DESC, product_id DESC ";
	
	// �ɤ߹�����ȥơ��֥�λ���
	$col = "product_id, name, category_id, main_list_image, status, product_code, price01, stock, stock_unlimited";
	$from = "vw_products_nonclass AS noncls ";

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
	break;
	
// ����ɽ��
case 'view':

	// ����������
	$create_tag = lfGetCreateTag($_POST['product_id']);
	$objPage->tpl_create_tag = $create_tag;
	break;
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

/* ���������� */
function lfGetCreateTag($product_id) {
	
	global $objQuery;
	
	// �񤭹��ߥ���
	$read_file = file_get_contents(CAMPAIGN_BLOC_PATH . "cart_tag.tpl");
	$read_file = ereg_replace("<{assign_product_id}>", $product_id, $read_file);
	// �����ȥ�����¸�ѥե��������
	$create_tag .= "<!--{* ������ID$product_id *}-->\n";
	$create_tag .= $read_file;
	
	return $create_tag;	
}
?>