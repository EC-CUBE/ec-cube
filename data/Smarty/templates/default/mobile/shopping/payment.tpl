<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
<center>支払い方法指定</center>

<hr>

<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="deliv_date">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="deliv_date" value="<!--{$arrForm.deliv_date.value}-->">
<input type="hidden" name="deliv_time_id" value="<!--{$arrForm.deliv_time_id.value}-->">
<!--<input type="hidden" name="message" value="">-->
<!--{if $tpl_login == 1}-->
<!--<input type="hidden" name="point_check" value="2">-->
<!--{/if}-->

<!--{assign var=key value="payment_id"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="red"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<!--{section name=cnt loop=$arrPayment}-->
<input type="radio" name="<!--{$key}-->" value="<!--{$arrPayment[cnt].payment_id}-->" <!--{$arrPayment[cnt].payment_id|sfGetChecked:$arrForm[$key].value}-->>
<!--{$arrPayment[cnt].payment_method|escape}-->
<br>
<!--{/section}-->

<center><input type="submit" value="次へ"></center>
<center><input type="submit" name="return" value="戻る"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
