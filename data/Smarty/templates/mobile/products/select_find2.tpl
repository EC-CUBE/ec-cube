<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center"><!--{$tpl_class_name2}--></div>
<hr>

<!--{if $arrErr.classcategory_id2 != ""}-->
	<font color="#FF0000">��<!--{$tpl_class_name2}-->�����Ϥ��Ʋ�������</font><br>
<!--{/if}-->
<form method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
	<select name="classcategory_id2">
		<option value="">���򤷤Ƥ�������</option>
		<!--{html_options options=$arrClassCat2 selected=$arrForm.classcategory_id2.value}-->
	</select><br>
	<input type="hidden" name="mode" value="selectItem">
	<input type="hidden" name="classcategory_id1" value="<!--{$arrForm.classcategory_id1.value}-->">
	<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
	<center><input type="submit" name="submit" value="����"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
