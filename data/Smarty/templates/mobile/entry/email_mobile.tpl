<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<center>携帯メール登録</center>

<hr>

<!--{$tpl_name|escape}-->様<br>
いつもご利用いただきありがとうございます。ご使用の携帯電話のメールアドレスをご登録下さい。<br>

<br>

<!--{assign var=key value='email_mobile'}-->
<!--{if @$tpl_kara_mail_to != ''}-->
<font color="#ff0000"><!--{$arrErr[$key]|default:''}--></font>
次のリンクをクリックして空メールを送信してください。<br>
<center><a href="mailto:<!--{$tpl_kara_mail_to|escape:'url'}-->">メール送信</a></center>
<!--{else}-->
<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
▼メールアドレス<br>
<font color="#ff0000"><!--{$arrErr[$key]|default:''}--></font>
<input type="text" name="email_mobile" value="<!--{$arrForm[$key].value|escape}-->" size="40" maxlength="<!--{$arrForm[$key].length}-->" istyle="3"><br>
<center><input type="submit" value="送信"></center>
</form>
<!--{/if}-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
