<?php
##################################################
# Veritrans CVS Merchant Development Kit.
# FMInvoice.php��Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: �٥�ȥ�󥹥���ӥ˥����ȥ�������
#       ��³���뤿��Υ���ץ�
#       << �ե��ߥ꡼�ޡ����ѥ���ץ� >>
##################################################

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
include_once("Cart.php");

# �ȥ�󥶥�����󥤥󥹥��󥹤����
$t = new Transaction;

# ����ե����� cvsgwlib.conf �ˤ�ꥤ�󥹥��󥹤�����
$t->setServer($CONFIG);

# �����ȥ��󥹥��󥹤����
$cart = new Cart;

# �����ϥ��󥹥��󥹤����
$logger = $t->getLogger();

# �����ϥ���ץ�
$logger->logprint('DEBUG', '<<< ��ʧ����(FMInvoice.php)��������... >>>');

# ���ߡ����ID�����
# ��ա�����åԥ󥰥����Ȥʤɤ����������褦�˥������ޥ������Ƥ���������
$order_id = "fm-" . $cart->getOrderId();

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
$params_str = $t->URLEncode($ENCODE, $params);

# �ڡ�����������ɻߤ��뤿��˥ϥå����׻�����
$hash = $t->genHash($MERCHANT_ID, $SIG_KEY, $params_str);

$logger->logprint(DBGLVL_DEBUG, ' REQ_ORDER = ' . $params_str);
$logger->logprint(DBGLVL_DEBUG, ' REQ_ORDER_SIG = ' . $hash);

# ���̤����
?>
<script language="javascript">
<!--
function disableSubmit(form) {
  var elements = form.elements;
  for (var i = 0; i < elements.length; i++) {
    if (elements[i].type == 'submit') {
      elements[i].disabled = true;
    }
  }
}
//-->
</script>
<html>
<head>
  <title>���㤤�夲���꤬�Ȥ��������ޤ���</title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?=$ENCODE?>" />
</head>
<body bgcolor="#FFFFFF" text="#000000">
  <hr />
  <h1>���㤤�夲����</h1>
  <p>
    ���ID:&nbsp;<strong><?=$order_id?></strong>
  </p>
  <p>
    ����:&nbsp;<strong><?=$amount?></strong>&nbsp;��
  </p>
  <p>
    ����ʧ����:&nbsp;<strong><?=$pay_limit?></strong>
  </p>
  <h3>�����;���</h3>
  <form action="<?=$PAY_URL_FM?>" method="POST" onsubmit="disableSubmit(this)">
    <table>
      <tr>
        <td>����</td>
        <td><input type="text" name="REQ_NAME1" /></td>
      </tr>
      <tr>
        <td>̾��</td>
        <td><input type="text" name="REQ_NAME2" /></td>
      </tr>
      <tr>
        <td>��̾�ʥ��ʡˡ�</td>
        <td><input type="text" name="REQ_KANA" /></td>
      </tr>
      <tr>
        <td>�����ֹ桧</td>
        <td><input type="text" name="REQ_TEL_NO" /></td>
      </tr>
    </table>
    <input type="hidden" name="REQ_ORDER" value="<?=$params_str?>" />
    <input type="hidden" name="REQ_ORDER_SIG" value="<?=$hash?>" />
    <p><input type="submit" value=" ���� " /></p>
  </form>
  <hr />
  <h3>ɬ�������λ����Web�ڡ����Τɤ����˵��ܤ��Ƥ���������</h3>
  <table>
    <tr>
      <td> ����ӥˤ����ѻ�ʧ���Ѥ�Ԥʤä�����ӥ�Ź�ޤ��ֶ�������ʤ��ݤ���ս񤭤򵭽Ҥ��Ƥ���������</td>
    </tr>
  </table>
</body>
</html>
<?php
# �����ϥ���ץ�
$logger->logprint('DEBUG', '<<< ��ʧ����(FMInvoice.php)������λ. >>>');
?>
