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
 */
require_once("../require.php");

$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objQuery = new SC_Query();

// 前のページで正しく登録手続きが行われた記録があるか判定
SC_Utils::sfIsPrePage($objSiteSess);
GC_Utils::gfPrintLog("before");
// SPSモジュール連携用
if (file_exists(MODULE_PATH . 'mdl_sps/inc/include.php')
 && !$objCartSess->getTotalQuantity()) {

    require_once MODULE_PATH . 'mdl_sps/inc/include.php';
    header("Location: " . ERROR_URL);
    exit;
}

// アクセスの正当性の判定
$uniqid = SC_Utils::sfCheckNormalAccess($objSiteSess, $objCartSess);
GC_Utils::gfPrintLog("after");

$payment_id = $_SESSION["payment_id"];

// 支払いIDが無い場合にはエラー
if($payment_id == ""){
	SC_Utils::sfDispSiteError(PAGE_ERROR, "", true);
}

// 決済情報を取得する
$objDB = new SC_Helper_DB_Ex();

if($objDB->sfColumnExists("dtb_payment", "memo01")){
	$sql = "SELECT module_path, memo01, memo02, memo03, memo04, memo05, memo06, memo07, memo08, memo09, memo10 FROM dtb_payment WHERE payment_id = ?";
	$arrPayment = $objQuery->getall($sql, array($payment_id));
}

if(count($arrPayment) > 0) {
	$path = $arrPayment[0]['module_path'];
	if(file_exists($path)) {
		require_once($path);
		exit;
	} else {
		SC_Utils::sfDispSiteError(FREE_ERROR_MSG, "", true, "モジュールファイルの取得に失敗しました。<br />この手続きは無効となりました。");
	}
}

?>
