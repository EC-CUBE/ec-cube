<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
 
 *		check.php 稼働・非稼働の切替
 */
require_once("../require.php");

$conn = new SC_DbConn();

// 認証可否の判定
$objSess = new SC_Session();
SC_Utils_Ex::sfIsSuccess($objSess);

// GET値の正当性を判定する
if(SC_Utils_Ex::sfIsInt($_GET['id']) && ($_GET['no'] == 1 || $_GET['no'] == 0)){
	$sqlup = "UPDATE dtb_member SET work = ? WHERE member_id = ?";
	$conn->query($sqlup, array($_GET['no'], $_GET['id']));
} else {
	// エラー処理
	gfPrintLog("error id=".$_GET['id']);
}

// ページの表示
$location = "Location: " . ADMIN_SYSTEM_URLPATH . "?pageno=".$_GET['pageno'];
header($location);
?>
