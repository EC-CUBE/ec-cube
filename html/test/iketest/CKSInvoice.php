<?php
##################################################
# Veritrans CVS Merchant Development Kit.
# CKSInvoice.php��Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: �٥�ȥ�󥹥���ӥ˥����ȥ�������
#       ��³���뤿��Υ���ץ�
#       << �������� K ���󥯥��ѥ���ץ� >>
##################################################

#-----------------------------------------------
# CVS�ѤΥѥå������Υѥ�����
# ��ա������ͤδĶ��˹�碌�����ꤷ�Ƥ���������
#-----------------------------------------------
# �ޡ������Ⱦ�������ե�����򥤥󥯥롼��

require_once("../../require.php");
require("merchant.ini");

# ��ѽ����ѥå������򥤥󥯥롼��

require_once($PHPLIB_PATH . "Transaction.php");
require_once($PHPLIB_PATH . "Config.php");
require_once($PHPLIB_PATH . "Log.php");
require_once("Cart.php");


class LC_Page{
	function LC_Page() {
		$this->tpl_mainpage = 'test/iketest/CKSInvoice.tpl';
		$this->tpl_title = '���㤤�夲���꤬�Ȥ��������ޤ�';
		global $PAY_URL_CKS;
		$this->PAY_URL_CKS = $PAY_URL_CKS;
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

# �ȥ�󥶥�����󥤥󥹥��󥹤����
$t = new Transaction;

# ����ե����� cvsgwlib.conf �ˤ�ꥤ�󥹥��󥹤�����
$t->setServer($CONFIG);
# �����ϥ��󥹥��󥹤����
$logger = $t->getLogger();

# �����ϥ���ץ�
$logger->logprint('DEBUG', '<<< ��ʧ��̲���(CKSInvoice.php)��������... >>>');

# �����ȥ��󥹥��󥹤����
$cart = new Cart;

# ���ߡ����ID�����
# ��ա�����åԥ󥰥����Ȥʤɤ����������褦�˥������ޥ������Ƥ���������
$order_id = "ck-" . $cart->getOrderId();

# ���ߡ���ۤ����
# ��ա�����åԥ󥰥����Ȥʤɤ����������褦�˥������ޥ������Ƥ���������
$amount = $cart->getPrice();

# ���ߡ���ʧ���¤����
# ��ա�����åԥ󥰥����Ȥʤɤ����������褦�˥������ޥ������Ƥ���������
$pay_limit = $cart->getPayLimit();

# �ꥯ�����ȥѥ�᡼������κ���
$params = array(
    REQ_ORDER_ID => $order_id,
    REQ_AMOUNT => $amount,
    REQ_PAY_LIMIT => $pay_limit
);

# URL ���󥳡��ɤ�Ԥ�
$objPage->params_str = $t->URLEncode($ENCODE, $params);

# �ڡ�����������ɻߤ��뤿��˥ϥå����׻�����
$objPage->hash = $t->genHash($MERCHANT_ID, $SIG_KEY, $objPage->params_str);

$objPage->order_id = $order_id;
$objPage->amount = $amount;
$objPage->pay_limit = $pay_limit;

# �����ϥ���ץ�
$logger->logprint(DBGLVL_DEBUG, ' REQ_ORDER = ' . $objPage->params_str);
$logger->logprint(DBGLVL_DEBUG, ' REQ_ORDER_SIG = ' . $objPage->hash);

$logger->logprint('DEBUG', '<<< ��ʧ����(CKSInvoice.php)������λ... >>>');

# ���̤����
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>
