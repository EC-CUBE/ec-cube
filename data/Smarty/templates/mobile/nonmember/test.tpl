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
<form name="member_form" id="member_form" method="post" action="sendRequest(test,'&test=test','POST','json.php',true,true)">
	<div align="center"><input type="submit" value="テスト"></div><br>
</form>
<!--▲まだ会員登録されていないお客様-->
<!--{/if}-->

<!--▼会員登録されないお客様-->
<form name="nonmember_form" id="nonmember_form" method="post" action="<!--{$smarty.const.MOBILE_URL_DIR}-->shopping/index.php">
	<input type="hidden" name="mode" value="nonmember">
	<input type="hidden" name="mode2" value="set1">
	<center><input type="submit" value="登録せずに購入" name="nonmember"></center>
</form>
<!--▲会員登録されないお客様-->
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
