<?php

class LC_CatPage {
	function LC_CatPage() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'frontparts/category.tpl';	// �ᥤ��
	}
}

$objSubPage = new LC_CatPage();
$objSubView = new SC_SiteView();

// ������Υ��ƥ���ID��Ƚ�ꤹ��
$category_id = sfGetCategoryId($_GET['product_id'], $_GET['category_id']);

if($category_id != "") {
	// ������Υ��ƥ���ID�οƥ��ƥ���ID��������롣
	$objQuery = new SC_Query();
	$parent_category_id = $objQuery->get("dtb_category", "parent_category_id", "category_id = ?", array($category_id));
}

// ������Υ��ƥ���γ��ؤ�Ƚ�ꤹ��
$level = lfGetCategoryLevel($category_id);

// ���ƥ�������μ���
$objSubPage->arrCategory = lfGetCategoryList($category_id, $level);

$objSubPage->tpl_category_id = $category_id;
$objSubPage->tpl_parent_category_id = $parent_category_id;

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
/* ������Υ��ƥ���Υ�٥��������� */
function lfGetCategoryLevel($category_id) {
	$objQuery = new SC_Query();
	$where = "category_id = ?";
	$ret = $objQuery->get("dtb_category", "level", $where, array($category_id));
	return $ret;
}

/* ���ƥ���ΰ������������ */
function lfGetCategoryList($category_id, $level) {
	$objQuery = new SC_Query();

	switch($level) {
	case '':
		break;
	case '1':
		$objQuery->setorder("rank DESC");
		$where = "parent_category_id = ? AND product_count > 0";
		$arrRet = $objQuery->select("*", "vw_category_count", $where, array($category_id));
		break;
	default:
		$arrRet = lfGetCatParentsList($category_id);
		break;
	}
	return $arrRet;
}

/* �ķ���οƥ��ƥ����������� */
function lfGetCatParentsList($category_id) {
	$objQuery = new SC_Query();
	// ���ʤ�°���륫�ƥ���ID��Ĥ˼���
	$arrCatID = sfGetParents($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);
	// ��°����ƥ��ƥ������
	$count = count($arrCatID);
	if ($count > 0) {
		for($cnt = 0; $cnt < $count; $cnt++) {
			if ($where == "") {
				$where = "(parent_category_id = ?";
			} else {
				$where.= " OR parent_category_id = ?";
			}
			$arrVal[] = $arrCatID[$cnt];
		}
		$where.= ")";
	}
	
	if($where != "") {
		$where.= " AND product_count > 0 AND level >= 2";
	} else {
		$where = "product_count > 0 AND level >= 2";
	}
	
	$objQuery->setorder("rank DESC");
	$arrRet = $objQuery->select("*", "vw_category_count", $where, $arrVal);
	return $arrRet;
}
?>