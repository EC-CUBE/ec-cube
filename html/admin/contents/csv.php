<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../../require.php");
require_once(DATA_PATH . "include/csv_output.inc");

class LC_Page {
	var $arrForm;
	var $arrHidden;

	function LC_Page() {
		$this->tpl_mainpage = 'contents/csv.tpl';
		$this->tpl_subnavi = 'contents/subnavi.tpl';
		$this->tpl_subno = 'csv';
		$this->tpl_subno_csv = $this->arrSubnavi[1];
		$this->tpl_mainno = "contents";
		$this->tpl_subtitle = 'CSV��������';
	}
}
$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();

$objPage->arrSubnavi = $arrSubnavi;
$objPage->arrSubnaviName = $arrSubnaviName;

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

$arrOutput = array();
$arrChoice = array();

$get_tpl_subno_csv = $_GET['tpl_subno_csv'];
// GET���ͤ������Ƥ�����ˤϤ����ͤ򸵤˲���ɽ�����ڤ��ؤ���
if ($get_tpl_subno_csv != ""){
	// �����Ƥ����ͤ��������Ͽ����Ƥ��ʤ����TOP��ɽ��
	if (in_array($get_tpl_subno_csv,$objPage->arrSubnavi)){
		$subno_csv = $get_tpl_subno_csv;
	}else{
		$subno_csv = $objPage->arrSubnavi[1];
	}
} else {
	// GET���ͤ��ʤ����POST���ͤ���Ѥ���
	if ($_POST['tpl_subno_csv'] != ""){
		$subno_csv = $_POST['tpl_subno_csv'];
	}else{
		$subno_csv = $objPage->arrSubnavi[1];
	}
}

// subno���ֹ�����
$subno_id = array_keys($objPage->arrSubnavi,$subno_csv);
$subno_id = $subno_id[0];
// �ǡ�������Ͽ
if ($_POST["mode"] == "confirm") {
	
	// ���顼�����å�
	$objPage->arrErr = lfCheckError($_POST['output_list']);
	
	if (count($objPage->arrErr) <= 0){
		// �ǡ����ι���
		lfUpdCsvOutput($subno_id, $_POST['output_list']);
		
		// ���̤Υ����
		sfReload("tpl_subno_csv=$subno_csv");
	}
}

// ���Ϲ��ܤμ���
$arrOutput = sfSwapArray(sfgetCsvOutput($subno_csv, "WHERE csv_id = ? AND status = 1", array($subno_id)));
$arrOutput = sfarrCombine($arrOutput['col'], $arrOutput['disp_name']);

// ����Ϲ��ܤμ���
$arrChoice = sfSwapArray(sfgetCsvOutput($subno_csv, "WHERE csv_id = ? AND status = 2", array($subno_id)));
$arrChoice = sfarrCombine($arrChoice['col'], $arrChoice['disp_name']);

$objPage->arrOutput=$arrOutput;
$objPage->arrChoice=$arrChoice;


$objPage->SubnaviName = $objPage->arrSubnaviName[$subno_id];
$objPage->tpl_subno_csv = $subno_csv;

// ���̤�ɽ��
$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//---------------------------------------------------------------------------------------------------------------------------------------------------------
/**************************************************************************************************************
 * �ؿ�̾	��lfUpdCsvOutput
 * ��������	��CSV���Ϲ��ܤ򹹿�����
 * ����		���ʤ�
 **************************************************************************************************************/
function lfUpdCsvOutput($csv_id, $arrData = array()){
	$objQuery = new SC_Query();

	// �ҤȤޤ����������Ѥ��ʤ��ǹ�������
	$upd_sql = "UPDATE dtb_csv SET status = 2, rank = NULL, update_date = now() WHERE csv_id = ?";
	$objQuery->query($upd_sql, array($csv_id));

	// ���Ѥ����Τ������ƹ������롣
	if (is_array($arrData)) {
		foreach($arrData as $key => $val){
			$upd_sql = "UPDATE dtb_csv SET status = 1, rank = ? WHERE csv_id = ? AND col = ? ";
			$objQuery->query($upd_sql, array($key+1, $csv_id,$val));
		}
	}
}

/**************************************************************************************************************
 * �ؿ�̾	��lfUpdCsvOutput
 * ��������	��CSV���Ϲ��ܤ򹹿�����
 * ����		���ʤ�
 * ����		���ʤ�
 **************************************************************************************************************/
function lfCheckError($data){
	$objErr = new SC_CheckError();
	$objErr->doFunc( array("���Ϲ���", "output_list"), array("EXIST_CHECK") );
	
	return $objErr->arrErr;

}

