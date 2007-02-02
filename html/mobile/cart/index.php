<?php
require_once("../require.php");

class LC_Page {
	var $arrSession;
	var $arrProductsClass;
	var $tpl_total_pretax;
	var $tpl_total_tax;
	var $tpl_total_point;
	var $tpl_message;
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_css = '/css/layout/cartin/index.css';	// �ᥤ��CSS�ѥ�
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'cart/index.tpl';		// �ᥤ��ƥ�ץ졼��
		$this->tpl_title = "�����ޤ���򸫤�";
	}
}

// �㤤ʪ��³������
if($_REQUEST['continue']) {
	header("Location: " . gfAddSessionId(URL_SITE_TOP) );
	exit;
}

$objPage = new LC_Page();
$objView = new SC_SiteView(false);
$objCartSess = new SC_CartSession("", false);
$objSiteSess = new SC_SiteSession();
$objSiteInfo = $objView->objSiteInfo;
$objCustomer = new SC_Customer();
// ���ܾ���μ���
$arrInfo = $objSiteInfo->data;

// ���ʹ�����˥��������Ƥ��ѹ����줿��
if($objCartSess->getCancelPurchase()) {
	$objPage->tpl_message = "���ʹ�����ˎ��������Ƥ��ѹ�����ޤ����Τǎ�������Ǥ���������³������ľ���Ʋ�������";
}

switch($_POST['mode']) {
case 'up':
	$objCartSess->upQuantity($_POST['cart_no']);
	sfReload();
	break;
case 'down':
	$objCartSess->downQuantity($_POST['cart_no']);
	sfReload();
	break;
case 'delete':
	$objCartSess->delProduct($_POST['cart_no']);
	sfReload();
	break;
case 'confirm':
	// �����������μ���
	$arrRet = $objCartSess->getCartList();
	$max = count($arrRet);
	$cnt = 0;
	for ($i = 0; $i < $max; $i++) {
		// ���ʵ��ʾ���μ���
		$arrData = sfGetProductsClass($arrRet[$i]['id']);
		// DB��¸�ߤ��뾦��
		if($arrData != "") {
			$cnt++;
		}
	}
	// �����Ⱦ��ʤ�1��ʾ�¸�ߤ�����
	if($cnt > 0) {
		// �������Ͽ���줿���Ȥ�Ͽ���Ƥ���
		$objSiteSess->setRegistFlag();
		$pre_uniqid = $objSiteSess->getUniqId();
		// ��ʸ���ID��ȯ��
		$objSiteSess->setUniqId();
		$uniqid = $objSiteSess->getUniqId();
		// ���顼��ȥ饤�ʤɤǴ���uniqid��¸�ߤ�����ϡ����������Ѥ�
		if($pre_uniqid != "") {
			$sqlval['order_temp_id'] = $uniqid;
			$where = "order_temp_id = ?";
			$objQuery = new SC_Query();
			$objQuery->update("dtb_order_temp", $sqlval, $where, array($pre_uniqid));
		}
		// �����Ȥ�����⡼�ɤ�����
		$objCartSess->saveCurrentCart($uniqid);
		// �����ڡ�����
		header("Location: " . gfAddSessionId(URL_SHOP_TOP));
		exit;
	}
	break;
default:
	break;
}

// �����Ƚ��׽���
$objPage = sfTotalCart($objPage, $objCartSess, $arrInfo);
$objPage->arrData = sfTotalConfirm($arrData, $objPage, $objCartSess, $arrInfo, $objCustomer);

$objPage->arrInfo = $arrInfo;

// ������Ƚ��
if($objCustomer->isLoginSuccess()) {
	$objPage->tpl_login = true;
	$objPage->tpl_user_point = $objCustomer->getValue('point');
	$objPage->tpl_name = $objCustomer->getValue('name01');
}

// ����̵���ޤǤζ�ۤ�׻�
$tpl_deliv_free = $objPage->arrInfo['free_rule'] - $objPage->tpl_total_pretax;
$objPage->tpl_deliv_free = $tpl_deliv_free;

// ���Ǥ�URL�����
$objPage->tpl_prev_url = $objCartSess->getPrevURL();

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);
//--------------------------------------------------------------------------------------------------------------------------
?>