<center>���ʸ���</center>

<hr>

��õ���ξ���̾�����֤����Ϥ��Ƥ���������<br>

<form method="get" action="<!--{$smarty.const.URL_DIR}-->products/list.php">
<center>
<input type="hidden" name="mode" value="search">
<input type="text" name="name" size="18" maxlength="50" value="<!--{$smarty.get.name|escape}-->"><br>
<input type="submit" name="search" value="����">
</center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
