<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">ログイン</div>
<hr>

<!--▼CONTENTS-->
<form name="login_mypage" id="login_mypage" method="post" action="./index.php">
	<input type="hidden" name="mode" value="login" >
<!--{if !$tpl_valid_phone_id}-->
	▼メールアドレス<br>
	<!--{assign var=key value="login_email"}-->
	<font color="#FF0000"><!--{$arrErr[$key]}--></font>
	<input type="text" name="<!--{$key}-->" value="<!--{$login_email|escape}-->" 
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
<!--▲CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
