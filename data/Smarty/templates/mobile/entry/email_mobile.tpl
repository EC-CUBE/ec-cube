<center>���ӥ᡼����Ͽ</center>

<hr>

<!--{$tpl_name|escape}-->��<br>
���Ĥ⤴���Ѥ����������꤬�Ȥ��������ޤ��������Ѥη������äΥ᡼�륢�ɥ쥹����Ͽ��������<br>

<br>

<!--{assign var=key value='email_mobile'}-->
<!--{if @$tpl_kara_mail_to != ''}-->
<font color="#ff0000"><!--{$arrErr[$key]|default:''}--></font>
���Υ�󥯤򥯥�å����ƶ��᡼����������Ƥ���������<br>
<center><a href="mailto:<!--{$tpl_kara_mail_to|escape:'url'}-->">�᡼������</a></center>
<!--{else}-->
<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
���᡼�륢�ɥ쥹<br>
<font color="#ff0000"><!--{$arrErr[$key]|default:''}--></font>
<input type="text" name="email_mobile" value="<!--{$arrForm[$key].value|escape}-->" size="40" maxlength="<!--{$arrForm[$key].length}-->" istyle="3"><br>
<center><input type="submit" value="����"></center>
</form>
<!--{/if}-->

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
