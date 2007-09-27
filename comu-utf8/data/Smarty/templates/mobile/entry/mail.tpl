<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>メールアドレス登録</center>

<hr>

次のリンクをクリックして空メールを送信してください。
すぐにご案内のメールが送信されますので、メール内のリンクをクリックして会員情報の入力に進んでください。<br>
<font color="#ff0000">※ドメイン指定受信機能を利用されている方は、メールを送信する前に必ず「<!--{$tpl_from_address|escape}-->」からのメールが受信できるように設定しておいてください。</font><br>
<br>

<br>

<center><a href="mailto:<!--{$tpl_kara_mail_to|escape:'url'}-->">メール送信</a></center>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
