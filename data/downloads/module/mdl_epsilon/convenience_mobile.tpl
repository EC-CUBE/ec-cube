<center>����ӥ˷��</center>

<hr>

<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="send">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">

�������顢����ʧ�����륳��ӥˤ����򤷡�ɬ�׻�������Ϥ��Ƥ���������<br>
���ϸ塢���ֲ��Ρּ��ءץܥ���򥯥�å����Ƥ���������<br>

<br>

����ӥˤμ���<br>
<font color="#ff0000"><!--{$arrErr.convenience}--></font>
<!--{foreach key=key item=item from=$arrConv}-->
<input type="radio" name="convenience" value="<!--{$key}-->" <!--{if $smarty.post.convenience == $key}-->checked<!--{/if}-->>
<!--{$item|escape}--><br>
<!--{/foreach}-->

��(����)<br>
<font color="#ff0000"><!--{$arrErr.order_kana01}--><!--{$arrErr.order_kana02}--></font>
<input type="text" name="order_kana01" size="15" value="<!--{$arrForm.order_kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

̾(����)<br>
<input type="text" name="order_kana02" size="15" value="<!--{$arrForm.order_kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>

�����ֹ�<br>
<font color="#ff0000"><!--{$arrErr.order_tel01}--><!--{$arrErr.order_tel02}--><!--{$arrErr.order_tel03}--></font>
<input type="text" name="order_tel01" size="6" value="<!--{$arrForm.order_tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">-<input type="text" name="order_tel02" size="6" value="<!--{$arrForm.order_tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4">-<input type="text" name="order_tel03" size="6" value="<!--{$arrForm.order_tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" istyle="4"><br>

<br>

<center><input type="submit" value="����"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<center>LOCKON CO.,LTD.</center>
<!-- ���եå��� �����ޤ� -->
