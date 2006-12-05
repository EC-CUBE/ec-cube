<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'products/review.tpl';
		global $arrRECOMMEND;
		$this->arrRECOMMEND = $arrRECOMMEND;
		global $arrSex;
		$this->arrSex = $arrSex;
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query(); 

//---- ��Ͽ�ѥ��������
$arrRegistColumn = array(
							 array(  "column" => "review_id", "convert" => "aKV" ),
							 array(  "column" => "product_id", "convert" => "aKV" ),
							 array(  "column" => "reviewer_name", "convert" => "aKV" ),
							 array(  "column" => "reviewer_url", "convert" => "a"),
							 array(  "column" => "sex", "convert" => "n" ),
							 array(  "column" => "email", "convert" => "a" ),
							 array(  "column" => "recommend_level", "convert" => "n" ),
							 array(  "column" => "title", "convert" => "aKV" ),
							 array(  "column" => "comment", "convert" => "aKV" ),

						);
switch ($_POST['mode']){
case 'confirm':
	$arrForm = lfConvertParam($_POST, $arrRegistColumn);
	$objPage->arrErr = lfErrorCheck($arrForm);
	//��ʣ��å�������Ƚ��
	$flag = $objQuery->count("dtb_review","product_id = ? AND title = ? ", array($arrForm['product_id'], $arrForm['title']));

	if ($flag > 0){
		$objPage->arrErr['title'] .= "��ʣ���������ȥ����Ͽ�Ǥ��ޤ���";
	}
		
	//���顼�����å�
	if($objPage->arrErr == ""){
		//��ʣ�����ȥ�Ǥʤ�
		if($flag == 0){
			//����̾�μ���
			$arrForm['name'] = $objQuery->get("dtb_products", "name", "product_id = ? ", array($arrForm['product_id']));
			$objPage->arrForm = $arrForm;
			$objPage->tpl_mainpage = 'products/review_confirm.tpl';
		}
	} else {
		//����̾�μ���
		$arrForm['name'] = $objQuery->get("dtb_products", "name", "product_id = ? ", array($arrForm['product_id']));	
		$objPage->arrForm = $arrForm;
	}
	break;

case 'return':
	foreach($_POST as $key => $val){
		$objPage->arrForm[ $key ] = $val;
	}
	
	//����̾�μ���
	$objPage->arrForm['name'] = $objQuery->get("dtb_products", "name", "product_id = ? ", array($objPage->arrForm['product_id']));
	if(empty($objPage->arrForm['name'])) {
		sfDispSiteError(PAGE_ERROR);
	}
	break;

case 'complete':
	$arrForm = lfConvertParam($_POST, $arrRegistColumn);
	$arrErr = lfErrorCheck($arrForm);
	//��ʣ��å�������Ƚ��
	$flag = $objQuery->count("dtb_review","product_id = ? AND title = ? ", array($arrForm['product_id'], $arrForm['title']));
	//���顼�����å�
	if ($arrErr == ""){
		//��ʣ�����ȥ�Ǥʤ�
		if($flag == 0) {
			//��Ͽ�¹�
			lfRegistRecommendData($arrForm, $arrRegistColumn);
			//��ӥ塼�񤭹��ߴ�λ�ڡ�����
			header("Location: ./review_complete.php");
			exit;
		}
	} else {
		if($flag > 0) {
			sfDispSiteError(PAGE_ERROR);
		}
	}
	break;

default:
	if(sfIsInt($_GET['product_id'])) {
		//���ʾ���μ���
		$arrForm = $objQuery->select("product_id, name", "dtb_products", "del_flg = 0 AND status = 1 AND product_id=?", array($_GET['product_id']));
		if(empty($arrForm)) {
			sfDispSiteError(PAGE_ERROR);
		}
		$objPage->arrForm = $arrForm[0];
	}
	break;

}

$objView->assignobj($objPage);
$objView->display($objPage->tpl_mainpage);					

//-----------------------------------------------------------------------------------------------------------------------------------


//���顼�����å�

function lfErrorCheck() {
	$objErr = new SC_CheckError();
	$objErr->doFunc(array("����ID", "product_id", INT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));			
	$objErr->doFunc(array("��Ƽ�̾", "reviewer_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("URL", "reviewer_url", MTEXT_LEN), array("MAX_LENGTH_CHECK", "URL_CHECK"));
	$objErr->doFunc(array("���������٥�", "recommend_level"), array("SELECT_CHECK"));
	$objErr->doFunc(array("�����ȥ�", "title", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("������", "comment", LTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));

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

//��Ͽ�¹�
function lfRegistRecommendData ($array, $arrRegistColumn) {
	global $objQuery;
	
	// ����Ͽ
	foreach ($arrRegistColumn as $data) {
		if (strlen($array[ $data["column"] ]) > 0 ) {
			$arrRegist[ $data["column"] ] = $array[ $data["column"] ];
		}
	}
	$arrRegist['create_date'] = 'now()';
	$arrRegist['update_date'] = 'now()';
	$arrRegist['creator_id'] = '0';
	//-- ��Ͽ�¹�
	$objQuery->begin();
	$objQuery->insert("dtb_review", $arrRegist);
	$objQuery->commit();
}

?>