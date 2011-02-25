<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<div align="center">メルマガ登録</div>
<hr>
ご登録いただいたお客様へは<!--{if $arrSiteInfo.shop_name != ""}--><!--{$arrSiteInfo.shop_name|h}-->より<!--{/if}-->商品やキャンペーン情報をメールでお届けいたします。<br>
※<!--{if $arrSiteInfo.shop_name != ""}--><!--{$arrSiteInfo.shop_name|h}-->では<!--{/if}-->ご利用規約に従い利用者のアドレスを保護しています。<br>
<br>
<form action="confirm.php" method="post">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

	■登録<br>
	<font color="#FF0000"><!--{$arrErr.regist}--></font>
	<input type="text" name="regist" value="<!--{$arrForm.regist|h}-->" istyle="3"><br>
	<div align="center"><input type="submit" name="btnRegist" value="次へ"></div>
	<br>

	■解除<br>
	<font color="#FF0000"><!--{$arrErr.cancel}--></font>
	<input type="text" name="cancel" value="<!--{$arrForm.cancel|h}-->" istyle="3"><br>
	<div align="center"><input type="submit" name="btnCancel" value="次へ"></div>
	<br>
</form>
<br>

■メールアドレス変更<br>
メールアドレス変更希望の方は一度、登録解除してから、もう一度登録し直してください。<br>

<br>

<hr>

<a href="<!--{$smarty.const.MOBILE_CART_URLPATH}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_TOP_URLPATH}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
