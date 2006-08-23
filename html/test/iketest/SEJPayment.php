<?php
######################################################
# Veritrans CVS Merchant Development Kit.
# SEJPayment.php Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: �٥�ȥ�󥹥���ӥ˥����ȥ����������
#       �쥹�ݥ󥹤�������륵��ץ�
#       << ���֥󡦥���֥��ѥ���ץ� >>
######################################################

#-----------------------------------------------
# CVS�ѤΥѥå������Υѥ�����
# ��ա������ͤδĶ��˹�碌�����ꤷ�Ƥ���������
#-----------------------------------------------
# �ޡ������Ⱦ�������ե�����򥤥󥯥롼��
include("merchant.ini");

# ��ѽ����ѥå������򥤥󥯥롼��
include_once($PHPLIB_PATH . "Transaction.php");
include_once($PHPLIB_PATH . "Config.php");
include_once($PHPLIB_PATH . "Log.php");

# �ȥ�󥶥�����󥤥󥹥��󥹤����
$t = new Transaction;

# ����ե����� cvsgwlib.conf �ˤ�ꥤ�󥹥��󥹤�����
$t->setServer($CONFIG);

# �����ϥ��󥹥��󥹤����
$logger = $t->getLogger();

# �����ϥ���ץ�
$logger->logprint('DEBUG', '<<< ��ʧ��̲���(SEJPayment.php)��������... >>>');

# ��ʧ�ڡ�������Υѥ�᡼�������
$query = $t->getQuery($ENCODE);

# �ꥯ�����ȥѥ�᡼����������å�
$hash = $t->genHash($MERCHANT_ID, $SIG_KEY, $query['REQ_ORDER']);

#if ($hash != $query['REQ_ORDER_SIG']) {
if (strlen($hash) <= 0 || $hash != $query['REQ_ORDER_SIG']) {
    # ��̾���顼��ɽ�����ƽ�λ
?>
<html>
<head>
  <title>�Żҽ�̾���顼</title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?=$ENCODE?>" />
</head>
<body bgcolor="#FFFFFF" text="#000000">
  <p>�Żҽ�̾���顼��ȯ�����ޤ�����</p>
</body>
</html>
<?php
    exit;
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
    # CVS������(���֥󥤥�֥�)
    REQ_CVS_TYPE => "01",
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
    $aux_msg = $t->jCode($result[RES_AUX_MSG], $ENCODE);
}
else {
    $MErrMsg = $t->jCode($result[RES_MERRMSG], $ENCODE);
    $MErrLoc = $result[RES_MERRLOC];
}
$action_code = $result[RES_ACTION_CODE];
$order_id = $result[RES_ORDER_ID];
$order_ctl_id = $result[RES_ORDER_CTL_ID];
$txn_version = $result[RES_TXN_VERSION];
$merch_txn = $result[RES_MERCH_TXN];
$cust_txn = $result[RES_CUST_TXN];
$receipt_no = $result[RES_RECEIPT_NO];
$haraikomi_url = $result[RES_HARAIKOMI_URL];
$err_code = $result[RES_ERR_CODE];
$payment_type = $result[RES_PAYMENT_TYPE];
$ref_code = $result[RES_REF_CODE];

# ��ۤȻ�ʧ���¤������ȥ���������Υ쥹�ݥ���ʸ�˴ޤޤ�ʤ�����
# �ꥯ��������ʸ�����������
$amount = $request[REQ_AMOUNT];
$pay_limit = $request[REQ_PAY_LIMIT];

# ��ʧ��̤ˤ�����������ԥڡ�����ɽ��
#if (substr_count($MStatus, RES_MSTATUS_SC) != 0) {
if ($MStatus == 'success') {
?>
<html>
  <head>
    <title>����ʸ���ˤ��꤬�Ȥ��������ޤ�����</title>
    <meta http-equiv="Content-Type"
          content="text/html; charset=<?=$ENCODE?>" />
  </head>
  <body bgcolor="#FFFFFF" text="#000000">
    <h1>����ʸ���ˤ��꤬�Ȥ��������ޤ�����</h1>
    <hr />
    <p>�����ͤμ��ID�� <?=$order_id?> �Ǥ���</p>
    <p>����ɼ�ֹ�� <?=$receipt_no?> �Ǥ���</p>
    <p>ʧ������ɼ��
       <a href="<?=$haraikomi_url?>"><?=$haraikomi_url?></a> �Ǥ���</p>
    <p>��ʧ��ۤ� <?=$amount?> �ߤǤ���</p>
    <p>��ʧ���¤� <?=$pay_limit?> �Ǥ���</p>
    <p>����ɼ��������⤷���Ͽ���ɼ�ֹ���ʤɤ˹���������Υ��֥󥤥�֥�ˤƤ���ʧ����������</p>
    <p>����ɼ�ֹ���ʤɤǤ������ξ��ϡ������ͤ���Ź�����󼨤κݤˡ֥��󥿡��ͥå����λ�ʧ���פȤ�������������</p>
    <p><a href="http://pr.sej.co.jp/in/system.html" target="_blank">�ܤ����Ϥ��Υڡ����ˤƤ���ǧ����������</a><br></p>
    <!-- �ʲ��Υѥ�᡼�������Ӥˤ�äƻȤäƤ�������
    <hr />
    <p>txn-version  :  <?=$txn_version?></p>
    <p>merch-txn    :  <?=$merch_txn?></p>
    <p>order-ctl-id :  <?=$order_ctl_id?></p>
    <p>MStatus      :  <?=$MStatus?></p>
    <p>MErrMsg      :  <?=$MErrMsg?></p>
    <p>aux-msg      :  <?=$aux_msg?></p>
    <p>receipt-no   :  <?=$receipt_no?></p>
    <p>action-code  :  <?=$action_code?></p>
    <p>ref-code     :  <?=$ref_code?></p>
    <p>MErrLoc      :  <?=$MErrLoc?></p>
    <p>err-code     :  <?=$err_code?></p>
    <p>cust-txn     :  <?=$cust_txn?></p>
    <p>order-id     :  <?=$order_id?></p>
    <p>amount       :  <?=$amount?></p>
    <p>pay-limit    :  <?=$pay_limit?></p>
    -->
  <hr />
  </body>
</html>
<?php
} else { 
?>
<html>
  <head>
    <title>����ʸ�ϼ��դǤ��ޤ���Ǥ�����</title>
    <meta http-equiv="Content-Type"
          content="text/html; charset=<?=$ENCODE?>" />
  </head>
  <body bgcolor="#FFFFFF" text="#000000">
    <h1>�������������ޤ���</h1>
    <h2>�����ͤΤ���ʸ�ϼ��դǤ��ޤ���Ǥ�����</h2>
    <hr />
    <blockquote><?=$MErrMsg?></blockquote>
    <p><blockquote>�����ͤμ��ID�� <?=$order_id?> �Ǥ���</blockquote></p>
    <!-- �ʲ��Υѥ�᡼�������Ӥˤ�äƻȤäƤ�������
    <hr />
    <p>txn-version  :  <?=$txn_version?></p>
    <p>merch-txn    :  <?=$merch_txn?></p>
    <p>order-ctl-id :  <?=$order_ctl_id?></p>
    <p>MStatus      :  <?=$MStatus?></p>
    <p>MErrMsg      :  <?=$MErrMsg?></p>
    <p>aux-msg      :  <?=$aux_msg?></p>
    <p>receipt-no   :  <?=receipt_no?></p>
    <p>action-code  :  <?=$action_code?></p>
    <p>ref-code     :  <?=$ref_code?></p>
    <p>MErrLoc      :  <?=$MErrLoc?></p>
    <p>err-code     :  <?=$err_code?></p>
    <p>cust-txn     :  <?=$cust_txn?></p>
    <p>order-id     :  <?=$order_id?></p>
    <p>amount       :  <?=$amount?></p>
    <p>pay-limit    :  <?=$pay_limit?></p>
    -->
  <hr />
  </body>
</html>
<?php
}
# �����ϥ���ץ�
$logger->logprint('DEBUG', '<<< ��ʧ��̲���(MIPayment.php)������λ. >>>');
?>
