<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
						'����̾',
						'��ӥ塼ɽ��',
						'�����',
						'��Ƽ�̾',
						'����',
						'���������٥�',
						'�����ȥ�',
						'������'
						);

$arrTRACKBACK_CVSTITLE = array(
						'����̾',
						'�֥�̾',
						'�֥����������ȥ�',
						'�֥���������',
						'����',
						'�����'
						);

$arrTRACKBACK_CVSCOL = array( 
						'B.name',
						'A.blog_name',
						'A.title',
						'A.excerpt',
						'A.status',
						'A.create_date'
						);

// CSV���ϥǡ�����������롣(����)
function lfGetProductsCSV($where, $option, $arrval, $arrOutputCols) {
	global $arrPRODUCTS_CVSCOL;

	$from = "vw_product_class AS prdcls";
	$cols = sfGetCommaList($arrOutputCols);
	
	$objQuery = new SC_Query();
	$objQuery->setoption($option);
	
	$list_data = $objQuery->select($cols, $from, $where, $arrval);
	$max = count($list_data);
	
	// ����ʬ��̾����
	$arrClassCatName = sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
	
	for($i = 0; $i < $max; $i++) {
		// ��Ϣ���ʾ������Ϳ
		$list_data[$i]['classcategory_id1'] = $arrClassCatName[$list_data[$i]['classcategory_id1']];
		$list_data[$i]['classcategory_id2'] = $arrClassCatName[$list_data[$i]['classcategory_id2']];
		
		// �ƹ��ܤ�CSV�����Ѥ��Ѵ����롣
		$data .= lfMakeProductsCSV($list_data[$i]);	
	}
	return $data;
}

// CSV���ϥǡ�����������롣(MOVILINK)
function lfGetMovilinkCSV($where, $option, $arrval, $arrOutputCols) {
	global $arrPRODUCTS_CVSCOL;

	$from = "dtb_products";
	$cols = sfGetCommaList($arrOutputCols);
	
	$objQuery = new SC_Query();
	$objQuery->setoption($option);
	
	$list_data = $objQuery->select($cols, $from, $where, $arrval);
	$max = count($list_data);
	
	for($i = 0; $i < $max; $i++) {
		// �ƹ��ܤ�CSV�����Ѥ��Ѵ����롣
		$data .= lfMakeProductsCSV($list_data[$i]);	
	}
	return $data;
}

// CSV���ϥǡ�����������롣(��ӥ塼)
function lfGetReviewCSV($where, $option, $arrval) {
	global $arrREVIEW_CVSCOL;

	$from = "dtb_review AS A INNER JOIN dtb_products AS B on A.product_id = B.product_id ";
	$cols = sfGetCommaList($arrREVIEW_CVSCOL);
	
	$objQuery = new SC_Query();
	$objQuery->setoption($option);
	
	$list_data = $objQuery->select($cols, $from, $where, $arrval);	

	$max = count($list_data);
	for($i = 0; $i < $max; $i++) {
		// �ƹ��ܤ�CSV�����Ѥ��Ѵ����롣
		$data .= lfMakeReviewCSV($list_data[$i]);
	}
	return $data;
}

// CSV���ϥǡ�����������롣(�ȥ�å��Хå�)
function lfGetTrackbackCSV($where, $option, $arrval) {
	global $arrTRACKBACK_CVSCOL;

	$from = "dtb_trackback AS A INNER JOIN dtb_products AS B on A.product_id = B.product_id ";
	$cols = sfGetCommaList($arrTRACKBACK_CVSCOL);
	
	$objQuery = new SC_Query();
	$objQuery->setoption($option);
	
	$list_data = $objQuery->select($cols, $from, $where, $arrval);	

	$max = count($list_data);
	for($i = 0; $i < $max; $i++) {
		// �ƹ��ܤ�CSV�����Ѥ��Ѵ����롣
		$data .= lfMakeTrackbackCSV($list_data[$i]);
	}
	return $data;
}

// �ƹ��ܤ�CSV�����Ѥ��Ѵ����롣(����)
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
	
			$tmp = str_replace("\"", "\\\"", $tmp);
			$line .= "\"".$tmp."\",";
		}
		// ʸ����","���Ѵ�
		$line = ereg_replace(",$", "\n", $line);
	}
	return $line;
}


// �ƹ��ܤ�CSV�����Ѥ��Ѵ����롣(��ӥ塼)
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
	// ʸ����","���Ѵ�
	$line = ereg_replace(",$", "\n", $line);
	return $line;
}

// �ƹ��ܤ�CSV�����Ѥ��Ѵ����롣(�ȥ�å��Хå�)
function lfMakeTrackbackCSV($list) {
	global $arrTrackBackStatus;
	global $arrDISP;
	
	$line = "";
	
	foreach($list as $key => $val) {
		$tmp = "";
		switch($key) {
			case 'status':
				$tmp = $arrTrackBackStatus[$val];
				break;
			default:
				$tmp = $val;
				break;
		}

		$tmp = ereg_replace("[\",]", " ", $tmp);
		$line .= "\"".$tmp."\",";
	}
	// ʸ����","���Ѵ�
	$line = ereg_replace(",$", "\n", $line);
	return $line;
}

?>