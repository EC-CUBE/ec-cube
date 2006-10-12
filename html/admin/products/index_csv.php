<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once(DATA_PATH . "include/csv_output.inc");

$arrREVIEW_CVSCOL = array( 
						'B.name',
						'A.status',
						'A.create_date',
						'A.reviewer_name',
						'A.sex',
						'A.recommend_level',
						'A.title',
						'A.comment'
						);
						
$arrREVIEW_CVSTITLE = array(
						'商品名',
						'レビュー表示',
						'投稿日',
						'投稿者名',
						'性別',
						'おすすめレベル',
						'タイトル',
						'コメント'
						);

// CSV出力データを作成する。(商品)
function lfGetProductsCSV($where, $option, $arrval, $arrOutputCols) {
	global $arrPRODUCTS_CVSCOL;

	$from = "vw_product_class AS prdcls";
	$cols = sfGetCommaList($arrOutputCols);
	
	$objQuery = new SC_Query();
	$objQuery->setoption($option);
	
	$list_data = $objQuery->select($cols, $from, $where, $arrval);
	$max = count($list_data);
	
	// 規格分類名一覧
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
	
	for($i = 0; $i < $max; $i++) {
		// 関連商品情報の付与
		$list_data[$i]['classcategory_id1'] = $arrClassCatName[$list_data[$i]['classcategory_id1']];
		$list_data[$i]['classcategory_id2'] = $arrClassCatName[$list_data[$i]['classcategory_id2']];
		
		// 各項目をCSV出力用に変換する。
		$data .= lfMakeProductsCSV($list_data[$i]);	
	}
	return $data;
}

// CSV出力データを作成する。(レビュー)
function lfGetReviewCSV($where, $option, $arrval) {
	global $arrREVIEW_CVSCOL;

	$from = "dtb_review AS A INNER JOIN dtb_products AS B on A.product_id = B.product_id ";
	$cols = sfGetCommaList($arrREVIEW_CVSCOL);
	
	$objQuery = new SC_Query();
	$objQuery->setoption($option);
	
	$list_data = $objQuery->select($cols, $from, $where, $arrval);	

	$max = count($list_data);
	for($i = 0; $i < $max; $i++) {
		// 各項目をCSV出力用に変換する。
		$data .= lfMakeReviewCSV($list_data[$i]);
	}
	return $data;
}

// 各項目をCSV出力用に変換する。(商品)
function lfMakeProductsCSV($list) {
	global $arrDISP;
	$line = "";
	if(is_array($list)) {
		foreach($list as $key => $val) {
			$tmp = "";
			switch($key) {
			case 'point_rate':
				if($val == "") {
					$tmp = '0';
				} else {
					$tmp = $val;
				}
				break;
			default:
				$tmp = $val;
				break;
			}
	
			$tmp = ereg_replace("[\",]", " ", $tmp);
			$line .= "\"".$tmp."\",";
		}
		// 文末の","を変換
		$line = ereg_replace(",$", "\n", $line);
	}
	return $line;
}


// 各項目をCSV出力用に変換する。(レビュー)
function lfMakeReviewCSV($list) {
	global $arrSex;
	global $arrRECOMMEND;
	global $arrDISP;
	
	$line = "";
	
	foreach($list as $key => $val) {
		$tmp = "";
		switch($key) {
		case 'sex':
			$tmp = $arrSex[$val];
			break;
		case 'recommend_level':
			$tmp = $arrRECOMMEND[$val];
			break;
		case 'status':
			$tmp = $arrDISP[$val];
			break;
		default:
			$tmp = $val;
			break;
		}

		$tmp = ereg_replace("[\",]", " ", $tmp);
		$line .= "\"".$tmp."\",";
	}
	// 文末の","を変換
	$line = ereg_replace(",$", "\n", $line);
	return $line;
}


?>