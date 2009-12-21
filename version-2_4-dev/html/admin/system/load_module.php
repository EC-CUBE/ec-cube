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
require_once(DATA_PATH . "module/Request.php");

// 認証確認
$objSess = new SC_Session();
sfIsSuccess($objSess);

if($_GET['module_id'] != ""){
	$module_id = $_GET['module_id'];
}elseif($_POST['module_id'] != ""){
	$module_id = $_POST['module_id'];
}

if(is_numeric($module_id)) {
	$objQuery = new SC_Query();
	$arrRet = $objQuery->select("main_php", "dtb_module", "module_id = ?", array($module_id));
	$path = MODULE_PATH . $arrRet[0]['main_php'];
	if(file_exists($path)) {
		require_once($path);
		exit;
	} else {
		print("モジュールの取得に失敗しました。:".$path);
	}
}

?>