<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">MYページTOP</div>
<hr>

<!--{$CustomerName1|escape}--> <!--{$CustomerName2|escape}-->様<br>
いつもご利用いただきありがとうございます。<br>
<br>
<a href="history.php" accesskey="1"><!--{1|numeric_emoji}-->購入履歴</a><br>
<a href="change.php" accesskey="2"><!--{2|numeric_emoji}-->登録内容変更</a><br>
<a href="refusal.php" accesskey="3"><!--{3|numeric_emoji}-->退会</a><br>

<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
