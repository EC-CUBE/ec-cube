<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page {
	var $arrCatList;
	var $arrSRANK;
	var $arrForm;
	var $arrSubList;
	var $arrHidden;
	var $arrTempImage;
	var $arrSaveImage;
	var $tpl_mode;
	function LC_Page() {
		$this->tpl_mainpage = 'products/product.tpl';
		$this->tpl_subnavi = 'products/subnavi.tpl';
		$this->tpl_mainno = 'products';		
		$this->tpl_subno = 'product';
		$this->tpl_subtitle = '������Ͽ';
		global $arrSRANK;
		$this->arrSRANK = $arrSRANK;
		global $arrDISP;
		$this->arrDISP = $arrDISP;
		global $arrCLASS;
		$this->arrCLASS = $arrCLASS;
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrSTATUS_VALUE;
		$this->arrSTATUS_VALUE = $arrSTATUS_VALUE;
		global $arrSTATUS_IMAGE;
		$this->arrSTATUS_IMAGE = $arrSTATUS_IMAGE;
		global $arrDELIVERYDATE;
		$this->arrDELIVERYDATE = $arrDELIVERYDATE;
		$this->tpl_nonclass = true;
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objSiteInfo = new SC_SiteInfo();
$objQuery = new SC_Query();

// ǧ�ڲ��ݤ�Ƚ��
$objSess = new SC_Session();
sfIsSuccess($objSess);

// �ե�����������饹
$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR, FTP_IMAGE_TEMP_DIR, FTP_IMAGE_SAVE_DIR, MULTI_WEB_SERVER_MODE);

// �ե��������ν����
lfInitFile();
// Hidden����Υǡ���������Ѥ�
$objUpFile->setHiddenFileList($_POST);

// �����ѥ�᡼���ΰ����Ѥ�
foreach ($_POST as $key => $val) {
	if (ereg("^search_", $key)) {
		$objPage->arrSearchHidden[$key] = $val;	
	}
}

// FORM�ǡ����ΰ����Ѥ�
$objPage->arrForm = $_POST;

switch($_POST['mode']) {
// �������̤�����Խ�
case 'pre_edit':
case 'copy' :
	// �Խ���
	if(sfIsInt($_POST['product_id'])){
		// DB���龦�ʾ�����ɹ�
		$arrForm = lfGetProduct($_POST['product_id']);
		// DB�ǡ�����������ե�����̾���ɹ�
		$objUpFile->setDBFileList($arrForm);
		
		if($_POST['mode'] == "copy"){
			$arrForm["copy_product_id"] = $arrForm["product_id"];
			$arrForm["product_id"] = "";
			// �����ե�����Υ��ԡ�
			$arrKey = $objUpFile->keyname;
			$arrSaveFile = $objUpFile->save_file;
			
			foreach($arrSaveFile as $key => $val){
				lfMakeScaleImage($arrKey[$key], $arrKey[$key], true); 
			}
		}
		$objPage->arrForm = $arrForm;
		
		// ���ʥ��ơ��������Ѵ�
		$arrRet = sfSplitCBValue($objPage->arrForm['product_flag'], "product_flag");
		$objPage->arrForm = array_merge($objPage->arrForm, $arrRet);
		// DB���餪�����ᾦ�ʤ��ɤ߹���
		$objPage->arrRecommend = lfPreGetRecommendProducts($_POST['product_id']);
		
		// ������Ͽ����ʤ�Ƚ��
		$objPage->tpl_nonclass = lfCheckNonClass($_POST['product_id']);
		lfProductPage();		// ������Ͽ�ڡ���
	}
	break;
// ������Ͽ���Խ�
case 'edit':
	// ������Ͽ����ʤ�Ƚ��
	$tpl_nonclass = lfCheckNonClass($_POST['product_id']);
	
	if($_POST['product_id'] == "" and sfIsInt($_POST['copy_product_id'])){
		$tpl_nonclass = lfCheckNonClass($_POST['copy_product_id']);
	}
	$objPage->tpl_nonclass = $tpl_nonclass;
	
	// �����ͤ��Ѵ�
	$objPage->arrForm = lfConvertParam($objPage->arrForm);
	// ���顼�����å�
	$objPage->arrErr = lfErrorCheck($objPage->arrForm);
	// �ե�����¸�ߥ����å�
	$objPage->arrErr = array_merge((array)$objPage->arrErr, (array)$objUpFile->checkEXISTS());
	// ���顼�ʤ��ξ��
	if(count($objPage->arrErr) == 0) {
		lfProductConfirmPage(); // ��ǧ�ڡ���
	} else {
		lfProductPage();		// ������Ͽ�ڡ���
	}
	break;
// ��ǧ�ڡ������鴰λ�ڡ�����
case 'complete':
	$objPage->tpl_mainpage = 'products/complete.tpl';
	
	$objPage->tpl_product_id = lfRegistProduct($_POST);		// �ǡ�����Ͽ
	
	$objQuery = new SC_Query();
	// ���������ȥХå��¹�
	sfCategory_Count($objQuery);
	// ����ե���������֥ǥ��쥯�ȥ�˰�ư����
	$objUpFile->moveTempFile();

	break;
// �����Υ��åץ���
case 'upload_image':
	// �ե�����¸�ߥ����å�
	$objPage->arrErr = array_merge((array)$objPage->arrErr, (array)$objUpFile->checkEXISTS($_POST['image_key']));
	// ������¸����
	$objPage->arrErr[$_POST['image_key']] = $objUpFile->makeTempFile($_POST['image_key']);

	// �桢����������
	lfSetScaleImage();

	lfProductPage(); // ������Ͽ�ڡ���
	break;
// �����κ��
case 'delete_image':
	$objUpFile->deleteFile($_POST['image_key']);
	lfProductPage(); // ������Ͽ�ڡ���
	break;
// ��ǧ�ڡ�����������
case 'confirm_return':
	// ������Ͽ����ʤ�Ƚ��
	$objPage->tpl_nonclass = lfCheckNonClass($_POST['product_id']);
	lfProductPage();		// ������Ͽ�ڡ���
	break;
// �������ᾦ������
case 'recommend_select' :
	lfProductPage();		// ������Ͽ�ڡ���
	break;
default:
	// ������������Υǥե������
	$objPage->arrForm['status'] = DEFAULT_PRODUCT_DISP;
	lfProductPage();		// ������Ͽ�ڡ���
	break;
}

if($_POST['mode'] != 'pre_edit') {
	// �������ᾦ�ʤ��ɤ߹���
	$objPage->arrRecommend = lfGetRecommendProducts();
}

// ���ܾ�����Ϥ�
$objPage->arrInfo = $objSiteInfo->data;

// ���־�������Ϥ����뤫�ɤ��������å�����
$sub_find = false;
for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
	if(	$objPage->arrForm['sub_title'.$cnt] != "" || 
		$objPage->arrForm['sub_comment'.$cnt] != "" || 
		$objPage->arrForm['sub_image'.$cnt] != "" || 
		$objPage->arrForm['sub_large_image'.$cnt] != ""	|| 
		is_array($objPage->arrFile['sub_image'.$cnt]) || 
		is_array($objPage->arrFile['sub_large_image'.$cnt])) {
		$sub_find = true;
		break;
	}
}
// ���־���ɽ������ɽ���Υ����å��˻��Ѥ��롣
$objPage->sub_find = $sub_find;

$objView->assignobj($objPage);
$objView->display(MAIN_FRAME);
//-----------------------------------------------------------------------------------------------------------------------------

/* �������ᾦ�ʤ��ɤ߹��� */
function lfGetRecommendProducts() {
	global $objPage;
	$objQuery = new SC_Query();
	
	for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
		$keyname = "recommend_id" . $i;
		$delkey = "recommend_delete" . $i;
		$commentkey = "recommend_comment" . $i;

		if($_POST[$keyname] != "" && $_POST[$delkey] != 1) {
			$arrRet = $objQuery->select("main_list_image, product_code_min, name", "vw_products_allclass AS allcls", "product_id = ?", array($_POST[$keyname])); 
			$arrRecommend[$i] = $arrRet[0];
			$arrRecommend[$i]['product_id'] = $_POST[$keyname];
			$arrRecommend[$i]['comment'] = $objPage->arrForm[$commentkey];
		}
	}
	return $arrRecommend;
}

/* �������ᾦ�ʤ���Ͽ */
function lfInsertRecommendProducts($objQuery, $arrList, $product_id) {
	// ��ö�������ᾦ�ʤ򤹤٤ƺ������
	$objQuery->delete("dtb_recommend_products", "product_id = ?", array($product_id));
	$sqlval['product_id'] = $product_id;
	$rank = RECOMMEND_PRODUCT_MAX;
	for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
		$keyname = "recommend_id" . $i;
		$commentkey = "recommend_comment" . $i;
		$deletekey = "recommend_delete" . $i;
		if($arrList[$keyname] != "" && $arrList[$deletekey] != '1') {
			$sqlval['recommend_product_id'] = $arrList[$keyname];
			$sqlval['comment'] = $arrList[$commentkey];
			$sqlval['rank'] = $rank;
			$sqlval['creator_id'] = $_SESSION['member_id'];
			$sqlval['create_date'] = "now()";
			$sqlval['update_date'] = "now()";
			$objQuery->insert("dtb_recommend_products", $sqlval);
			$rank--;
		}
	}
}

/* ��Ͽ�Ѥߤ������ᾦ�ʤ��ɤ߹��� */
function lfPreGetRecommendProducts($product_id) {
	$objQuery = new SC_Query();
	$objQuery->setorder("rank DESC");
	$arrRet = $objQuery->select("recommend_product_id, comment", "dtb_recommend_products", "product_id = ?", array($product_id));
	$max = count($arrRet);
	$no = 1;
	
	for($i = 0; $i < $max; $i++) {
		$arrProductInfo = $objQuery->select("main_list_image, product_code_min, name", "vw_products_allclass AS allcls", "product_id = ?", array($arrRet[$i]['recommend_product_id'])); 
		$arrRecommend[$no] = $arrProductInfo[0];
		$arrRecommend[$no]['product_id'] = $arrRet[$i]['recommend_product_id'];
		$arrRecommend[$no]['comment'] = $arrRet[$i]['comment'];
		$no++;
	}
	return $arrRecommend;
}

/* ���ʾ�����ɤ߹��� */
function lfGetProduct($product_id) {
	$objQuery = new SC_Query();
	$col = "*";
	$table = "vw_products_nonclass AS noncls ";
	$where = "product_id = ?";
	
	// view��ʹ���(mysql�б�)
	sfViewWhere("&&noncls_where&&", $where, array($product_id));
	
	$arrRet = $objQuery->select($col, $table, $where, array($product_id));
		
	return $arrRet[0];
}

/* ������Ͽ�ڡ���ɽ���� */
function lfProductPage() {
	global $objPage;
	global $objUpFile;
	
	// ���ƥ�����ɹ�
	list($objPage->arrCatVal, $objPage->arrCatOut) = sfGetLevelCatList(false);

	if($objPage->arrForm['status'] == "") {
		$objPage->arrForm['status'] = 1;
	}
	
	if(!is_array($objPage->arrForm['product_flag'])) {
		// ���ʥ��ơ�������ʬ���ɹ�
		$objPage->arrForm['product_flag'] = sfSplitCheckBoxes($objPage->arrForm['product_flag']);
	}
	
	// HIDDEN�Ѥ�������Ϥ���
	$objPage->arrHidden = array_merge((array)$objPage->arrHidden, (array)$objUpFile->getHiddenFileList());
	// Form��������Ϥ���
	$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
	
	
	// ���󥫡�������
	if($_POST['image_key'] != ""){
		$anchor_hash = "location.hash='#" . $_POST['image_key'] . "'";
	}elseif($_POST['anchor_key'] != ""){
		$anchor_hash = "location.hash='#" . $_POST['anchor_key'] . "'";
	}
		
	$objPage->tpl_onload = "fnCheckSaleLimit('" . DISABLED_RGB . "'); fnCheckStockLimit('" . DISABLED_RGB . "'); " . $anchor_hash;
}

/* �ե��������ν���� */
function lfInitFile() {
	global $objUpFile;
	$objUpFile->addFile("����-�ᥤ�����", 'main_list_image', array('jpg', 'gif', 'png'),IMAGE_SIZE, true, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
	$objUpFile->addFile("�ܺ�-�ᥤ�����", 'main_image', array('jpg', 'gif', 'png'), IMAGE_SIZE, true, NORMAL_IMAGE_WIDTH, NORMAL_IMAGE_HEIGHT);
	$objUpFile->addFile("�ܺ�-�ᥤ��������", 'main_large_image', array('jpg', 'gif', 'png'), IMAGE_SIZE, false, LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT);
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$objUpFile->addFile("�ܺ�-���ֲ���$cnt", "sub_image$cnt", array('jpg', 'gif', 'png'), IMAGE_SIZE, false, NORMAL_SUBIMAGE_WIDTH, NORMAL_SUBIMAGE_HEIGHT);	
		$objUpFile->addFile("�ܺ�-���ֳ������$cnt", "sub_large_image$cnt", array('jpg', 'gif', 'png'), IMAGE_SIZE, false, LARGE_SUBIMAGE_WIDTH, LARGE_SUBIMAGE_HEIGHT);
	}
	$objUpFile->addFile("������Ӳ���", 'file1', array('jpg', 'gif', 'png'), IMAGE_SIZE, false, OTHER_IMAGE1_WIDTH, OTHER_IMAGE1_HEIGHT);
	$objUpFile->addFile("���ʾܺ٥ե�����", 'file2', array('pdf'), PDF_SIZE, false, 0, 0, false);
}

/* ���ʤ���Ͽ */
function lfRegistProduct($arrList) {
	global $objUpFile;
	global $arrSTATUS;
	$objQuery = new SC_Query();
	$objQuery->begin();
	
	// INSERT�����ͤ�������롣
	$sqlval['name'] = $arrList['name'];
	$sqlval['category_id'] = $arrList['category_id'];
	$sqlval['status'] = $arrList['status'];
	$sqlval['product_flag'] = $arrList['product_flag'];
	$sqlval['main_list_comment'] = $arrList['main_list_comment'];
	$sqlval['main_comment'] = $arrList['main_comment'];
	$sqlval['point_rate'] = $arrList['point_rate'];	
	$sqlval['deliv_fee'] = $arrList['deliv_fee'];
	$sqlval['comment1'] = $arrList['comment1'];
	$sqlval['comment2'] = $arrList['comment2'];
	$sqlval['comment3'] = $arrList['comment3'];
	$sqlval['comment4'] = $arrList['comment4'];
	$sqlval['comment5'] = $arrList['comment5'];
	$sqlval['comment6'] = $arrList['comment6'];
	$sqlval['main_list_comment'] = $arrList['main_list_comment'];
	$sqlval['sale_limit'] = $arrList['sale_limit'];
	$sqlval['sale_unlimited'] = $arrList['sale_unlimited'];
	$sqlval['deliv_date_id'] = $arrList['deliv_date_id'];
	$sqlval['update_date'] = "Now()";
	$sqlval['creator_id'] = $_SESSION['member_id'];
	$arrRet = $objUpFile->getDBFileList();
	$sqlval = array_merge($sqlval, $arrRet);
		
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$sqlval['sub_title'.$cnt] = $arrList['sub_title'.$cnt];
		$sqlval['sub_comment'.$cnt] = $arrList['sub_comment'.$cnt];
	}

	if($arrList['product_id'] == "") {
		if (DB_TYPE == "pgsql") {
			$product_id = $objQuery->nextval("dtb_products", "product_id");
			$sqlval['product_id'] = $product_id;
		}
		// ���ƥ�����Ǻ���Υ�󥯤������Ƥ�
		$sqlval['rank'] = $objQuery->max("dtb_products", "rank", "category_id = ?", array($arrList['category_id'])) + 1;
		// INSERT�μ¹�
		$sqlval['create_date'] = "Now()";
		$objQuery->insert("dtb_products", $sqlval);

		if (DB_TYPE == "mysql") {
			$product_id = $objQuery->nextval("dtb_products", "product_id");
			$sqlval['product_id'] = $product_id;
		}
		
		// ���ԡ����ʤξ��ˤϵ��ʤ⥳�ԡ�����
		if($_POST["copy_product_id"] != "" and sfIsInt($_POST["copy_product_id"])){
			// dtb_products_class �Υ��������
			$arrColList = sfGetColumnList("dtb_products_class", $objQuery);
			$arrColList_tmp = array_flip($arrColList);

			// ���ԡ����ʤ���
			unset($arrColList[$arrColList_tmp["product_class_id"]]);	 //����ID
			unset($arrColList[$arrColList_tmp["product_id"]]);			 //����ID

			$col = sfGetCommaList($arrColList);

			$objQuery->query("INSERT INTO dtb_products_class (product_id, ". $col .") SELECT ?, " . $col. " FROM dtb_products_class WHERE product_id = ? ORDER BY product_class_id", array($product_id, $_POST["copy_product_id"]));
			
		}

	} else {
		$product_id = $arrList['product_id'];
		// ����׵�Τ��ä���¸�ե�����κ��
		$arrRet = lfGetProduct($arrList['product_id']);
		$objUpFile->deleteDBFile($arrRet);
		
		// ���ƥ������󥯤�Ĵ������
		$old_catid = $objQuery->get("dtb_products", "category_id", "product_id = ?", array($arrList['product_id']));
		sfMoveCatRank($objQuery, "dtb_products", "product_id", "category_id", $old_catid, $arrList['category_id'], $arrList['product_id']);
		
		// UPDATE�μ¹�
		$where = "product_id = ?";
		$objQuery->update("dtb_products", $sqlval, $where, array($arrList['product_id']));
	}
	
	// ������Ͽ
	sfInsertProductClass($objQuery, $arrList, $product_id);
	
	// �������ᾦ����Ͽ
	lfInsertRecommendProducts($objQuery, $arrList, $product_id);
	
	$objQuery->commit();
	return $product_id;
}


/* ����ʸ������Ѵ� */
function lfConvertParam($array) {
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 */
	// ��ʪ���ܾ���
	
	// ���ݥåȾ���
	$arrConvList['name'] = "KVa";
	$arrConvList['main_list_comment'] = "KVa";
	$arrConvList['main_comment'] = "KVa";
	$arrConvList['price01'] = "n";
	$arrConvList['price02'] = "n";
	$arrConvList['stock'] = "n";
	$arrConvList['sale_limit'] = "n";
	$arrConvList['point_rate'] = "n";
	$arrConvList['product_code'] = "KVna";
	$arrConvList['comment1'] = "a";
	// �����λ���ʤ�
	$arrConvList['deliv_fee'] = "n";
	
	// �ܺ�-����
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$arrConvList["sub_title$cnt"] = "KVa";
	}
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$arrConvList["sub_comment$cnt"] = "KVa";
	}
	
	// �������ᾦ��
	for ($cnt = 1; $cnt <= RECOMMEND_PRODUCT_MAX; $cnt++) {
		$arrConvList["recommend_comment$cnt"] = "KVa";
	}

	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	
	global $arrSTATUS;
	$array['product_flag'] = sfMergeCheckBoxes($array['product_flag'], count($arrSTATUS));
	
	return $array;
}

// ���ϥ��顼�����å�
function lfErrorCheck($array) {
	global $objPage;
	global $arrAllowedTag;
	
	$objErr = new SC_CheckError($array);
	$objErr->doFunc(array("����̾", "name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("���ʥ��ƥ���", "category_id", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("����-�ᥤ�󥳥���", "main_list_comment", MTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�ܺ�-�ᥤ�󥳥���", "main_comment", LLTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�ܺ�-�ᥤ�󥳥���", "main_comment", $arrAllowedTag), array("HTML_TAG_CHECK"));
	$objErr->doFunc(array("�ݥ������ͿΨ", "point_rate", PERCENTAGE_LEN), array("EXIST_CHECK", "NUM_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("��������", "deliv_fee", PRICE_LEN), array("NUM_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�������", "comment3", LLTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("�᡼����URL", "comment1", URL_LEN), array("SPTAB_CHECK", "URL_CHECK", "MAX_LENGTH_CHECK"));
	$objErr->doFunc(array("ȯ�����ܰ�", "deliv_date_id", INT_LEN), array("NUM_CHECK"));
	
	if($objPage->tpl_nonclass) {
		$objErr->doFunc(array("���ʥ�����", "product_code", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK","MAX_LENGTH_CHECK","MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("�̾����", "price01", PRICE_LEN), array("ZERO_CHECK", "SPTAB_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("���ʲ���", "price02", PRICE_LEN), array("EXIST_CHECK", "NUM_CHECK", "ZERO_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
			
		if($array['stock_unlimited'] != "1") {
			$objErr->doFunc(array("�߸˿�", "stock", AMOUNT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
		}
	}
	
	if($array['sale_unlimited'] != "1") {	
		$objErr->doFunc(array("��������", "sale_limit", AMOUNT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
	}
	
	if(isset($objErr->arrErr['category_id'])) {
		// ��ư������ɤ�����˥��ߡ�ʸ��������Ƥ���
		$objPage->arrForm['category_id'] = "#";
	}
	
	for ($cnt = 1; $cnt <= PRODUCTSUB_MAX; $cnt++) {
		$objErr->doFunc(array("�ܺ�-���֥����ȥ�$cnt", "sub_title$cnt", STEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("�ܺ�-���֥�����$cnt", "sub_comment$cnt", LLTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
		$objErr->doFunc(array("�ܺ�-���֥�����$cnt", "sub_comment$cnt", $arrAllowedTag),  array("HTML_TAG_CHECK"));	
	}
	
	for ($cnt = 1; $cnt <= RECOMMEND_PRODUCT_MAX; $cnt++) {
		if($_POST["recommend_id$cnt"] != "" && $_POST["recommend_delete$cnt"] != 1) {
			$objErr->doFunc(array("�������ᾦ�ʥ�����$cnt", "recommend_comment$cnt", LTEXT_LEN), array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
		}
	}
	
	return $objErr->arrErr;
}

/* ��ǧ�ڡ���ɽ���� */
function lfProductConfirmPage() {
	global $objPage;
	global $objUpFile;
	$objPage->tpl_mainpage = 'products/confirm.tpl';
	$objPage->arrForm['mode'] = 'complete';
	// ���ƥ�����ɹ�
	$objPage->arrCatList = sfGetCategoryList();
	// Form��������Ϥ���
	$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
}

/* ���ʤ���Ƚ����(���ʤ���Ͽ����Ƥ��ʤ����:TRUE) */
function lfCheckNonClass($product_id) {
	if(sfIsInt($product_id)) {
		$objQuery  = new SC_Query();
		$where = "product_id = ? AND classcategory_id1 <> 0 AND classcategory_id1 <> 0";
		$count = $objQuery->count("dtb_products_class", $where, array($product_id));
		if($count > 0) {
			return false;
		}
	}
	return true;
}

// �̾����������򥻥åȤ���
function lfSetScaleImage(){
	
	$subno = str_replace("sub_large_image", "", $_POST['image_key']);
	switch ($_POST['image_key']){
		case "main_large_image":
			// �ܺ٥ᥤ�����
			lfMakeScaleImage($_POST['image_key'], "main_image");
		case "main_image":
			// �����ᥤ�����
			lfMakeScaleImage($_POST['image_key'], "main_list_image");
            break;
		case "sub_large_image" . $subno:
			// ���֥ᥤ�����
			lfMakeScaleImage($_POST['image_key'], "sub_image" . $subno);
			break;
		default:
			break;
	}
}

// �̾���������
function lfMakeScaleImage($from_key, $to_key, $forced = false){
	global $objUpFile;
	$arrImageKey = array_flip($objUpFile->keyname);
	
	if($objUpFile->temp_file[$arrImageKey[$from_key]]){
		$from_path = $objUpFile->temp_dir . $objUpFile->temp_file[$arrImageKey[$from_key]];
	}elseif($objUpFile->save_file[$arrImageKey[$from_key]]){
		$from_path = $objUpFile->save_dir . $objUpFile->save_file[$arrImageKey[$from_key]];
	}else{
		return "";
	}
	
	if(file_exists($from_path)){
		// �����������������
		list($from_w, $from_h) = getimagesize($from_path);
		
		// ������β��������������
		$to_w = $objUpFile->width[$arrImageKey[$to_key]];
		$to_h = $objUpFile->height[$arrImageKey[$to_key]];
		
		
		if($forced) $objUpFile->save_file[$arrImageKey[$to_key]] = "";
		
		if(($objUpFile->temp_file[$arrImageKey[$to_key]] == "" and $objUpFile->save_file[$arrImageKey[$to_key]] == "")){
			$path = $objUpFile->makeThumb($from_path, $to_w, $to_h);
			$objUpFile->temp_file[$arrImageKey[$to_key]] = basename($path);
		}
	}else{
		return "";
	}
}

?>