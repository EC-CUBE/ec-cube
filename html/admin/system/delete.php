<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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