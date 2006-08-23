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

  <h1>お買い上げ情報</h1>
  <p>
    取引ID:&nbsp;<strong><!--{$order_id|escape}--></strong>
  </p>
  <p>
    価格:&nbsp;<strong><!--{$amount|escape}--></strong>&nbsp;円
  </p>
  <p>
    お支払期限:&nbsp;<strong><!--{$pay_limit|escape}--></strong>
  </p>
  <h3>お客様情報</h3>
  <form action="<!--{$PAY_URL_CKS}-->" method="POST" onsubmit="disableSubmit(this)">
    <table>
      <tr>
        <td>姓：</td>
        <td><input type="text" name="REQ_NAME1" /></td>
      </tr>
      <tr>
        <td>名：</td>
        <td><input type="text" name="REQ_NAME2" /></td>
      </tr>
      <tr>
        <td>電話番号：</td>
        <td><input type="text" name="REQ_TEL_NO" /></td>
      </tr>
    </table>
    <input type="hidden" name="REQ_ORDER" value="<!--{$params_str}-->" />
    <input type="hidden" name="REQ_ORDER_SIG" value="<!--{$hash}-->" />
    <p><input type="submit" value=" 申込 " /></p>
  </form>
  <table>
    <tr>
      <td>お支払を行なわれる方の為に支払い方法についてご案内ください。</td>
    </tr>
  </table>
  <h3>必ず下記の事項をWebページのどこかに記載してください。</h3>
  <table>
    <tr>
      <td> コンビニで費用支払後決済を行なったコンビニ店舗で返金を受けれない旨の注意書きを記述してください。</td>
    </tr>
  </table>
