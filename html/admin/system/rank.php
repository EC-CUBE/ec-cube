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