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

  <h1>���㤤�夲����</h1>
  <p>
    ���ID:&nbsp;<strong><!--{$order_id|escape}--></strong>
  </p>
  <p>
    ����:&nbsp;<strong><!--{$amount|escape}--></strong>&nbsp;��
  </p>
  <p>
    ����ʧ����:&nbsp;<strong><!--{$pay_limit|escape}--></strong>
  </p>
  <h3>�����;���</h3>
  <form action="<!--{$PAY_URL_CKS}-->" method="POST" onsubmit="disableSubmit(this)">
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
    <input type="hidden" name="REQ_ORDER" value="<!--{$params_str}-->" />
    <input type="hidden" name="REQ_ORDER_SIG" value="<!--{$hash}-->" />
    <p><input type="submit" value=" ���� " /></p>
  </form>
  <table>
    <tr>
      <td>����ʧ��Ԥʤ������ΰ٤˻�ʧ����ˡ�ˤĤ��Ƥ����⤯��������</td>
    </tr>
  </table>
  <h3>ɬ�������λ����Web�ڡ����Τɤ����˵��ܤ��Ƥ���������</h3>
  <table>
    <tr>
      <td> ����ӥˤ����ѻ�ʧ���Ѥ�Ԥʤä�����ӥ�Ź�ޤ��ֶ�������ʤ��ݤ���ս񤭤򵭽Ҥ��Ƥ���������</td>
    </tr>
  </table>
