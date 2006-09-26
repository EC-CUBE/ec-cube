<?php
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

$conn = new SC_DbConn();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// ランキングの変更
if($_GET['move'] == 'up') {
	// 正当な数値であった場合
	if(sfIsInt($_GET['id'])){
		lfRunkUp($conn, $_GET['id']);
	} else {
		// エラー処理
		gfPrintLog("error id=".$_GET['id']);
	}
} else if($_GET['move'] == 'down') {
	if(sfIsInt($_GET['id'])){
		lfRunkDown($conn, $_GET['id']);
	}  else {
		// エラー処理
		gfPrintLog("error id=".$_GET['id']);
	}
}

// ページの表示
$location = "Location: " . URL_SYSTEM_TOP . "?pageno=".$_GET['pageno'];
header($location);

// ランキングを上げる。
function lfRunkUp($conn, $id) {
	// 自身のランクを取得する。
	$rank = $conn->getOne("SELECT rank FROM dtb_member WHERE member_id = ".$id);
	// ランクの最大値を取得する。
	$maxno = $conn->getOne("SELECT max(rank) FROM dtb_member");
	// ランクが最大値よりも小さい場合に実行する。
	if($rank < $maxno) {
		// ランクがひとつ上のIDを取得する。
		$sqlse = "SELECT member_id FROM dtb_member WHERE rank = ?";
		$up_id = $conn->getOne($sqlse, $rank + 1);
		// ランク入れ替えの実行
		$conn->query("BEGIN");
		$sqlup = "UPDATE dtb_member SET rank = ? WHERE member_id = ?";
		$conn->query($sqlup, array($rank + 1, $id));
		$conn->query($sqlup, array($rank, $up_id));
		$conn->query("COMMIT");
	}
}

// ランキングを下げる。
function lfRunkDown($conn, $id) {
	// 自身のランクを取得する。
	$rank = $conn->getOne("SELECT rank FROM dtb_member WHERE member_id = ".$id);
	// ランクの最小値を取得する。
	$minno = $conn->getOne("SELECT min(rank) FROM dtb_member");
	// ランクが最大値よりも大きい場合に実行する。
	if($rank > $minno) {
		// ランクがひとつ下のIDを取得する。
		$sqlse = "SELECT member_id FROM dtb_member WHERE rank = ?";
		$down_id = $conn->getOne($sqlse, $rank - 1);
		// ランク入れ替えの実行
		$conn->query("BEGIN");
		$sqlup = "UPDATE dtb_member SET rank = ? WHERE member_id = ?";
		$conn->query($sqlup, array($rank - 1, $id));
		$conn->query($sqlup, array($rank, $down_id));
		$conn->query("COMMIT");
	}
}	
?>