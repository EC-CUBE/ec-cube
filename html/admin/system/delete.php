<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

$conn = new SC_DbConn();
$oquery = new SC_Query();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// member_idのチェック
if(sfIsInt($_GET['id'])){
	// レコードの削除
	$conn->query("BEGIN");
	fnRenumberRank($conn, $oquery, $_GET['id']);
	fnDeleteRecord($conn, $_GET['id']);
	$conn->query("COMMIT");
} else {
	// エラー処理
	gfPrintLog("error id=".$_GET['id']);
}

// ページの表示
$location = "Location: " . URL_SYSTEM_TOP . "?pageno=".$_GET['pageno'];
header($location);

// ランキングの振り直し
function fnRenumberRank($conn, $oquery, $id) {
	$where = "member_id = $id";
	// ランクの取得
	$rank = $oquery->get("dtb_member", "rank", $where);
	// 削除したレコードより上のランキングを下げてRANKの空きを埋める。
	$sqlup = "UPDATE dtb_member SET rank = (rank - 1) WHERE rank > $rank AND del_flg <> 1";
	// UPDATEの実行
	$ret = $conn->query($sqlup);
	return $ret;
}

// レコードの削除(削除フラグをONにする)
function fnDeleteRecord($conn, $id) {
	// ランクを最下位にする、DELフラグON
	$sqlup = "UPDATE dtb_member SET rank = 0, del_flg = 1 WHERE member_id = $id";
	// UPDATEの実行
	$ret = $conn->query($sqlup);
	return $ret;
}
?>