<?php

function lfGetPDFColum($page, $type, $key = "") {
	
	$arrSUBNAME['day'] = "日別";
	$arrSUBNAME['month'] = "月別";
	$arrSUBNAME['year'] = "年別";
	$arrSUBNAME['hour'] = "時間別";
	$arrSUBNAME['wday'] = "曜日別";
	$arrSUBNAME['all'] = "全体";
	$arrSUBNAME['member'] = "会員";
	$arrSUBNAME['nonmember'] = "非会員";
			
	switch($page) {
	// 商品別集計
	case 'products':
		$title = "商品別集計(" . $arrSUBNAME[$type] . ")";
		$arrColSize = array(
			60,
			120,
			220,
			80,
			80,
			80,
		);
		$arrAlign = array(
			'right',
			'center',
			'right',
			'right',
			'right',
			'right',
		);
		break;
	// 職業別集計
	case 'job':
		$title = "職業別集計(" . $arrSUBNAME[$type] . ")";
		$arrColSize = array(
			100,
			100,
			100,
			100,
		);
		$arrAlign = array(
			'right',
			'right',
			'right',
			'right',
		);
		break;
	// 会員別集計
	case 'member':
		$title = "会員別集計(" . $arrSUBNAME[$type] . ")";
		$arrColSize = array(
			100,
			100,
			100,
			100,
		);
		$arrAlign = array(
			'right',
			'right',
			'right',
			'right',
		);
		break;
	// 年代別集計
	case 'age':
		$title = "年代別集計(" . $arrSUBNAME[$type] . ")";
		$arrColSize = array(
			80,
			100,
			100,
			100,
		);
		$arrAlign = array(
			'right',
			'right',
			'right',
			'right',
		);
		break;
	// 期間別集計
	default:
		$title = "期間別集計(" . $arrSUBNAME[$type] . ")";
		$arrColSize = array(
			60,
			60,
			50,
			50,
			80,
			80,
			80,
			80,
			80,
			80,
		);
		$arrAlign = array(
			'right',
			'right',
			'right',
			'right',
			'right',
			'right',
			'right',
			'right',
			'right',
			'right',
		);
		break;
	}
	
	list($arrTitleCol, $arrDataCol) = lfGetCSVColum($page, $key);
		
	return array($arrTitleCol, $arrDataCol, $arrColSize, $arrAlign, $title);
}


function lfGetCSVColum($page, $key = "") {
	switch($page) {
	// 商品別集計
	case 'products':
		$arrTitleCol = array(
			'商品番号',
			'商品名',
			'購入件数',
			'点数',
			'単価',
			'金額'			
		);
		$arrDataCol = array(
			'product_code',
			'name',
			'order_count',
			'products_count',
			'price',
			'total',
		);
		break;
	// 職業別集計
	case 'job':
		$arrTitleCol = array(
			'職業',
			'購入件数',
			'購入合計',
			'購入平均',
		);
		$arrDataCol = array(
			'job_name',
			'order_count',
			'total',
			'total_average',
		);
		break;
	// 会員別集計
	case 'member':
		$arrTitleCol = array(
			'会員',
			'購入件数',
			'購入合計',
			'購入平均',
		);
		$arrDataCol = array(
			'member_name',
			'order_count',
			'total',
			'total_average',
		);
		break;
	// 年代別集計
	case 'age':
		$arrTitleCol = array(
			'年齢',
			'購入件数',
			'購入合計',
			'購入平均',
		);
		$arrDataCol = array(
			'age_name',
			'order_count',
			'total',
			'total_average',
		);
		break;
	// 期間別集計
	default:
		$arrTitleCol = array(
			'期間',
			'購入件数',
			'男性',
			'女性',
			'男性(会員)',
			'男性(非会員)',
			'女性(会員)',
			'女性(非会員)',
			'購入合計',
			'購入平均',		
		);
		$arrDataCol = array(
			$key,
			'total_order',
			'men',
			'women',
			'men_member',
			'men_nonmember',
			'women_member',
			'women_nonmember',
			'total',
			'total_average'
		);
		break;
	}
	
	return array($arrTitleCol, $arrDataCol);
}

// 必要なカラムのみ抽出する(CSVデータで取得する)
function lfGetDataColCSV($arrData, $arrDataCol) {
	$max = count($arrData);
	for($i = 0; $i < $max; $i++) {
		foreach($arrDataCol as $val) {		
			$arrRet[$i][$val] = $arrData[$i][$val];
		}
		$csv_data.= sfGetCSVList($arrRet[$i]);
	}
	return $csv_data;
}

// 必要なカラムのみ抽出する(PDFデータで取得する)
function lfGetDataColPDF($arrData, $arrDataCol, $len) {
	$max = count($arrData);
	for($i = 0; $i < $max; $i++) {
		foreach($arrDataCol as $val) {		
			$arrRet[$i][$val] = sfCutString($arrData[$i][$val], $len);
		}
		$csv_data.= sfGetPDFList($arrRet[$i]);
	}
	return $csv_data;
}


?>