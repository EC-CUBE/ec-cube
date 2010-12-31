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
<div align="center">ログイン</div>
<hr>

<!--▼CONTENTS-->
<form name="login_mypage" id="login_mypage" method="post" action="./<!--{$smarty.const.DIR_INDEX_URL}-->">
	<input type="hidden" name="mode" value="login" >
<!--{if !$tpl_valid_phone_id}-->
	▼メールアドレス<br>
	<!--{assign var=key value="login_email"}-->
	<font color="#FF0000"><!--{$arrErr[$key]}--></font>
	<input type="text" name="<!--{$key}-->" value="<!--{$login_email|h}-->" 
		size="40" istyle="3"><br>
<!--{else}-->
	<input type="hidden" name="login_email" value="dummy">
<!--{/if}-->
	▼パスワード<br>
	<!--{assign var=key value="login_pass"}--><font color="#FF0000"><!--{$arrErr[$key]}--></font>
	<input type="password" name="<!--{$key}-->" size="40" istyle="3"><br>
	<center><input type="submit" value="送信" name="log"></center><br>
	<a href="<!--{$smarty.const.MOBILE_URL_PATH}-->forgot/<!--{$smarty.const.DIR_INDEX_URL}-->">パスワードをお忘れの方はこちら</a><br>
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
