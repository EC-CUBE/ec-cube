<?php
######################################################
# Veritrans CVS Merchant Development Kit.
# CKSPayment.php Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: �٥�ȥ�󥹥���ӥ˥����ȥ����������
#       �쥹�ݥ󥹤�������륵��ץ�
#       << �������� K ���󥯥��ѥ���ץ� >>
######################################################

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
class LC_Page{
	function LC_Page() {
		$this->tpl_title = "����ʸ���ˤ��꤬�Ȥ��������ޤ�����";
		$this->tpl_mainpage = 'test/iketest/CKSPayment.tpl';
	}
}

$objPage = new LC_Page;
$objView = new SC_SiteView;

# �ȥ�󥶥�����󥤥󥹥��󥹤����
$t = new Transaction;

# ����ե����� cvsgwlib.conf �ˤ�ꥤ�󥹥��󥹤�����
$t->setServer($CONFIG);

# �����ϥ��󥹥��󥹤����
$logger = $t->getLogger();

# �����ϥ���ץ�
$logger->logprint('DEBUG', '<<< ��ʧ��̲���(CKSPayment.php)��������... >>>');

# ��ʧ�ڡ�������Υѥ�᡼�������
$query = $t->getQuery($ENCODE);

# �ꥯ�����ȥѥ�᡼����������å�
$hash = $t->genHash($MERCHANT_ID, $SIG_KEY, $query['REQ_ORDER']);

if(strlen($hash) <= 0 || $hash != $query['REQ_ORDER_SIG']) {
	//��̾���顼��ɽ�����ƽ�λ
	sfDispSiteError(E_SIGN_ERROR);
}

# �ꥯ�����ȥѥ�᡼����ǥ�����
$orders = $t->URLDecode($ENCODE, $query['REQ_ORDER']);

#-----------------------------------------------
# �ꥯ��������ʸ($request)�˥ѥ�᡼���򥻥åȢ�
#-----------------------------------------------
$request = array(
    # ��������
    #REQ_ACCEPT_LANGUAGE => ACCEPT_LANGUAGE_JA,
    # ������ޥ�ɡ� entry(��Ͽ)
    #REQ_COMMAND => CMD_ENTRY,
    # ��� ID
    REQ_ORDER_ID => $orders[REQ_ORDER_ID],
    # CVS������(CircleKSunks)
    REQ_CVS_TYPE => "04",
    # ���
    REQ_AMOUNT => $orders[REQ_AMOUNT],
    # ��ʧ����
    REQ_PAY_LIMIT => $orders[REQ_PAY_LIMIT],
    # ��̾����ա��٥�ȥ�󥹥���ӥ˥����ȥ������� UTF-8 ��ʸ���Τߤ�
    # �����դ��뤿�ᡢ�����ȥ�������³������ UTF-8 �����ɤ��Ѵ���
    REQ_NAME1 => $t->jCode($query[REQ_NAME1], ENCODE_UTF8),
    REQ_NAME2 => $t->jCode($query[REQ_NAME2], ENCODE_UTF8),
    # �����ֹ�
    REQ_TEL_NO => $query[REQ_TEL_NO]
);


#------------------------------------------------
# �٥�ȥ�󥹥���ӥ˥����ȥ������˼�����ꤲ��
# �����̤� result �˥������ͤΥڥ��ǳ�Ǽ�����
#------------------------------------------------
$result = $t->doTransaction(CMD_ENTRY, $request);

# �쥹�ݥ��ͤ��������
$MStatus = $result[RES_MSTATUS];
#if (substr_count($MStatus, RES_MSTATUS_SC) != 0) {
if ($MStatus == 'success') {
    $objPage->aux_msg = $t->jCode($result[RES_AUX_MSG], $ENCODE);
}
else {
    $objPage->MErrMsg = $t->jCode($result[RES_MERRMSG], $ENCODE);
    $objPage->MErrLoc = $result[RES_MERRLOC];
}
$objPage->MStatus = $MStatus;
$objPage->action_code = $result[RES_ACTION_CODE];
$objPage->order_id = $result[RES_ORDER_ID];
$objPage->order_ctl_id = $result[RES_ORDER_CTL_ID];
$objPage->txn_version = $result[RES_TXN_VERSION];
$objPage->merch_txn = $result[RES_MERCH_TXN];
$objPage->cust_txn = $result[RES_CUST_TXN];
$objPage->receipt_no = $result[RES_RECEIPT_NO];
$objPage->haraikomi_url = $result[RES_HARAIKOMI_URL];
$objPage->err_code = $result[RES_ERR_CODE];
$objPage->payment_type = $result[RES_PAYMENT_TYPE];
$objPage->ref_code = $result[RES_REF_CODE];

# ��ۤȻ�ʧ���¤������ȥ���������Υ쥹�ݥ���ʸ�˴ޤޤ�ʤ�����
# �ꥯ��������ʸ�����������
$objPage->amount = $request[REQ_AMOUNT];
$objPage->pay_limit = $request[REQ_PAY_LIMIT];

# �������� K ���󥯥������� URL ����
$objPage->mobile_url = preg_replace("/https:\/\/.+?\/JLPcon/",
                           "https://w2.kessai.info/JLM/JLMcon",
                           $objPage->haraikomi_url);
#$result{'mobile-url'} = 


# ��ʧ��̤ˤ�����������ԥڡ�����ɽ��
#if (substr_count($MStatus, RES_MSTATUS_SC) != 0) {
if ($MStatus != 'success') {
	$objPage->tpl_title = "����ʸ�ϼ��դǤ��ޤ���Ǥ�����";
	$objPage->tpl_mainpage = "test/iketest/error.tpl";
}


# �����ϥ���ץ�
$logger->logprint('DEBUG', '<<< ��ʧ��̲���(CKSPayment.php)������λ. >>>');

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>
