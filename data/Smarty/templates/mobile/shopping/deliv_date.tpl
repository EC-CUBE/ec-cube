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
<center>お届け日時指定</center>

<hr>

<form method="post" action="?">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="payment_id" value="<!--{$arrForm.payment_id.value}-->">
<!--<input type="hidden" name="message" value="">-->
<!--{if $tpl_login == 1}-->
<!--<input type="hidden" name="point_check" value="2">-->
<!--{/if}-->

■お届け日<br>
<!--{assign var=key value="deliv_date"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="red"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<!--{if $arrDelivDate}-->
<select name="<!--{$key}-->">
<option value="">指定なし</option>
<!--{html_options options=$arrDelivDate selected=$arrForm[$key].value}-->
</select>
<!--{else}-->
ご指定頂けません。
<!--{/if}-->
<br><br>

■お届け時間<br>
<!--{assign var=key value="deliv_time_id"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="red"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<select name="<!--{$key}-->">
<option value="">指定なし</option>
<!--{html_options options=$arrDelivTime selected=$arrForm[$key].value}-->
</select>
<br>

<center><input type="submit" value="次へ"></center>
<center><input type="submit" name="return" value="戻る"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_CART_URL_PATH}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_TOP_URL_PATH}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
