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
		$this->tpl_mainpage = 'basis/kiyaku.tpl';
		$this->tpl_subnavi = 'basis/subnavi.tpl';
		$this->tpl_subno = 'kiyaku';
		$this->tpl_subtitle = '���������Ͽ';
		$this->tpl_mainno = 'basis';
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
		if($_POST['kiyaku_id'] == "") {
			lfInsertClass($objPage->arrForm);	// ��������
		} else {
			lfUpdateClass($objPage->arrForm);	// ��¸�Խ�
		}
		// ��ɽ��
		sfReload();
	} else {
		// POST�ǡ���������Ѥ�
		$objPage->tpl_kiyaku_id = $_POST['kiyaku_id'];
	}
	break;
// ���
case 'delete':
	sfDeleteRankRecord("dtb_kiyaku", "kiyaku_id", $_POST['kiyaku_id'], "", true);
	// ��ɽ��
	sfReload();
	break;
// �Խ�������
case 'pre_edit':
	// �Խ����ܤ�DB���������롣
	$where = "kiyaku_id = ?";
	$arrRet = $objQuery->select("kiyaku_text, kiyaku_title", "dtb_kiyaku", $where, array($_POST['kiyaku_id']));
	// ���Ϲ��ܤ˥��ƥ���̾�����Ϥ��롣
	$objPage->arrForm['kiyaku_title'] = $arrRet[0]['kiyaku_title'];
	$objPage->arrForm['kiyaku_text'] = $arrRet[0]['kiyaku_text'];
	// POST�ǡ���������Ѥ�
	$objPage->tpl_kiyaku_id = $_POST['kiyaku_id'];
break;
case 'down':
	sfRankDown("dtb_kiyaku", "kiyaku_id", $_POST['kiyaku_id']);
	// ��ɽ��
	sfReload();
	break;
case 'up':
	sfRankUp("dtb_kiyaku", "kiyaku_id", $_POST['kiyaku_id']);
	// ��ɽ��
	sfReload();
	break;
default:
	break;
}

// ���ʤ��ɹ�
$where = "del_flg <> 1";
$objQuery->setorder("rank DESC");
$objPage->arrKiyaku = $objQuery->select("kiyaku_title, kiyaku_text, kiyaku_id", "dtb_kiyaku", $where);

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//--------------------------------------------------------------------------------------------------------------------------------

/* DB�ؤ����� */
function lfInsertClass($arrData) {
	$objQuery = new SC_Query();
	// INSERT�����ͤ�������롣
	$sqlval['kiyaku_title'] = $arrData['kiyaku_title'];
	$sqlval['kiyaku_text'] = $arrData['kiyaku_text'];
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$sqlval['rank'] = $objQuery->max("dtb_kiyaku", "rank") + 1;
	$sqlval['update_date'] = "Now()";
	$sqlval['create_date'] = "Now()";
	// INSERT�μ¹�
	$ret = $objQuery->insert("dtb_kiyaku", $sqlval);
	return $ret;
}

/* DB�ؤι��� */
function lfUpdateClass($arrData) {
	$objQuery = new SC_Query();
	// UPDATE�����ͤ�������롣
	$sqlval['kiyaku_title'] = $arrData['kiyaku_title'];
	$sqlval['kiyaku_text'] = $arrData['kiyaku_text'];
	$sqlval['update_date'] = "Now()";
	$where = "kiyaku_id = ?";
	// UPDATE�μ¹�
	$ret = $objQuery->update("dtb_kiyaku", $sqlval, $where, array($_POST['kiyaku_id']));
	return $ret;
}

/* ����ʸ������Ѵ� */
function lfConvertParam($array) {
	// ʸ���Ѵ�
	$arrConvList['kiyaku_title'] = "KVa";
	$arrConvList['kiyaku_text'] = "KVa";

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
	$objErr->doFunc(array("���󥿥��ȥ�", "kiyaku_title", SMTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��������", "kiyaku_text", MLTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	if(!isset($objErr->arrErr['name'])) {
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("kiyaku_id, kiyaku_title", "dtb_kiyaku", "del_flg = 0 AND kiyaku_title = ?", array($_POST['kiyaku_title']));
		// �Խ���Υ쥳���ɰʳ���Ʊ��̾�Τ�¸�ߤ�����		
		if ($arrRet[0]['kiyaku_id'] != $_POST['kiyaku_id'] && $arrRet[0]['kiyaku_title'] == $_POST['kiyaku_title']) {
			$objErr->arrErr['name'] = "�� ����Ʊ�����Ƥ���Ͽ��¸�ߤ��ޤ���<br>";
		}
	}
	return $objErr->arrErr;
}
?>