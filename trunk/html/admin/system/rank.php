<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

$conn = new SC_DbConn();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// ��󥭥󥰤��ѹ�
if($_GET['move'] == 'up') {
	// �����ʿ��ͤǤ��ä����
	if(sfIsInt($_GET['id'])){
		lfRunkUp($conn, $_GET['id']);
	} else {
		// ���顼����
		gfPrintLog("error id=".$_GET['id']);
	}
} else if($_GET['move'] == 'down') {
	if(sfIsInt($_GET['id'])){
		lfRunkDown($conn, $_GET['id']);
	}  else {
		// ���顼����
		gfPrintLog("error id=".$_GET['id']);
	}
}

// �ڡ�����ɽ��
$location = "Location: " . URL_SYSTEM_TOP . "?pageno=".$_GET['pageno'];
header($location);

// ��󥭥󥰤�夲�롣
function lfRunkUp($conn, $id) {
	// ���ȤΥ�󥯤�������롣
	$rank = $conn->getOne("SELECT rank FROM dtb_member WHERE member_id = ".$id);
	// ��󥯤κ����ͤ�������롣
	$maxno = $conn->getOne("SELECT max(rank) FROM dtb_member");
	// ��󥯤������ͤ��⾮�������˼¹Ԥ��롣
	if($rank < $maxno) {
		// ��󥯤��ҤȤľ��ID��������롣
		$sqlse = "SELECT member_id FROM dtb_member WHERE rank = ?";
		$up_id = $conn->getOne($sqlse, $rank + 1);
		// ��������ؤ��μ¹�
		$conn->query("BEGIN");
		$sqlup = "UPDATE dtb_member SET rank = ? WHERE member_id = ?";
		$conn->query($sqlup, array($rank + 1, $id));
		$conn->query($sqlup, array($rank, $up_id));
		$conn->query("COMMIT");
	}
}

// ��󥭥󥰤򲼤��롣
function lfRunkDown($conn, $id) {
	// ���ȤΥ�󥯤�������롣
	$rank = $conn->getOne("SELECT rank FROM dtb_member WHERE member_id = ".$id);
	// ��󥯤κǾ��ͤ�������롣
	$minno = $conn->getOne("SELECT min(rank) FROM dtb_member");
	// ��󥯤������ͤ����礭�����˼¹Ԥ��롣
	if($rank > $minno) {
		// ��󥯤��ҤȤĲ���ID��������롣
		$sqlse = "SELECT member_id FROM dtb_member WHERE rank = ?";
		$down_id = $conn->getOne($sqlse, $rank - 1);
		// ��������ؤ��μ¹�
		$conn->query("BEGIN");
		$sqlup = "UPDATE dtb_member SET rank = ? WHERE member_id = ?";
		$conn->query($sqlup, array($rank - 1, $id));
		$conn->query($sqlup, array($rank, $down_id));
		$conn->query("COMMIT");
	}
}	
?>