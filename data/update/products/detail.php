<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");
require_once(DATA_PATH . "include/page_layout.inc");

class UC_Page {
	function UC_Page() {
		/** ɬ�����ꤹ�� **/
		global $arrSTATUS;
		$this->arrSTATUS = $arrSTATUS;
		global $arrSTATUS_IMAGE;
		$this->arrSTATUS_IMAGE = $arrSTATUS_IMAGE;
		global $arrDELIVERYDATE;
		$this->arrDELIVERYDATE = $arrDELIVERYDATE;
		global $arrRECOMMEND;
		$this->arrRECOMMEND = $arrRECOMMEND;
		session_cache_limiter('private-no-expire');
	}
}

ufDetailPHP();
exit;

function ufDetailPHP() {
	global $objPage;
	global $objView;
	global $objCustomer;
	global $objQuery;
	global $objUpFile;
	
	$objPage = new UC_Page();
	$objView = new SC_SiteView();
	$objCustomer = new SC_Customer();
	$objQuery = new SC_Query();
	
	// �쥤�����ȥǥ���������
	$objPage = sfGetPageLayout($objPage, false, "products/detail.php");
	
	// �ѥ�᡼���������饹
	$objFormParam = new SC_FormParam();
	// �ѥ�᡼������ν����
	lfInitParam();
	// POST�ͤμ���
	$objFormParam->setParam($_POST);
	
	// �ե�����������饹
	$objUpFile = new SC_UploadFile(IMAGE_TEMP_DIR, IMAGE_SAVE_DIR);
	// �ե��������ν����
	lfInitFile();
	
	// �����ڡ�������γ�ǧ�ξ��ϡ�������ξ��ʤ�ɽ�����롣
	if($_GET['admin'] == 'on') {
		$where = "del_flg = 0";
	} else {
		$where = "del_flg = 0 AND status = 1";
	}
	
	if($_POST['mode'] != "") {
		$tmp_id = $_POST['product_id'];
	} else {
		$tmp_id = $_GET['product_id'];
	}
	
	// �ͤ������������å�
	if(!sfIsInt($_GET['product_id']) || !sfIsRecord("dtb_products", "product_id", $tmp_id, $where)) {
		sfDispSiteError(PRODUCT_NOT_FOUND);
	}
	// ������Ƚ��
	if($objCustomer->isLoginSuccess()) {
		//����������ܥ���ɽ��
		$objPage->tpl_login = true;
		
		$table = "dtb_customer_reading";
		$where = "customer_id = ? ";
		$arrval[] = $objCustomer->getValue('customer_id');
		//�ܵҤα������ʿ�
		$rpcnt = $objQuery->count($table, $where, $arrval);
	
		//��������������ʲ�
		if ($rpcnt < CUSTOMER_READING_MAX){
			//��������˿����ɲ�
			lfRegistReadingData($tmp_id, $objCustomer->getValue('customer_id'));
		} else {
			//�����������ǰ��ָŤ���Τ������ƿ����ɲ�
			$where = "customer_id = ? AND update_date = (SELECT MIN(update_date) FROM ".$table." WHERE customer_id = ? ) ";
			$arrval = array($objCustomer->getValue("customer_id"), $objCustomer->getValue("customer_id"));
			//���
			$objQuery->delete($table, $where, $arrval);
			//�ɲ�
			lfRegistReadingData($tmp_id, $objCustomer->getValue('customer_id'));
		}
	}
	
	
	// �������򥻥쥯�ȥܥå����κ���
	$objPage = lfMakeSelect($objPage, $tmp_id);
	
	// ����ID��FORM����ݻ����롣
	$objPage->tpl_product_id = $tmp_id;
	
	switch($_POST['mode']) {
	case 'cart':
		// �����ͤ��Ѵ�
		$objFormParam->convParam();
		$objPage->arrErr = lfCheckError();
		if(count($objPage->arrErr) == 0) {
			$objCartSess = new SC_CartSession();
			$classcategory_id1 = $_POST['classcategory_id1'];
			$classcategory_id2 = $_POST['classcategory_id2'];
					
			// ����1�����ꤵ��Ƥ��ʤ����
			if(!$objPage->tpl_classcat_find1) {
				$classcategory_id1 = '0';
			}
			
			// ����2�����ꤵ��Ƥ��ʤ����
			if(!$objPage->tpl_classcat_find2) {
				$classcategory_id2 = '0';
			}
	
			$objCartSess->addProduct(array($_POST['product_id'], $classcategory_id1, $classcategory_id2), $objFormParam->getValue('quantity'));
			header("Location: " . URL_CART_TOP);
	
			exit;
		}
		break;
			
	default:
		break;
	}
	
	$objQuery = new SC_Query();
	// DB���龦�ʾ����������롣
	$arrRet = $objQuery->select("*", "vw_products_allclass_detail AS alldtl", "product_id = ?", array($tmp_id));
	$objPage->arrProduct = $arrRet[0];
	
	// �������¿������
	if($objPage->arrProduct['sale_unlimited'] == 1 || $objPage->arrProduct['sale_limit'] > SALE_LIMIT_MAX) {
	  $objPage->tpl_sale_limit = SALE_LIMIT_MAX;
	} else {
	  $objPage->tpl_sale_limit = $objPage->arrProduct['sale_limit'];
	}
	
	// ���֥����ȥ�����
	$arrFirstCat = GetFirstCat($arrRet[0]['category_id']);
	$tpl_subtitle = $arrFirstCat['name'];
	$objPage->tpl_subtitle = $tpl_subtitle;
	
	// DB����Υǡ���������Ѥ�
	$objUpFile->setDBFileList($objPage->arrProduct);
	// �ե�����ɽ����������Ϥ�
	$objPage->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
	// ��ʧ��ˡ�μ���
	$objPage->arrPayment = lfGetPayment();
	// ���Ͼ�����Ϥ�
	$objPage->arrForm = $objFormParam->getFormParamList();
	//��ӥ塼����μ���
	$objPage->arrReview = lfGetReviewData($tmp_id);
	// �����ȥ�˾���̾�������
	$objPage->tpl_title = "���ʾܺ� ". $objPage->arrProduct["name"];
	//�������ᾦ�ʾ���ɽ��
	$objPage->arrRecommend = lfPreGetRecommendProducts($tmp_id);
	//���ξ��ʤ���ä��ͤϤ���ʾ��ʤ���äƤ��ޤ�
	$objPage->arrRelateProducts = lfGetRelateProducts($tmp_id);
	
	$objView->assignobj($objPage);
	$objView->display(SITE_FRAME);
}
//-----------------------------------------------------------------------------------------------------------------------------------
?>