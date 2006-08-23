<?php

require_once("../require.php");

define("ZIP_CSV_LINE_MAX", 8192);
define("IMAGE_MAX", 680);	// 画像の表示個数

$path = ROOT_DIR . "html/install/KEN_ALL.CSV";

$objQuery = new SC_Query();
//$objSess = new SC_Session();

// 認証可否の判定
//sfIsSuccess($objSess);

$fp = fopen($path, "r");

// 一部のIEは256バイト以上受け取ってから表示を開始する。
for($i = 0; $i < 256; $i++) {
	print(" ");
}
print("\n");
flush();

if(!$fp) {
	sfErrorHeader(">> " . $path . "の取得に失敗しました。");
} else {
	print("<img src='/img/install/main_w.jpg'><br>");
	flush();
	
	// CSVの件数を数える
	$line = 0;
	while(!feof($fp)) {
		fgets($fp, ZIP_CSV_LINE_MAX);
		$line++;
	}
	
	print("<img src='/img/install/space_w.gif'>");
	flush();
		
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
			flush();
			$img_cnt++;
		}
	}
	fclose($fp);
	print("<img src='/img/install/space_w.gif'><br>");
	print($cnt - 1 . "/" . $line);
}
?>