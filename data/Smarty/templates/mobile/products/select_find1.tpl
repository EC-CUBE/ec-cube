<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center"><!--{$tpl_class_name1}--></div>
<hr>

<!--{if $arrErr.classcategory_id1 != ""}-->
	<font color="#FF0000">※<!--{$tpl_class_name1}-->を入力して下さい｡</font><br>
<!--{/if}-->
<form method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
	<select name="classcategory_id1">
		<option value="">選択してください</option>
		<!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
	</select><br>
	<input type="hidden" name="mode" value="select2">
	<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
	<center><input type="submit" name="submit" value="次へ"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
