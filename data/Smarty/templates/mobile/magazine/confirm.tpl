<div align="center"><!--{$arrForm.kind|escape}-->��ǧ</div>
<hr>
<!--{$arrForm.kind|escape}-->��Ԥ��ޤ���<br>
�ʲ������ƤǤ�����Ǥ��礦����<br>

<br>
<form action="confirm.php" method="post">
	<input type="hidden" name="mode" value="<!--{$arrForm.type}-->">
	<input type="hidden" name="email" value="<!--{$arrForm.mail|escape}-->">
	���᡼�륢�ɥ쥹<br>
	<!--{$arrForm.mail|escape}--><br>
	<br>
	<div align="center"><input type="submit" name="submit" value="����"></div>
</form>
<br>

<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
