<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *		check.php ��Ư�����Ư������
 */
require_once("../require.php");

$conn = new SC_DbConn();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// GET�ͤ���������Ƚ�ꤹ��
if(sfIsInt($_GET['id']) && ($_GET['no'] == 1 || $_GET['no'] == 0)){
	$sqlup = "UPDATE dtb_member SET work = ? WHERE member_id = ?";
	$conn->query($sqlup, array($_GET['no'], $_GET['id']));
} else {
	// ���顼����
	gfPrintLog("error id=".$_GET['id']);
}

// �ڡ�����ɽ��
$location = "Location: " . URL_SYSTEM_TOP . "?pageno=".$_GET['pageno'];
header($location);
?>
