<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>パスワードを忘れた方</center>

<font color="#ff0000">※新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</font>

<hr>

<!--{if $errmsg}-->
<font color="#ff0000"><!--{$errmsg}--></font><br>
<!--{/if}-->

<!--{if @$tpl_kara_mail_to != ''}-->
■ご登録時のメールアドレスからメールを送れる方は、次のリンクをクリックして空メールを送信してください。<br>
<center><a href="mailto:<!--{$tpl_kara_mail_to|escape:'url'}-->">メール送信</a></center>

<br>

■メールを送れない方は、ご登録時のメールアドレスを入力して「次へ」ボタンをクリックしてください。<br>
<!--{else}-->
ご登録時のメールアドレスを入力して「次へ」ボタンをクリックしてください。<br>
<!--{/if}-->

<form action="<!--{$smarty.server.PHP_SELF|escape}-->" method="post">
<input type="hidden" name="mode" value="mail_check">

メールアドレス：<input type="text" name="email" value="<!--{$tpl_login_email|escape}-->" size="50" istyle="3"><br>

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
