<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">購入履歴</div>
<hr>

<!--{section name=cnt loop=$arrOrder}-->
	■<!--{$arrOrder[cnt].create_date|sfDispDBDate}--><br>
	注文番号:<!--{$arrOrder[cnt].order_id}--><br>
	<!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}-->
	合計金額:<!--{$arrOrder[cnt].payment_total|number_format}-->円<br>

	<div align="center">
	<form name="form1" method="post" action="history_detail.php">
		<input type="hidden" name="order_id" value="<!--{$arrOrder[cnt].order_id}-->">
		<input type="submit" name="submit" value="詳細を見る">
	</form>
	</div>
	<br>
<!--{/section}-->
<br>

<!--{$tpl_strnavi}-->

<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
