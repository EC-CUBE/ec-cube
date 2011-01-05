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
<center>パスワードを忘れた方</center>

<font color="#ff0000">※新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</font>

<hr>

<!--{if $errmsg}-->
<font color="#ff0000"><!--{$errmsg}--></font><br>
<!--{/if}-->

<!--{if @$tpl_kara_mail_to != ''}-->
■ご登録時のメールアドレスからメールを送れる方は、次のリンクをクリックして空メールを送信してください。<br>
<center><a href="mailto:<!--{$tpl_kara_mail_to|u}-->">メール送信</a></center>

<br>

■メールを送れない方は、ご登録時のメールアドレスを入力して「次へ」ボタンをクリックしてください。<br>
<!--{else}-->
ご登録時のメールアドレスを入力して「次へ」ボタンをクリックしてください。<br>
<!--{/if}-->

<form action="<!--{$smarty.server.PHP_SELF|h}-->" method="post">
<input type="hidden" name="mode" value="mail_check">

メールアドレス：<input type="text" name="email" value="<!--{$tpl_login_email|h}-->" size="50" istyle="3"><br>

<center><input type="submit" value="次へ" name="next"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_CART_URL_PATH}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_TOP_URL_PATH}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
