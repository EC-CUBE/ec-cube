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
<center>携帯メール登録</center>

<hr>

<!--{$tpl_name|h}-->様<br>
いつもご利用いただきありがとうございます。ご使用の携帯電話のメールアドレスをご登録下さい。<br>

<br>

<!--{assign var=key value='email_mobile'}-->
<!--{if @$tpl_kara_mail_to != ''}-->
<font color="#ff0000"><!--{$arrErr[$key]|default:''}--></font>
次のリンクをクリックして空メールを送信してください。<br>
<center><a href="mailto:<!--{$tpl_kara_mail_to|u}-->">メール送信</a></center>
<!--{else}-->
<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
▼メールアドレス<br>
<font color="#ff0000"><!--{$arrErr[$key]|default:''}--></font>
<input type="text" name="email_mobile" value="<!--{$arrForm[$key].value|h}-->" size="40" maxlength="<!--{$arrForm[$key].length}-->" istyle="3"><br>
<center><input type="submit" value="送信"></center>
<center><a href="../mypage/<!--{$smarty.const.DIR_INDEX_URL}-->" accesskey="0">今は登録しない</a></center>
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
