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
		$this->tpl_mainpage = 'products/review_edit.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'review';
		global $arrRECOMMEND;
		$this->arrRECOMMEND = $arrRECOMMEND;
		$this->tpl_subtitle = '��ӥ塼����';
		global $arrSex;
		$this->arrSex = $arrSex;
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

//����ʸ������Ѵ��ѥ����
$arrRegistColumn = array (		
								array( "column" => "update_date"),
								array( "column" => "status"),
								array( "column" => "recommend_level"),		
								array(	"column" => "title","convert" => "KVa"),
								array(	"column" => "comment","convert" => "KVa"),
								array(	"column" => "reviewer_name","convert" => "KVa"),
								array(	"column" => "reviewer_url","convert" => "KVa"),
								array(	"column" => "sex","convert" => "n")
								
							);

//��ӥ塼ID���Ϥ�
$objPage->tpl_review_id = $_POST['review_id'];
//��ӥ塼����Υ����μ���
$objPage->arrReview = lfGetReviewData($_POST['review_id']);
//��Ͽ�ѤߤΥ��ơ��������Ϥ�
$objPage->tpl_pre_status = $objPage->arrReview['status'];
//���ʤ��ȤΥ�ӥ塼ɽ��������
$count = $objQuery->count("dtb_review", "del_flg=0 AND status=1 AND product_id=?", array($objPage->arrReview['product_id']));
//ξ�������ǽ
$objPage->tpl_status_change = true;

switch($_POST['mode']) {
//��Ͽ
case 'complete':
	//�ե������ͤ��Ѵ�
	$arrReview = lfConvertParam($_POST, $arrRegistColumn);
	$objPage->arrErr = lfCheckError($arrReview);
	//���顼̵��
	if (!$objPage->arrErr){
		//��ӥ塼������Խ���Ͽ
		lfRegistReviewData($arrReview, $arrRegistColumn);
		$objPage->arrReview = $arrReview;
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
	$objErr->doFunc(array("���������٥�", "recommend_level"), array("SELECT_CHECK"));
	$objErr->doFunc(array("�����ȥ�", "title", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("������", "comment", LTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��Ƽ�̾", "reviewer_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�ۡ���ڡ������ɥ쥹", "reviewer_url", URL_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�����ȥ�", "title", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("����", "sex", STEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
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

//��ӥ塼����μ���
function lfGetReviewData($review_id){
	global $objPage;
	global $objQuery;
	$select="review_id, A.product_id, reviewer_name, sex, recommend_level, ";
	$select.="reviewer_url, title, comment, A.status, A.create_date, A.update_date, name";
	$from = "dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id ";
	$where = "A.del_flg = 0 AND B.del_flg = 0 AND review_id = ? ";
	$arrReview = $objQuery->select($select, $from, $where, array($review_id));
	if(!empty($arrReview)) {
		$objPage->arrReview = $arrReview[0];
	} else {
		sfDispError("");
	}
	return $objPage->arrReview;
}

//��ӥ塼������Խ���Ͽ
function lfRegistReviewData($array, $arrRegistColumn){
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
	$objQuery->update("dtb_review", $arrRegist, "review_id='".$_POST['review_id']."'");
	$objQuery->commit();
}
?>