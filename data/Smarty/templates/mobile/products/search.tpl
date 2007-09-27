<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>商品検索</center>

<hr>

お探しの商品名・型番を入力してください。<br>

<form method="get" action="<!--{$smarty.const.MOBILE_URL_DIR}-->products/list.php">
<center>
<input type="hidden" name="mode" value="search">
<input type="text" name="name" size="18" maxlength="50" value="<!--{$smarty.get.name|escape}-->"><br>
<input type="submit" name="search" value="検索">
</center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
