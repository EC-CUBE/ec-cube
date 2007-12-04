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
<div align="center">購入履歴</div>
<hr>

<!--{section name=cnt loop=$arrOrder}-->
	■<!--{$arrOrder[cnt].create_date|sfDispDBDate}--><br>
	注文番号:<!--{$arrOrder[cnt].order_id}--><br>
	<!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}-->
	合計金額:<!--{$arrOrder[cnt].payment_total|number_format}-->円<br>

	<div align="center">
	<form name="form1" method="post" action="history_detail.php">
		<input type="hidden" name="order_id" value="<!--{$arrOrder[cnt].order_id}-->">
		<input type="submit" name="submit" value="詳細を見る">
	</form>
	</div>
	<br>
<!--{/section}-->
<br>

<!--{$tpl_strnavi}-->

<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
