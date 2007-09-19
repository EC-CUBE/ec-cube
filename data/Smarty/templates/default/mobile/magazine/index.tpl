<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<div align="center">メルマガ登録</div>
<hr>
ご登録いただいたお客様へは<!--{if $arrSiteInfo.shop_name != ""}--><!--{$arrSiteInfo.shop_name|escape}-->より<!--{/if}-->商品やキャンペーン情報をメールでお届けいたします。<br>
※<!--{if $arrSiteInfo.shop_name != ""}--><!--{$arrSiteInfo.shop_name|escape}-->では<!--{/if}-->ご利用規約に従い利用者のアドレスを保護しています。<br>
<br>
<form action="confirm.php" method="post">
	■登録<br>
	<font color="#FF0000"><!--{$arrErr.regist}--></font>
	<input type="text" name="regist" value="<!--{$arrForm.regist|escape}-->" istyle="3"><br>
	<div align="center"><input type="submit" name="btnRegist" value="次へ"></div>
	<br>

	■解除<br>
	<font color="#FF0000"><!--{$arrErr.cancel}--></font>
	<input type="text" name="cancel" value="<!--{$arrForm.cancel|escape}-->" istyle="3"><br>
	<div align="center"><input type="submit" name="btnCancel" value="次へ"></div>
	<br>
</form>
<br>

■メールアドレス変更<br>
メールアドレス変更希望の方は一度、登録解除してから、もう一度登録し直してください。<br>

<br>

<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
