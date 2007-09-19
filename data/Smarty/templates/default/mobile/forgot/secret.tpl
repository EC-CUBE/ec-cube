<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>パスワードを忘れた方</center>

<hr>

<!--{if $errmsg}-->
<font color="#ff0000"><!--{$errmsg}--></font><br>
<!--{/if}-->

ご登録時に入力した下記質問の答えを入力して「次へ」ボタンをクリックしてください。<br>
※下記質問の答えをお忘れになられた場合は、<a href="mailto:<!--{$arrSiteInfo.email02|escape}-->"><!--{$arrSiteInfo.email02|escape}--></a>までご連絡ください。<br>

<form action="<!--{$smarty.server.PHP_SELF|escape}-->" method="post">
<input type="hidden" name="mode" value="secret_check">

<!--{$Reminder|escape}-->：<input type="text" name="input_reminder" value="" size="40"><br>

<center><input type="submit" value="次へ" name="next"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
