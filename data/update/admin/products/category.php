<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

class UC_Page {
	function UC_Page() {
		$this->tpl_mainpage = 'products/category.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'category';
		$this->tpl_onload = " fnSetFocus('category_name'); ";
		$this->tpl_subtitle = '���ƥ��꡼��Ͽ';
	}
}

$conn = new SC_DBConn();
$objPage = new UC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �ѥ�᡼���������饹
$objFormParam = new SC_FormParam();

sfPrintR($objFormParam);

// �ѥ�᡼������ν����
lfInitParam();
// POST�ͤμ���
$objFormParam->setParam($_POST);

// �̾���Ͽƥ��ƥ����0�����ꤹ�롣
$objPage->arrForm['parent_category_id'] = $_POST['parent_category_id'];

switch($_POST['mode']) {
case 'edit':
	$objFormParam->convParam();
	$arrRet =  $objFormParam->getHashArray();
	$objPage->arrErr = lfCheckError($arrRet);
	
	if(count($objPage->arrErr) == 0) {
		if($_POST['category_id'] == "") {
			$objQuery = new SC_Query();
			$count = $objQuery->count("dtb_category");
			if($count < CATEGORY_MAX) {			
				lfInsertCat($_POST['parent_category_id']);
			} else {
				print("���ƥ������Ͽ�������Ķ���ޤ�����");
			}
		} else {
			lfUpdateCat($_POST['category_id']);
		}
	} else {
		$objPage->arrForm = array_merge($objPage->arrForm, $objFormParam->getHashArray());
		$objPage->arrForm['category_id'] = $_POST['category_id'];
	}
	break;
case 'pre_edit':
	// �Խ����ܤΥ��ƥ���̾��DB���������롣
	$oquery = new SC_Query();
	$where = "category_id = ?";
	$cat_name = $oquery->get("dtb_category", "category_name", $where, array($_POST['category_id']));
	// ���Ϲ��ܤ˥��ƥ���̾�����Ϥ��롣
	$objPage->arrForm['category_name'] = $cat_name;
	// POST�ǡ���������Ѥ�
	$objPage->arrForm['category_id'] = $_POST['category_id'];
	break;
case 'delete':
	$objQuery = new SC_Query();
	// �ҥ��ƥ���Υ����å�
	$where = "parent_category_id = ? AND del_flg = 0";
	$count = $objQuery->count("dtb_category", $where, array($_POST['category_id']));
	if($count != 0) {
		$objPage->arrErr['category_name'] = "�� �ҥ��ƥ��꤬¸�ߤ��뤿�����Ǥ��ޤ���<br>";
	}
	// ��Ͽ���ʤΥ����å�
	$where = "category_id = ? AND del_flg = 0";
	$count = $objQuery->count("dtb_products", $where, array($_POST['category_id']));
	if($count != 0) {
		$objPage->arrErr['category_name'] = "�� ���ƥ�����˾��ʤ�¸�ߤ��뤿�����Ǥ��ޤ���<br>";
	}	
	
	if(!isset($objPage->arrErr['category_name'])) {
		// ����դ��쥳���ɤκ��(��������٤��θ���ƥ쥳���ɤ��Ⱥ�����롣)
		sfDeleteRankRecord("dtb_category", "category_id", $_POST['category_id'], "", true);
	}
	break;
case 'up':
	$objQuery = new SC_Query();
	$objQuery->begin();
	$up_id = lfGetUpRankID($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
	if($up_id != "") {
		// ��Υ��롼�פ�rank���鸺�������
		$my_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
		// ��ʬ�Υ��롼�פ�rank�˲û������
		$up_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $up_id);
		if($my_count > 0 && $up_count > 0) {
			// ��ʬ�Υ��롼�פ˲û�
			lfUpRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id'], $up_count);
			// ��Υ��롼�פ��鸺��
			lfDownRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $up_id, $my_count);
		}
	}
	$objQuery->commit();
	break;
case 'down':
	$objQuery = new SC_Query();
	$objQuery->begin();
	$down_id = lfGetDownRankID($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
	if($down_id != "") {
		// ���Υ��롼�פ�rank�˲û������
		$my_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id']);
		// ��ʬ�Υ��롼�פ�rank���鸺�������
		$down_count = lfCountChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $down_id);
		if($my_count > 0 && $down_count > 0) {
			// ��ʬ�Υ��롼�פ��鸺��
			lfUpRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $down_id, $my_count);
			// ���Υ��롼�פ˲û�
			lfDownRankChilds($objQuery, "dtb_category", "parent_category_id", "category_id", $_POST['category_id'], $down_count);
		}
	}
	$objQuery->commit();
	break;
case 'tree':
	break;
default:
	$objPage->arrForm['parent_category_id'] = 0;
	break;
}

$objPage->arrList = lfGetCat($objPage->arrForm['parent_category_id']);
$objPage->arrTree = sfGetCatTree($objPage->arrForm['parent_category_id']);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------------

?>