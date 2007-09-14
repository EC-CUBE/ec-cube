<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 * ��Х��륵����/�ᥤ�󥫥ƥ��꡼
 */

class LC_CatPage {
	function LC_CatPage() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'frontparts/bloc/category.tpl';	// �ᥤ��ƥ�ץ졼��
	}
}

$objSubPage = new LC_CatPage();
$objSubView = new SC_MobileView();

$objSubPage = lfGetMainCat(true, $objSubPage);

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);

//-----------------------------------------------------------------------------------------------------------------------------------

// �ᥤ�󥫥ƥ��꡼�μ���
function lfGetMainCat($count_check = false, $objSubPage) {
	$objQuery = new SC_Query();
	$col = "*";
	$from = "dtb_category left join dtb_category_total_count using (category_id)";
	// �ᥤ�󥫥ƥ��꡼�Ȥ���ľ���Υ��ƥ��꡼��������롣
	$where = 'level <= 2 AND del_flg = 0';
	// ��Ͽ���ʿ��Υ����å�
	if($count_check) {
		$where .= " AND product_count > 0";
	}
	$objQuery->setoption("ORDER BY rank DESC");
	$arrRet = $objQuery->select($col, $from, $where);

	// �ᥤ�󥫥ƥ��꡼����Ф��롣
	$arrMainCat = array();
	foreach ($arrRet as $cat) {
		if ($cat['level'] != 1) {
			continue;
		}

		// �ҥ��ƥ��꡼����Ĥ��ɤ�����Ĵ�٤롣
		$arrChildrenID = sfGetUnderChildrenArray($arrRet, 'parent_category_id', 'category_id', $cat['category_id']);
		$cat['has_children'] = count($arrChildrenID) > 0;
		$arrMainCat[] = $cat;
	}

	$objSubPage->arrCat = $arrMainCat;
	return $objSubPage;
}
?>
