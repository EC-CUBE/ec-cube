<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("./require.php");

class LC_Page {
	var $tpl_state;
	var $tpl_city;
	var $tpl_town;
	var $tpl_onload;
	var $tpl_message;
	function CPage() {
		$this->tpl_message = "����򸡺����Ƥ��ޤ���";
	}
}

$conn = new SC_DBconn(ZIP_DSN);
$objPage = new LC_Page();
$objView = new SC_SiteView(false);

// ���ϥ��顼�����å�
$arrErr = fnErrorCheck();

// ���ϥ��顼�ξ��Ͻ�λ
if(count($arrErr) > 0) {
	$objPage->tpl_start = "window.close();";
}

// ͹���ֹ渡��ʸ����
$zipcode = $_GET['zip1'].$_GET['zip2'];
$zipcode = mb_convert_kana($zipcode ,"n");
$sqlse = "SELECT state, city, town FROM mtb_zip WHERE zipcode = ?";

$data_list = $conn->getAll($sqlse, array($zipcode));

// ����ǥå������ͤ�ȿž�����롣
$arrREV_PREF = array_flip($arrPref);

$objPage->tpl_state = $arrREV_PREF[$data_list[0]['state']];
$objPage->tpl_city = $data_list[0]['city'];
$town =  $data_list[0]['town'];
/*
	��̳�ʤ����������ɤ����ǡ����򤽤Τޤޥ���ݡ��Ȥ����
	�ʲ��Τ褦��ʸ�������äƤ���Τ�	�к����롣
	���ʣ�����������ܡ�
	���ʲ��˷Ǻܤ��ʤ����
*/
$town = ereg_replace("��.*��$","",$town);
$town = ereg_replace("�ʲ��˷Ǻܤ��ʤ����","",$town);
$objPage->tpl_town = $town;

// ͹���ֹ椬ȯ�����줿���
if(count($data_list) > 0) {
	$func = "fnPutAddress('" . $_GET['input1'] . "','" . $_GET['input2']. "');";
	$objPage->tpl_onload = "$func";
	$objPage->tpl_start = "window.close();";
} else {
	$objPage->tpl_message = "�������뽻�꤬���Ĥ���ޤ���Ǥ�����";
}

/* �ڡ�����ɽ����*/
$objView->assignobj($objPage);
$objView->display("input_zip.tpl");

/* ���ϥ��顼�Υ����å� */
function fnErrorCheck() {
	// ���顼��å���������ν����
	$objErr = new SC_CheckError();
	
	// ͹���ֹ�
	$objErr->doFunc( array("͹���ֹ�1",'zip1',ZIP01_LEN ) ,array( "NUM_COUNT_CHECK" ) );
	$objErr->doFunc( array("͹���ֹ�2",'zip2',ZIP02_LEN ) ,array( "NUM_COUNT_CHECK" ) );
	
	return $objErr->arrErr;
}

?>