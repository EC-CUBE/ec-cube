<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">���Ϥ�����Ͽ��ǧ</div>
<hr>
<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="complete">

	�ڸĿ;����<br>
	<!--{$list_data.name01|escape}-->��<!--{$list_data.name02|escape}--><br>
	<!--{$list_data.kana01|escape}-->��<!--{$list_data.kana02|escape}--><br>
	��<!--{$list_data.zip01|escape}--> - <!--{$list_data.zip02|escape}--><br>
	<!--{$arrPref[$list_data.pref]|escape}--><!--{$list_data.addr01|escape}--><!--{$list_data.addr02|escape}--><br>
	<!--{$list_data.tel01|escape}-->-<!--{$list_data.tel02|escape}-->-<!--{$list_data.tel03|escape}--><br>

	<div align="center"><input type="submit" name="submit" value="����"></div>
	<div align="center"><input type="submit" name="return" value="���"></div>

	<!--{foreach from=$list_data key=key item=item}-->
		<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
	<!--{/foreach}-->
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
