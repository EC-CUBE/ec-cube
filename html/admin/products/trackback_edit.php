<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrSession;
	function LC_Page() {
		$this->tpl_mainpage = 'products/trackback_edit.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'trackback';
		$this->tpl_subtitle = '�ȥ�å��Хå�����';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSess = new SC_Session();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
sfIsSuccess($objSess);

//������ɤΰ��Ѥ�
foreach ($_POST as $key => $val){
	if (ereg("^search_", $key)){
		$objPage->arrSearchHidden[$key] = $val;
	}
}

// ���֤�����
$objPage->arrTrackBackStatus = $arrTrackBackStatus;

//����ʸ������Ѵ��ѥ����
$arrRegistColumn = array (		
						array( "column" => "update_date"),
						array( "column" => "status"),
						array(	"column" => "title","convert" => "KVa"),
						array(	"column" => "excerpt","convert" => "KVa"),
						array(	"column" => "blog_name","convert" => "KVa"),
						array(	"column" => "url","convert" => "KVa"),
						array(	"column" => "del_flg","convert" => "n")
					);

// �ȥ�å��Хå�ID���Ϥ�
$objPage->tpl_trackback_id = $_POST['trackback_id'];
// �ȥ�å��Хå�����Υ����μ���
$objPage->arrTrackback = lfGetTrackbackData($_POST['trackback_id']);

// ���ʤ��ȤΥȥ�å��Хå�ɽ��������
$count = $objQuery->count("dtb_trackback", "del_flg = 0 AND product_id = ?", array($objPage->arrTrackback['product_id']));
// ξ�������ǽ
$objPage->tpl_status_change = true;

switch($_POST['mode']) {
	// ��Ͽ
	case 'complete':
		//�ե������ͤ��Ѵ�
		$arrTrackback = lfConvertParam($_POST, $arrRegistColumn);
		$objPage->arrErr = lfCheckError($arrTrackback);
		//���顼̵��

		if (!$objPage->arrErr) {
			//��ӥ塼������Խ���Ͽ
			lfRegistTrackbackData($arrTrackback, $arrRegistColumn);
			$objPage->arrTrackback = $arrTrackback;
			$objPage->tpl_onload = "confirm('��Ͽ����λ���ޤ�����');";
		}
		break;

	default:
		break;
}

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);

//------------------------------------------------------------------------------------------------------------------------------------

// ���ϥ��顼�����å�
function lfCheckError($array) {
	$objErr = new SC_CheckError($array);
	$objErr->doFunc(array("�֥�̾", "blog_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�֥����������ȥ�", "title", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�֥���������", "excerpt", LTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�֥�URL", "url", URL_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("����", "status"), array("SELECT_CHECK"));
	return $objErr->arrErr;
}

//----������ʸ������Ѵ�
function lfConvertParam($array, $arrRegistColumn) {
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 *  a :  ���ѱѿ�����Ⱦ�ѱѿ������Ѵ�����
	 */
	// �����̾�ȥ���С��Ⱦ���
	foreach ($arrRegistColumn as $data) {
		$arrConvList[ $data["column"] ] = $data["convert"];
	}
	
	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(strlen(($array[$key])) > 0) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	return $array;
}

// �ȥ�å��Хå�����μ���
function lfGetTrackbackData($trackback_id) {
	global $objPage;
	global $objQuery;
	$select = "tra.trackback_id, tra.product_id, tra.blog_name, tra.title, tra.excerpt, ";
	$select .= "tra.url, tra.status, tra.create_date, tra.update_date, pro.name ";
	$from = "dtb_trackback AS tra LEFT JOIN dtb_products AS pro ON tra.product_id = pro.product_id ";
	$where = "tra.del_flg = 0 AND pro.del_flg = 0 AND tra.trackback_id = ? ";
	$arrTrackback = $objQuery->select($select, $from, $where, array($trackback_id));
	if(!empty($arrTrackback)) {
		$objPage->arrTrackback = $arrTrackback[0];
	} else {
		sfDispError("");
	}
	return $objPage->arrTrackback;
}

// �ȥ�å��Хå�������Խ���Ͽ
function lfRegistTrackbackData($array, $arrRegistColumn) {
	global $objQuery;

	foreach ($arrRegistColumn as $data) {
		if (strlen($array[ $data["column"] ]) > 0 ) {
			$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
		}
		if ($data['column'] == 'update_date'){
			$arrRegist['update_date'] = 'now()';
		}
	}
	//��Ͽ�¹�
	$objQuery->begin();
	$objQuery->update("dtb_trackback", $arrRegist, "trackback_id = '".$_POST['trackback_id']."'");
	$objQuery->commit();
}
?>