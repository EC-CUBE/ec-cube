<center>�ѥ���ɤ�˺�줿��</center>

<hr>

<!--{if $errmsg}-->
<font color="#ff0000"><!--{$errmsg}--></font><br>
<!--{/if}-->

����Ͽ�������Ϥ���������������������Ϥ��ơּ��ءץܥ���򥯥�å����Ƥ���������<br>
�����������������˺��ˤʤ�줿���ϡ�<a href="mailto:<!--{$arrSiteInfo.email02|escape}-->"><!--{$arrSiteInfo.email02|escape}--></a>�ޤǤ�Ϣ����������<br>

<form action="<!--{$smarty.server.PHP_SELF|escape}-->" method="post">
<input type="hidden" name="mode" value="secret_check">

<!--{$Reminder|escape}-->��<input type="text" name="input_reminder" value="" size="40"><br>

<center><input type="submit" value="����" name="next"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
