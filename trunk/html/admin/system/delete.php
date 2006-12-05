<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

$conn = new SC_DbConn();
$oquery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// member_id�Υ����å�
if(sfIsInt($_GET['id'])){
	// �쥳���ɤκ��
	$conn->query("BEGIN");
	fnRenumberRank($conn, $oquery, $_GET['id']);
	fnDeleteRecord($conn, $_GET['id']);
	$conn->query("COMMIT");
} else {
	// ���顼����
	gfPrintLog("error id=".$_GET['id']);
}

// �ڡ�����ɽ��
$location = "Location: " . URL_SYSTEM_TOP . "?pageno=".$_GET['pageno'];
header($location);

// ��󥭥󥰤ο���ľ��
function fnRenumberRank($conn, $oquery, $id) {
	$where = "member_id = $id";
	// ��󥯤μ���
	$rank = $oquery->get("dtb_member", "rank", $where);
	// ��������쥳���ɤ���Υ�󥭥󥰤򲼤���RANK�ζ��������롣
	$sqlup = "UPDATE dtb_member SET rank = (rank - 1) WHERE rank > $rank AND del_flg <> 1";
	// UPDATE�μ¹�
	$ret = $conn->query($sqlup);
	return $ret;
}

// �쥳���ɤκ��(����ե饰��ON�ˤ���)
function fnDeleteRecord($conn, $id) {
	// ��󥯤�ǲ��̤ˤ��롢DEL�ե饰ON
	$sqlup = "UPDATE dtb_member SET rank = 0, del_flg = 1 WHERE member_id = $id";
	// UPDATE�μ¹�
	$ret = $conn->query($sqlup);
	return $ret;
}
?>