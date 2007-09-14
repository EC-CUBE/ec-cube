<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $arrHidden;
	var $arrForm;
	var $tpl_class_name;
	var $arrClassCat;
	function LC_Page() {
		$this->tpl_mainpage = 'products/classcategory.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_subno = 'class';
		$this->tpl_subtitle = '������Ͽ';
		$this->tpl_mainno = 'products';
	}
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

$get_check = false;

// ����ID�Υ����å�
if(sfIsInt($_GET['class_id'])) {
	// ����̾�μ���
	$objPage->tpl_class_name = $objQuery->get("dtb_class", "name", "class_id = ?", array($_GET['class_id']));
	if($objPage->tpl_class_name != "") {
		// ����ID�ΰ����Ѥ�
		$objPage->arrHidden['class_id'] = $_GET['class_id'];
		$get_check = true;
	}
}

if(!$get_check) {
	// ������Ͽ�ڡ��������Ф���
	header("Location: " . URL_CLASS_REGIST);
	exit;
}

// �������� or �Խ�
switch($_POST['mode']) {
// ��Ͽ�ܥ��󲡲�
case 'edit':
	// POST�ͤΰ����Ѥ�
	$objPage->arrForm = $_POST;
	// ����ʸ�����Ѵ�
	$_POST = lfConvertParam($_POST);
	// ���顼�����å�
	$objPage->arrErr = lfErrorCheck();
	if(count($objPage->arrErr) <= 0) {
		if($_POST['classcategory_id'] == "") {
			lfInsertClass();	// DB�ؤν񤭹���
		} else {
			lfUpdateClass();	// DB�ؤν񤭹���
		}
		// ��ɽ��
		sfReload("class_id=" . $_GET['class_id']);
	} else {
		// POST�ǡ���������Ѥ�
		$objPage->tpl_classcategory_id = $_POST['classcategory_id'];
	}
	break;
// ���
case 'delete':
	// ����դ��쥳���ɤκ��
	$where = "class_id = " . addslashes($_POST['class_id']);
	sfDeleteRankRecord("dtb_classcategory", "classcategory_id", $_POST['classcategory_id'], $where, true);
	break;
// �Խ�������
case 'pre_edit':
	// �Խ����ܤ�DB���������롣
	$where = "classcategory_id = ?";
	$name = $objQuery->get("dtb_classcategory", "name", $where, array($_POST['classcategory_id']));
	// ���Ϲ��ܤ˥��ƥ���̾�����Ϥ��롣
	$objPage->arrForm['name'] = $name;
	// POST�ǡ���������Ѥ�
	$objPage->tpl_classcategory_id = $_POST['classcategory_id'];
	break;
case 'down':
	$where = "class_id = " . addslashes($_POST['class_id']);
	sfRankDown("dtb_classcategory", "classcategory_id", $_POST['classcategory_id'], $where);
	break;
case 'up':
	$where = "class_id = " . addslashes($_POST['class_id']);
	sfRankUp("dtb_classcategory", "classcategory_id", $_POST['classcategory_id'], $where);
	break;
default:
	break;
}

// ����ʬ����ɹ�
$where = "del_flg <> 1 AND class_id = ?";
$objQuery->setorder("rank DESC");
$objPage->arrClassCat = $objQuery->select("name, classcategory_id", "dtb_classcategory", $where, array($_GET['class_id']));

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//-----------------------------------------------------------------------------------------------------------------------------

/* DB�ؤ����� */
function lfInsertClass() {
	$objQuery = new SC_Query();
	$objQuery->begin();
	// �Ƶ���ID��¸�ߥ����å�
	$where = "del_flg <> 1 AND class_id = ?";
	$ret = 	$objQuery->get("dtb_class", "class_id", $where, array($_POST['class_id']));
	if($ret != "") {	
		// INSERT�����ͤ�������롣
		$sqlval['name'] = $_POST['name'];
		$sqlval['class_id'] = $_POST['class_id'];
		$sqlval['creator_id'] = $_SESSION['member_id'];
		$sqlval['rank'] = $objQuery->max("dtb_classcategory", "rank", $where, array($_POST['class_id'])) + 1;
		$sqlval['create_date'] = "now()";
		$sqlval['update_date'] = "now()";
		// INSERT�μ¹�
		$ret = $objQuery->insert("dtb_classcategory", $sqlval);
	}
	$objQuery->commit();
	return $ret;
}

/* DB�ؤι��� */
function lfUpdateClass() {
	$objQuery = new SC_Query();
	// UPDATE�����ͤ�������롣
	$sqlval['name'] = $_POST['name'];
	$sqlval['update_date'] = "Now()";
	$where = "classcategory_id = ?";
	// UPDATE�μ¹�
	$ret = $objQuery->update("dtb_classcategory", $sqlval, $where, array($_POST['classcategory_id']));
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
	$objErr->doFunc(array("ʬ��̾", "name", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
	if(!isset($objErr->arrErr['name'])) {
		$objQuery = new SC_Query();
		$where = "class_id = ? AND name = ?";
		$arrRet = $objQuery->select("classcategory_id, name", "dtb_classcategory", $where, array($_GET['class_id'], $_POST['name']));
		// �Խ���Υ쥳���ɰʳ���Ʊ��̾�Τ�¸�ߤ�����
		if ($arrRet[0]['classcategory_id'] != $_POST['classcategory_id'] && $arrRet[0]['name'] == $_POST['name']) {
			$objErr->arrErr['name'] = "�� ����Ʊ�����Ƥ���Ͽ��¸�ߤ��ޤ���<br>";
		}
	}
	return $objErr->arrErr;
}
?>