<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	function LC_Page() {
		$this->tpl_mainpage = 'products/class.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_subno = 'class';
		$this->tpl_subtitle = '������Ͽ';
		$this->tpl_mainno = 'products';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

// �׵�Ƚ��
switch($_POST['mode']) {
// �Խ�����
case 'edit':
	// POST�ͤΰ����Ѥ�
	$objPage->arrForm = $_POST;
	// ����ʸ�����Ѵ�
	$objPage->arrForm = lfConvertParam($objPage->arrForm);
	// ���顼�����å�
	$objPage->arrErr = lfErrorCheck();
	if(count($objPage->arrErr) <= 0) {
		if($_POST['class_id'] == "") {
			lfInsertClass($objPage->arrForm);	// ��������
		} else {
			lfUpdateClass($objPage->arrForm);	// ��¸�Խ�
		}
		// ��ɽ��
		sfReload();
	} else {
		// POST�ǡ���������Ѥ�
		$objPage->tpl_class_id = $_POST['class_id'];
	}
	break;
// ���
case 'delete':
	sfDeleteRankRecord("dtb_class", "class_id", $_POST['class_id'], "", true);
	$objQuery = new SC_Query();
	$objQuery->delete("dtb_classcategory", "class_id = ?", $_POST['class_id']);
	// ��ɽ��
	sfReload();
	break;
// �Խ�������
case 'pre_edit':
	// �Խ����ܤ�DB���������롣
	$where = "class_id = ?";
	$class_name = $objQuery->get("dtb_class", "name", $where, array($_POST['class_id']));
	// ���Ϲ��ܤ˥��ƥ���̾�����Ϥ��롣
	$objPage->arrForm['name'] = $class_name;
	// POST�ǡ���������Ѥ�
	$objPage->tpl_class_id = $_POST['class_id'];
break;
case 'down':
	sfRankDown("dtb_class", "class_id", $_POST['class_id']);
	// ��ɽ��
	sfReload();
	break;
case 'up':
	sfRankUp("dtb_class", "class_id", $_POST['class_id']);
	// ��ɽ��
	sfReload();
	break;
default:
	break;
}

// ���ʤ��ɹ�
$where = "del_flg <> 1";
$objQuery->setorder("rank DESC");
$objPage->arrClass = $objQuery->select("name, class_id", "dtb_class", $where);
$objPage->arrClassCatCount = sfGetClassCatCount();

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//--------------------------------------------------------------------------------------------------------------------------------

/* DB�ؤ����� */
function lfInsertClass($arrData) {
	$objQuery = new SC_Query();
	// INSERT�����ͤ�������롣
	$sqlval['name'] = $arrData['name'];
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['rank'] = $objQuery->max("dtb_class", "rank") + 1;
	$sqlval['create_date'] = "now()";
	$sqlval['update_date'] = "now()";
	// INSERT�μ¹�
	$ret = $objQuery->insert("dtb_class", $sqlval);
	
	return $ret;
}

/* DB�ؤι��� */
function lfUpdateClass($arrData) {
	$objQuery = new SC_Query();
	// UPDATE�����ͤ�������롣
	$sqlval['name'] = $arrData['name'];
	$sqlval['update_date'] = "Now()";
	$where = "class_id = ?";
	// UPDATE�μ¹�
	$ret = $objQuery->update("dtb_class", $sqlval, $where, array($arrData['class_id']));
	return $ret;
}

/* ����ʸ������Ѵ� */
function lfConvertParam($array) {
	// ʸ���Ѵ�
	$arrConvList['name'] = "KVa";

	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

/* ���ϥ��顼�����å� */
function lfErrorCheck() {
	$objErr = new SC_CheckError();
	$objErr->doFunc(array("����̾", "name", STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	
	if(!isset($objErr->arrErr['name'])) {
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("class_id, name", "dtb_class", "del_flg = 0 AND name = ?", array($_POST['name']));
		// �Խ���Υ쥳���ɰʳ���Ʊ��̾�Τ�¸�ߤ�����		
		if ($arrRet[0]['class_id'] != $_POST['class_id'] && $arrRet[0]['name'] == $_POST['name']) {
			$objErr->arrErr['name'] = "�� ����Ʊ�����Ƥ���Ͽ��¸�ߤ��ޤ���<br>";
		}
	}
	return $objErr->arrErr;
}
?>