<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center"><!--{$arrForm.kind|escape}-->確認</div>
<hr>
<!--{$arrForm.kind|escape}-->を行います。<br>
以下の内容でよろしいでしょうか。<br>

<br>
<form action="confirm.php" method="post">
	<input type="hidden" name="mode" value="<!--{$arrForm.type}-->">
	<input type="hidden" name="email" value="<!--{$arrForm.mail|escape}-->">
	■メールアドレス<br>
	<!--{$arrForm.mail|escape}--><br>
	<br>
	<div align="center"><input type="submit" name="submit" value="決定"></div>
</form>
<br>

<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
