<?php
/*
 * Copyright ¢í 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

// Ç§¾Ú²ÄÈÝ¤ÎÈ½Äê
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
	
	$header = "\"¸¢¸Â\",\"Ì¾Á°\",\"½êÂ°\",\"¥í¥°¥¤¥óID\",\"²ÔÆ¯¾õ¶·\"\n";
	
	return $header.$data;
}
?>