<?php
require_once("../require.php");

define("ZIP_CSV_LINE_MAX", 8192);
define("IMAGE_MAX", 680);	// ������ɽ���Ŀ�

$path = ROOT_DIR . "html/install/KEN_ALL.CSV";

$objQuery = new SC_Query();
$objSess = new SC_Session();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

$fp = fopen($path, "r");

// ������IE��256�Х��Ȱʾ������äƤ���ɽ���򳫻Ϥ��롣
for($i = 0; $i < 256; $i++) {
	print(" ");
}
print("\n");
MyFlush();

if(!$fp) {
	sfErrorHeader(">> " . $path . "�μ����˼��Ԥ��ޤ�����");
} else {
	print("<img src='/img/install/main_w.jpg'><br>");
	MyFlush();
	
	// CSV�η���������
	$line = 0;
	while(!feof($fp)) {
		fgets($fp, ZIP_CSV_LINE_MAX);
		$line++;
	}
	
	print("<img src='/img/install/space_w.gif'>");
	MyFlush();
		
	// �ե�����ݥ��󥿤��᤹
	fseek($fp, 0);
	
	// ��������ɽ������������롣
	$disp_line = intval($line / IMAGE_MAX);
	
	// ���˽񤭹��ޤ줿�ǡ����������
	$end_cnt = $objQuery->count("mtb_zip");
	$objQuery->begin();
	
	$cnt = 1;
	$img_cnt = 0;
	while (!feof($fp)) {
		$arrCSV = fgetcsv($fp, ZIP_CSV_LINE_MAX);
		
		// ���Ǥ˽񤭹��ޤ줿�ǡ��������Ф���
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
		// $disp_line�老�Ȥ˿�Ľɽ������
		if($cnt % $disp_line == 0 && $img_cnt < IMAGE_MAX) {
			print("<img src='/img/install/graph_1_w.gif'>");
			MyFlush();
			$img_cnt++;
		}
	}
	$objQuery->commit();
	fclose($fp);
	print("<img src='/img/install/space_w.gif'><br>");
}

function MyFlush() {
	flush();
	ob_end_flush();
	ob_start();
}

?>