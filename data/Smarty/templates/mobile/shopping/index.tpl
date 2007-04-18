<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--▼CONTENTS-->
<!--▼MAIN ONTENTS-->
<div align="center"><font color="#000080">ご注文手続き</font></div><br>
<hr>
<!--{if !$tpl_valid_phone_id}-->
<!--▼会員登録がお済みでないお客様-->
■初めてご注文の方<br>
(新規ご登録)<br>
<form name="member_form" id="member_form" method="post" action="<!--{$smarty.const.MOBILE_URL_DIR}-->entry/kiyaku.php">
	<div align="center"><input type="submit" value="新規登録"></div><br>
</form>
<!--▲まだ会員登録されていないお客様-->
<!--{/if}-->

<!--▼会員登録がお済みのお客様-->
<form name="member_form" id="member_form" method="post" action="./deliv.php">
	<input type="hidden" name="mode" value="login">
<!--{if !$tpl_valid_phone_id}-->
	■以前にご注文された方<br>
	(モバイル又はPCでご登録済み)<br>
	▼メールアドレス<br>
	<!--{assign var=key value="login_email"}-->
	<font color="#FF0000"><!--{$arrErr[$key]}--></font>
	<input type="text" name="<!--{$key}-->" value="<!--{$tpl_login_email|escape}-->" 
		maxlength="<!--{$arrForm[$key].length}-->" size="40" istyle="3"><br>
<!--{else}-->
<input type="hidden" name="login_email" value="dummy">
<!--{/if}-->
	▼パスワード<br>
	<!--{assign var=key value="login_pass"}--><font color="#FF0000"><!--{$arrErr[$key]}--></font>
	<input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" size="40" istyle="3"><br>
	<center><input type="submit" value="送信" name="log"></center><br>
	<a href="<!--{$smarty.const.MOBILE_URL_DIR}-->forgot/index.php">パスワードをお忘れの方はこちら</a><br>
</form>
<!--▲会員登録がお済のお客様-->
<!--▲MAIN ONTENTS-->
<!--▲CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
