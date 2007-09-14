<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("./require.php");
//header("Content-Type: text/json; charset=euc-jp");


$conn = new SC_DBconn(ZIP_DSN);

// ���ϥ��顼�����å�
$arrErr = fnErrorCheck();

// ���ϥ��顼�ξ��Ͻ�λ
if(count($arrErr) == 0) {

// ͹���ֹ渡��ʸ����
$zipcode = $_GET['zip01'].$_GET['zip02'];
$zipcode = mb_convert_kana($zipcode ,"n");
$sqlse = "SELECT state, city, town FROM mtb_zip WHERE zipcode = ?";

$data_list = $conn->getAll($sqlse, array($zipcode));

// ����ǥå������ͤ�ȿž�����롣
$arrREV_PREF = array_flip($arrPref);
$pref = $arrREV_PREF[$data_list[0]['state']];
$city = $data_list[0]['city'];
$town =  $data_list[0]['town'];
/*
	��̳�ʤ����������ɤ����ǡ����򤽤Τޤޥ���ݡ��Ȥ����
	�ʲ��Τ褦��ʸ�������äƤ���Τ�	�к����롣
	���ʣ�����������ܡ�
	���ʲ��˷Ǻܤ��ʤ����
*/
$town = ereg_replace("��.*��$","",$town);
$town = ereg_replace("�ʲ��˷Ǻܤ��ʤ����","",$town);

// ͹���ֹ椬ȯ�����줿���
if(count($data_list[0]) > 0) {
	echo "{'pref':'$pref','city':'$city','town':'$town','flag':'1'}" ;
} else {
    echo "{'flag' : '0'}" ;
    }
}
/* ���ϥ��顼�Υ����å� */
function fnErrorCheck() {
	// ���顼��å���������ν����
	$objErr = new SC_CheckError();
	
	// ͹���ֹ�
	$objErr->doFunc( array("͹���ֹ�1",'zip01',ZIP01_LEN ) ,array( "NUM_COUNT_CHECK" ) );
	$objErr->doFunc( array("͹���ֹ�2",'zip02',ZIP02_LEN ) ,array( "NUM_COUNT_CHECK" ) );
	
	return $objErr->arrErr;
}

?>