<div align="center">���ޥ���Ͽ</div>
<hr>
����Ͽ���������������ͤؤ�<!--{if $arrSiteInfo.shop_name != ""}--><!--{$arrSiteInfo.shop_name|escape}-->���<!--{/if}-->���ʤ䥭���ڡ�������᡼��Ǥ��Ϥ��������ޤ���<br>
��<!--{if $arrSiteInfo.shop_name != ""}--><!--{$arrSiteInfo.shop_name|escape}-->�Ǥ�<!--{/if}-->�����ѵ���˽������ѼԤΥ��ɥ쥹���ݸ�Ƥ��ޤ���<br>
<br>
<form action="confirm.php" method="post">
	����Ͽ<br>
	<font color="#FF0000"><!--{$arrErr.regist}--></font>
	<input type="text" name="regist" value="<!--{$arrForm.regist|escape}-->" istyle="3"><br>
	<div align="center"><input type="submit" name="btnRegist" value="����"></div>
	<br>

	�����<br>
	<font color="#FF0000"><!--{$arrErr.cancel}--></font>
	<input type="text" name="cancel" value="<!--{$arrForm.cancel|escape}-->" istyle="3"><br>
	<div align="center"><input type="submit" name="btnCancel" value="����"></div>
	<br>
</form>
<br>

���᡼�륢�ɥ쥹�ѹ�<br>
�᡼�륢�ɥ쥹�ѹ���˾�����ϰ��١���Ͽ������Ƥ��顢�⤦������Ͽ��ľ���Ƥ���������<br>

<br>

<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
