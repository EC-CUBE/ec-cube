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

// 認証可否の判定
$objSess = new SC_Session();
sfIsSuccess($objSess);

$data = lfGetCSVData();
sfCSVDownload($data);
exit();

function lfGetCSVData() {
	global $arrAUTHORITY;
	global $arrWORK;
	
	$oquery = new SC_Query();
	$cols = "authority,name,department,login_id,work";
	$oquery->setwhere("del_flg <> 1");
	$oquery->andwhere("member_id <> ".ADMIN_ID);
	$oquery->setoption("ORDER BY rank DESC");
	$list_data = $oquery->select($cols, "dtb_member");
	$max = count($list_data);
	
	for($i = 0; $i < $max; $i++ ){
		$line = "";
		$line .= "\"".$arrAUTHORITY[$list_data[$i]['authority']]."\",";
		$tmp = ereg_replace("\"","\"\"",$list_data[$i]['name']);
		$line .= "\"".$tmp."\",";
		$tmp = ereg_replace("\"","\"\"",$list_data[$i]['department']);
		$line .= "\"".$tmp."\",";
		$tmp = ereg_replace("\"","\"\"",$list_data[$i]['login_id']);
		$line .= "\"".$tmp."\",";
		$line .= "\"".$arrWORK[$list_data[$i]['work']]."\"\n";
		$data .= $line;
	}
	
	$header = "\"権限\",\"名前\",\"所属\",\"ログインID\",\"稼働状況\"\n";
	
	return $header.$data;
}
?>