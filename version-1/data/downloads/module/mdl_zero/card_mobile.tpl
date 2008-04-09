<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>クレジット決済</center>

<hr>
<br>
<form method="post" action="<!--{$order_url|escape}-->">
	<!--{foreach key=key item=item from=$arrSendData}-->
	<input type="hidden" name="<!--{$key}-->" value="<!--{$item}-->">
	<!--{/foreach}-->
	<center><input type="submit" value="クレジット決済に進む"></center>
</form>

<br>
<hr>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
