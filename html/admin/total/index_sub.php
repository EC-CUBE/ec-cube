<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
function lfGetPDFColum($page, $type, $key = "") {
	
	$arrSUBNAME['day'] = "����";
	$arrSUBNAME['month'] = "����";
	$arrSUBNAME['year'] = "ǯ��";
	$arrSUBNAME['hour'] = "������";
	$arrSUBNAME['wday'] = "������";
	$arrSUBNAME['all'] = "����";
	$arrSUBNAME['member'] = "���";
	$arrSUBNAME['nonmember'] = "����";
			
	switch($page) {
	// �����̽���
	case 'products':
		$title = "�����̽���(" . $arrSUBNAME[$type] . ")";
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
	// �����̽���
	case 'job':
		$title = "�����̽���(" . $arrSUBNAME[$type] . ")";
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
	// ����̽���
	case 'member':
		$title = "����̽���(" . $arrSUBNAME[$type] . ")";
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
	// ǯ���̽���
	case 'age':
		$title = "ǯ���̽���(" . $arrSUBNAME[$type] . ")";
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
	// �����̽���
	default:
		$title = "�����̽���(" . $arrSUBNAME[$type] . ")";
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
	// �����̽���
	case 'products':
		$arrTitleCol = array(
			'�����ֹ�',
			'����̾',
			'�������',
			'����',
			'ñ��',
			'���'			
		);
		$arrDataCol = array(
			'product_code',
			'product_name',
			'order_count',
			'products_count',
			'price',
			'total',
		);
		break;
	// �����̽���
	case 'job':
		$arrTitleCol = array(
			'����',
			'�������',
			'�������',
			'����ʿ��',
		);
		$arrDataCol = array(
			'job_name',
			'order_count',
			'total',
			'total_average',
		);
		break;
	// ����̽���
	case 'member':
		$arrTitleCol = array(
			'���',
			'�������',
			'�������',
			'����ʿ��',
		);
		$arrDataCol = array(
			'member_name',
			'order_count',
			'total',
			'total_average',
		);
		break;
	// ǯ���̽���
	case 'age':
		$arrTitleCol = array(
			'ǯ��',
			'�������',
			'�������',
			'����ʿ��',
		);
		$arrDataCol = array(
			'age_name',
			'order_count',
			'total',
			'total_average',
		);
		break;
	// �����̽���
	default:
		$arrTitleCol = array(
			'����',
			'�������',
			'����',
			'����',
			'����(���)',
			'����(����)',
			'����(���)',
			'����(����)',
			'�������',
			'����ʿ��',		
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

// ɬ�פʥ����Τ���Ф���(CSV�ǡ����Ǽ�������)
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

// ɬ�פʥ����Τ���Ф���(PDF�ǡ����Ǽ�������)
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