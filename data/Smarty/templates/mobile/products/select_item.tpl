<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">数量指定</div>
<hr>

<!--{if $arrErr.classcategory_id2 != ""}-->
	<font color="#FF0000">※数量を入力して下さい｡</font><br>
<!--{/if}-->
<form method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
	<input type="text" name="quantity" size="3" value="<!--{$arrForm.quantity.value|default:1}-->" maxlength=<!--{$smarty.const.INT_LEN}--> istyle="4"><br>
	<input type="hidden" name="mode" value="cart">
	<input type="hidden" name="classcategory_id1" value="<!--{$arrForm.classcategory_id1.value}-->">
	<input type="hidden" name="classcategory_id2" value="<!--{$arrForm.classcategory_id2.value}-->">
	<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
	<center><input type="submit" name="submit" value="かごに入れる"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
