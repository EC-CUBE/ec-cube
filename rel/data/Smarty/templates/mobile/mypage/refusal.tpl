<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">退会確認</div>
<hr>
<!--{$CustomerName1|escape}--> <!--{$CustomerName2|escape}-->様、会員から退会をされますと、登録されているお届け先の情報など全て削除されますがよろしいでしょうか。<br>
<br>
<div align="center">
<form action="<!--{$smarty.server.PHP_SELF|escape}-->" method="post">
	<input type="submit" name="no" value="いいえ">
	<input type="submit" name="complete" value="はい">
</form>
</div>
<br>

<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
