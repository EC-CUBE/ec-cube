<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 *		check.php 稼働・非稼働の切替
 */
require_once("../require.php");

$conn = new SC_DbConn();

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

// GET値の正当性を判定する
if(sfIsInt($_GET['id']) && ($_GET['no'] == 1 || $_GET['no'] == 0)){
	$sqlup = "UPDATE dtb_member SET work = ? WHERE member_id = ?";
	$conn->query($sqlup, array($_GET['no'], $_GET['id']));
} else {
	// エラー処理
	gfPrintLog("error id=".$_GET['id']);
}

// ページの表示
$location = "Location: " . URL_SYSTEM_TOP . "?pageno=".$_GET['pageno'];
header($location);
?>
