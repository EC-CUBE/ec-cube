<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

ini_set("max_execution_time", 600);

define("ZIP_CSV_LINE_MAX", 8192);
define("IMAGE_MAX", 680);	// 画像の表示個数

$path = HTML_PATH . "admin/basis/KEN_ALL.CSV";

$objQuery = new SC_Query();
$objSess = new SC_Session();

// 認証可否の判定
sfIsSuccess($objSess);

$fp = fopen($path, "r");

// 一部のIEは256バイト以上受け取ってから表示を開始する。
for($i = 0; $i < 256; $i++) {
	print(" ");
}
print("\n");
MyFlush();

if(!$fp) {
	sfErrorHeader(">> " . $path . "の取得に失敗しました。");
} else {
	print("<img src='".URL_DIR."img/install/main_w.jpg'><br>");
	MyFlush();
	
	// CSVの件数を数える
	$line = 0;
	while(!feof($fp)) {
		fgets($fp, ZIP_CSV_LINE_MAX);
		$line++;
	}
	
	print("<img src='".URL_DIR."img/install/space_w.gif'>");
	MyFlush();
		
	// ファイルポインタを戻す
	fseek($fp, 0);
	
	// 画像を一個表示する件数を求める。
	$disp_line = intval($line / IMAGE_MAX);
	
	// 既に書き込まれたデータを数える
	$end_cnt = $objQuery->count("mtb_zip");
	$cnt = 1;
	$img_cnt = 0;
	while (!feof($fp)) {
		$arrCSV = fgetcsv($fp, ZIP_CSV_LINE_MAX);
		
		// すでに書き込まれたデータを飛ばす。
		if($cnt > $end_cnt) {
			$sqlval['code'] = $arrCSV[0];
			$sqlval['old_zipcode'] = $arrCSV[1];
			$sqlval['zipcode'] = $arrCSV[2];
			$sqlval['state_kana'] = $arrCSV[3];
			$sqlval['city_kana'] = $arrCSV[4];
			$sqlval['town_kana'] = $arrCSV[5];
			$sqlval['state'] = $arrCSV[6];
			$sqlval['city'] = $arrCSV[7];
			$sqlval['town'] = $arrCSV[8];
			$sqlval['flg1'] = $arrCSV[9];
			$sqlval['flg2'] = $arrCSV[10];
			$sqlval['flg3'] = $arrCSV[11];
			$sqlval['flg4'] = $arrCSV[12];
			$sqlval['flg5'] = $arrCSV[13];
			$sqlval['flg6'] = $arrCSV[14];	
			$objQuery->insert("mtb_zip", $sqlval);
		}
		
		$cnt++;
		// $disp_line件ごとに進捗表示する
		if($cnt % $disp_line == 0 && $img_cnt < IMAGE_MAX) {
			print("<img src='/img/install/graph_1_w.gif'>");
			MyFlush();
			$img_cnt++;
		}
	}
	fclose($fp);
	
	print("<img src='".URL_DIR."img/install/space_w.gif'><br>\n");
	print("<table width='700' height='50' border='0' cellpadding='0' cellspacing='0' bgcolor='#494E5F'>\n");
	print("<tr>\n");
	print("<td align='center'><a href='javascript:window.close()'><img src='".URL_DIR."img/install/close.gif' alt='CLOSE' width='85' height='22' border='0' /></a></td>\n");
	print("</tr>\n");
	print("</table>\n");
}

function MyFlush() {
	flush();
	ob_end_flush();
	ob_start();
}

?>