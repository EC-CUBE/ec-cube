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
■<!--{$arrDisp.create_date|sfDispDBDate}--><br>
注文番号:<!--{$arrDisp.order_id}--><br>
<br>
【お届け先】<br>
	<!--{assign var=key1 value="deliv_name01"}--><!--{assign var=key2 value="deliv_name02"}-->
	<!--{$arrDisp[$key1]|h}-->&nbsp;<!--{$arrDisp[$key2]|h}--><br>
	<!--{assign var=key1 value="deliv_zip01"}--><!--{assign var=key2 value="deliv_zip02"}-->
	〒<!--{$arrDisp[$key1]}-->-<!--{$arrDisp[$key2]}--><br>
	<!--{assign var=pref value=`$arrDisp.deliv_pref`}--><!--{$arrPref[$pref]}-->
	<!--{assign var=key value="deliv_addr01"}--><!--{$arrDisp[$key]|h}-->
	<!--{assign var=key value="deliv_addr02"}--><!--{$arrDisp[$key]|h}--><br>
<br>
【お届け日時指定】<br>
<!--{if $arrDisp.deliv_date eq "" and $arrDelivTime[$arrDisp.deliv_time_id] eq ""}-->
	指定なし<br>
<!--{else}-->
	<!--{$arrDisp.deliv_date|h}--> <!--{$arrDelivTime[$arrDisp.deliv_time_id]|h}--><br>
<!--{/if}-->
<br>
【お支払い方法】<br>
<!--{$arrPayment[$arrDisp.payment_id]|h}--><br>
<br>
【ご注文内容】<br>
<!--{section name=cnt loop=$arrDisp.quantity}-->
<!--{$arrDisp.product_name[cnt]|h}--><br>
<a href="<!--{$smarty.const.MOBILE_P_DETAIL_URLPATH}--><!--{$arrDisp.product_id[cnt]|u}-->">商品詳細→</a><br>
<!--{/section}-->
<br>
【購入金額】<br>
商品合計:<!--{$arrDisp.subtotal|number_format}-->円<br>
送料:<!--{assign var=key value="deliv_fee"}--><!--{$arrDisp[$key]|number_format|h}-->円<br>
合計:<!--{$arrDisp.payment_total|number_format}-->円<br>
<br>

<form action="order.php" method="post">
	<input type="hidden" name="order_id" value="<!--{$arrDisp.order_id}-->">
	<div align="center"><input type="submit" name="submit" value="再注文"></div>
</form>

<br>

<hr>

<a href="<!--{$smarty.const.MOBILE_CART_URLPATH}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_TOP_URLPATH}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>
