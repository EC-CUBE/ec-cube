<?php
##################################################
# Veritrans CVS Merchant Development Kit.
# SEJInvoice.php��Version 1.0.0
# Copyright(c) 2006 SBI VeriTrans Co., Ltd.
# Note: �٥�ȥ�󥹥���ӥ˥����ȥ�������
#       ��³���뤿��Υ���ץ�
#       << ���֥󥤥�֥��ѥ���ץ� >>
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
$logger->logprint('DEBUG', '<<< ��ʧ����(SEJInvoice.php)��������... >>>');

# ���ߡ����ID�����
# ��ա�����åԥ󥰥����Ȥʤɤ����������褦�˥������ޥ������Ƥ���������
$order_id = "sj-" . $cart->getOrderId();

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
  <form action="<?=$PAY_URL_SEJ?>" method="POST"
                                   onsubmit="disableSubmit(this)">
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
      <td>������ҡ����֥󡦥���֥󡦥���ѥ��ʧ������ɼ�˵��ܤ���Ƥ��뾦�����ι����۾����</td>
    </tr>
    <tr>
      <td>�ܵҤΥץ饤�Х�������θ�ξ塢���������׾����Ū�ˤ����ƸĿͤ�����Ǥ��ʤ��ͼ��ˤ����Ѥ��ޤ���</td>
    </tr>
  </table>
  <table>
    <tr>
      <td>��������ɬ�ܤηǺܾ�� </td>
    </tr>
    <tr>
      <td>�ʣ��˳�����ҥ��֥�ݥ���֥󡦥���ѥ󤬥��֥�ݥ���֥�Ź���̤��ơ��ܵҤξ��������������������ζ�̳��ԤäƤ��뤳�� </td>
    </tr>
    <tr>
      <td>�ʣ��˲���Ź�ν��ꡢ����ޤ���̾�Ρ��ʤ�Ӥ���ɽ�Ԥλ�̾ </td>
    </tr>
    <tr>
      <td>�ʣ��˸ܵҤˤ�륻�֥�ݥ���֥�Ź�Ǥο����ߤμ�äȾ������ι����ޤ��ϥ����ӥ��󶡤ε���˴ؤ����� </td>
    </tr>
    <tr>
      <td>�ʣ���ʧ������ɼ���ˤ�륻�֥󡼥���֥�Ź�ˤ������ʧ�����ܹ���ˤ����Ʊ߲ߤǹԤ��� </td>
    </tr>
    <tr>
      <td>�ʣ��˾����������ơ����Ͼ����ʡ���ʧ����ӿ����ߤμ�þ�綠��¾������ </td>
    </tr>
    <tr>
      <td>�ʣ��˾������ˤĤ��Ƥ��䤤��碌�����Ϣ����ʤ�Ӥ��Żҥ᡼�륢�ɥ쥹 </td>
    </tr>
    <tr>
      <td>�ʣ��˾��������Ф��륯�졼������Ϣ���� </td>
    </tr>
    <tr>
      <td>�ʣ��˾����������ʡ�������󥰥��դ�ɬ�פȤ�����³�� </td>
    </tr>
    <tr>
      <td>�ʣ���»�������Ǥ������ </td>
    </tr>
    <tr>
      <td>�������侩�ηǺܾ�� </td>
    </tr>
    <tr>
      <td>�ܵҤ��Ф��ơ��ʲ��ηﳰ������򥷥�åפΥ��󥿡��ͥåȥۡ���ڡ�����ޤ���¾�Υޥ����ǥ������������뤳�Ȥ�侩���뤬˾�ޤ����� </td>
    </tr>
    <tr>
      <td>�ʣ��˸ܵҤϡ��������ͤȤ��뤳�ȡ��ڤӡ��Ͷ�̾����ƿ̾���ܿͰʳ���̾���ˤ�뿽���ߤ�ػߤ��뤳�� </td>
    </tr>
    <tr>
      <td>�ʣ��˷ﳰ������Ω�λ��� </td>
    </tr>
    <tr>
      <td>�ʣ��˸ܵҤθĿ;������Ͽ������ </td>
    </tr>
    <tr>
      <td>�ʣ��˷ﳰ����������Τ������ѹ������뤳�Ȥξ�ǧ </td>
    </tr>
  </table>
  <table>
    <tr>
      <td> ����ӥˤ����ѻ�ʧ���Ѥ�Ԥʤä�����ӥ�Ź�ޤ��ֶ�������ʤ��ݤ���ս񤭤򵭽Ҥ��Ƥ���������</td>
    </tr>
 </table>
</body>
</html>
<?php
# �����ϥ���ץ�
$logger->logprint('DEBUG', '<<< ��ʧ����(SEJInvoice.php)������λ. >>>');
?>
