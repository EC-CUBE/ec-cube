<?php

class LC_PankuzuPage {
	function LC_PankuzuPage() {
		/** ɬ���ѹ����� **/
		$this->tpl_mainpage = 'frontparts/pankuzu.tpl';	// �ᥤ��
	}
}

$objSubPage = new LC_PankuzuPage();
$objSubView = new SC_SiteView();

// ������Υ��ƥ���ID��Ƚ�ꤹ��
$category_id = sfGetCategoryId($_GET['product_id'], $_GET['category_id']);
// �ѥ󥯥����ƥ���̾�μ���
list($objSubPage->arrCatID, $objSubPage->arrCatName) = lfGetCatName($category_id);

$objSubView->assignobj($objSubPage);
$objSubView->display($objSubPage->tpl_mainpage);
//-----------------------------------------------------------------------------------------------------------------------------------
/* �ķ���οƥ��ƥ������� */
function lfGetCatName($category_id) {
	if($category_id != 0) {
		$objQuery = new SC_Query();
		$arrCatID = sfGetParents($objQuery, "dtb_category", "parent_category_id", "category_id", $category_id);
		// �ķ���οƥ��ƥ���̾�����
		$arrList = sfGetParentsCol($objQuery, "dtb_category", "category_id", "category_name", $arrCatID);
		$count = count($arrList);
		for($cnt = 0; $cnt < $count; $cnt++) {
			$arrCatName[] = $arrList[$cnt]['category_name'];
		}
	}
	return array($arrCatID, $arrCatName);
}
?>