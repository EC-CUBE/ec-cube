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
<center>ご注文内容確認</center>

<hr>

<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">

下記のご注文内容に間違いはございませんか？<br>

<br>

【お届け先】<br>
<!--{* 別のお届け先が選択されている場合 *}-->
<!--{if $arrData.deliv_check >= 1}-->
<!--{$arrData.deliv_name01|escape}--> <!--{$arrData.deliv_name02|escape}--><br>
〒<!--{$arrData.deliv_zip01|escape}-->-<!--{$arrData.deliv_zip02|escape}--><br>
<!--{$arrPref[$arrData.deliv_pref]}--><!--{$arrData.deliv_addr01|escape}--><!--{$arrData.deliv_addr02|escape}--><br>
<!--{else}-->
<!--{$arrData.order_name01|escape}--> <!--{$arrData.order_name02|escape}--><br>
〒<!--{$arrData.order_zip01|escape}-->-<!--{$arrData.order_zip02|escape}--><br>
<!--{$arrPref[$arrData.order_pref]}--><!--{$arrData.order_addr01|escape}--><!--{$arrData.order_addr02|escape}--><br>
<!--{/if}-->

<br>

【お届け日時指定】<br>
日：<!--{$arrData.deliv_date|escape|default:"指定なし"}--><br>
時間：<!--{$arrData.deliv_time|escape|default:"指定なし"}--><br>

<br>

【お支払い方法】<br>
<!--{$arrData.payment_method|escape}--><br>

<br>

【ご注文内容】<br>
<!--{section name=cnt loop=$arrProductsClass}-->
<!--{$arrProductsClass[cnt].name}--> <!--{$arrProductsClass[cnt].quantity|number_format}--><br>
<!--{/section}-->

<br>

【購入金額】<br>
商品合計：<!--{$tpl_total_pretax|number_format}--><br>
送料：<!--{$arrData.deliv_fee|number_format}--><br>
<!--{if $arrData.charge > 0}-->手数料：<!--{$arrData.charge|number_format}--><br><!--{/if}-->
合計：<!--{$arrData.payment_total|number_format}--><br>
(内消費税：<!--{$arrData.tax|number_format}-->)<br>

<br>

<center><input type="submit" value="注文"></center>
</form>
<form action="<!--{$smarty.const.MOBILE_URL_SHOP_PAYMENT}-->" method="post">
<input type="hidden" name="mode" value="deliv_date">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="payment_id" value="<!--{$arrData.payment_id}-->">
<input type="hidden" name="deliv_date" value="<!--{$arrData.deliv_date}-->">
<input type="hidden" name="deliv_time_id" value="<!--{$arrData.deliv_time_id}-->">
<center><input type="submit" value="戻る"></center>
</form>

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
