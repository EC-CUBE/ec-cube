<?php
/**
 * ��Х��륵����/���ƥ��꡼����
 */

require_once('../require.php');

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'products/category_list.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_title = '���ƥ�������ڡ���';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, DEF_LAYOUT);

// ���ƥ��꡼�����������롣
lfGetCategories(@$_GET['category_id'], true, $objPage);

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

/**
 * ���򤵤줿���ƥ��꡼�Ȥ��λҥ��ƥ��꡼�ξ�����������
 * �ڡ������֥������Ȥ˳�Ǽ���롣
 *
 * @param string $category_id ���ƥ��꡼ID
 * @param boolean $count_check ͭ���ʾ��ʤ��ʤ����ƥ��꡼��������ɤ���
 * @param object &$objPage �ڡ������֥�������
 * @return void
 */
function lfGetCategories($category_id, $count_check = false, &$objPage) {
	// ���ƥ��꡼��������ID��������롣
	$category_id = sfGetCategoryId('', $category_id);
	if ($category_id == 0) {
		sfDispSiteError(CATEGORY_NOT_FOUND);
	}

	$arrCategory = null;	// ���򤵤줿���ƥ��꡼
	$arrChildren = array();	// �ҥ��ƥ��꡼

	$arrAll = sfGetCatTree($category_id, $count_check);
	foreach ($arrAll as $category) {
		// ���򤵤줿���ƥ��꡼�ξ��
		if ($category['category_id'] == $category_id) {
			$arrCategory = $category;
			continue;
		}

		// �ط��Τʤ����ƥ��꡼�ϥ����åפ��롣
		if ($category['parent_category_id'] != $category_id) {
			continue;
		}

		// �ҥ��ƥ��꡼�ξ��ϡ�¹���ƥ��꡼��¸�ߤ��뤫�ɤ�����Ĵ�٤롣
		$arrGrandchildrenID = sfGetUnderChildrenArray($arrAll, 'parent_category_id', 'category_id', $category['category_id']);
		$category['has_children'] = count($arrGrandchildrenID) > 0;
		$arrChildren[] = $category;
	}

	if (!isset($arrCategory)) {
		sfDispSiteError(CATEGORY_NOT_FOUND);
	}

	// �ҥ��ƥ��꡼�ξ��ʿ����פ��롣
	$children_product_count = 0;
	foreach ($arrChildren as $category) {
		$children_product_count += $category['product_count'];
	}

	// ���򤵤줿���ƥ��꡼��ľ°�ξ��ʤ�������ϡ��ҥ��ƥ��꡼����Ƭ���ɲä��롣
	if ($arrCategory['product_count'] > $children_product_count) {
		$arrCategory['product_count'] -= $children_product_count;	// �ҥ��ƥ��꡼�ξ��ʿ��������
		$arrCategory['has_children'] = false;	// ���ʰ����ڡ��������ܤ����뤿�ᡣ
		array_unshift($arrChildren, $arrCategory);
	}

	// ��̤��Ǽ���롣
	$objPage->arrCategory = $arrCategory;
	$objPage->arrChildren = $arrChildren;
}
?>
