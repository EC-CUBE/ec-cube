<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
class LC_SearchProductsPage {
	function LC_SearchProductsPage() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = BLOC_PATH . 'search_products.tpl';	// �ᥤ��
	}
}

$objSubPage = new LC_SearchProductsPage();
$arrSearch = array();	// ��������ɽ����

// ������Υ��ƥ���ID��Ƚ�ꤹ��
$objSubPage->category_id = sfGetCategoryId($_GET['product_id'], $_GET['category_id']);
// ���ƥ��긡��������ꥹ��
$arrRet = sfGetCategoryList('', true, '��');

if(is_array($arrRet)) {
	// ʸ�������������¤���
	foreach($arrRet as $key => $val) {
		$arrRet[$key] = sfCutString($val, SEARCH_CATEGORY_LEN);
	}
}
$objSubPage->arrCatList = $arrRet;

$objSubView = new SC_SiteView();
$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
?>