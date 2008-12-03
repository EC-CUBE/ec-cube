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
<!--▼CONTENTS-->
<!--▼MAIN ONTENTS-->
<div align="center"><font color="#000080">かご表示</font></div>
<!--{if $tpl_message != ""}-->
	<!--{$tpl_message}--><br>
<!--{/if}-->
<!--{if count($arrProductsClass) > 0}-->
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->"  utn>
	<input type="hidden" name="mode" value="confirm">
	<input type="hidden" name="cart_no" value="">
	<!--ご注文内容ここから-->
	<hr>
	<!--{section name=cnt loop=$arrProductsClass}-->
		<!--{* 商品名 *}--><!--{$arrProductsClass[cnt].name|escape}--><br>
		<!--{* 価格 *}-->
		<!--{if $arrProductsClass[cnt].price02 != ""}-->
			\<!--{$arrProductsClass[cnt].price02|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
		<!--{else}-->
			\<!--{$arrProductsClass[cnt].price01|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
		<!--{/if}-->
		× <!--{$arrProductsClass[cnt].quantity}--><br>
		<!--{* 詳細 *}-->
		<!--{if $arrProductsClass[cnt].classcategory_name1 != ""}-->
			<!--{$arrProductsClass[cnt].class_name1}-->:<!--{$arrProductsClass[cnt].classcategory_name1}--><br>
		<!--{/if}-->
		<!--{if $arrProductsClass[cnt].classcategory_name2 != ""}-->
			<!--{$arrProductsClass[cnt].class_name2}-->:<!--{$arrProductsClass[cnt].classcategory_name2}--><br>
		<!--{/if}-->
		<br>
		<!--{* 数量 *}-->
		数量:<!--{$arrProductsClass[cnt].quantity}-->
		<a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=up&amp;cart_no=<!--{$arrProductsClass[cnt].cart_no}-->">+</a>
		<a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=down&amp;cart_no=<!--{$arrProductsClass[cnt].cart_no}-->">-</a>
		<a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=delete&amp;cart_no=<!--{$arrProductsClass[cnt].cart_no}-->">削除</a><br>
		<!--{* 合計 *}-->
		小計:<!--{$arrProductsClass[cnt].total_pretax|number_format}-->円<br>
		<div align="right"><a href="<!--{$smarty.const.MOBILE_DETAIL_P_HTML}--><!--{$arrProductsClass[cnt].product_id}-->">商品購入詳細へ→</a></div>
		<HR>
	<!--{/section}-->
	商品合計:<!--{$tpl_total_pretax|number_format}-->円<br>
	合計:<!--{$arrData.total-$arrData.deliv_fee|number_format}-->円<br>
	<!--{if $arrData.birth_point > 0}-->
		お誕生月ﾎﾟｲﾝﾄ<br>
		<!--{$arrData.birth_point|number_format}-->pt<br>
	<!--{/if}-->
	<br>
	<center><input type="submit" value="注文する" name="confirm"></center>
</form>
<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<center><input type="submit" value="お買物を続ける" name="continue"></center>
</form>
<!--{else}-->
	※現在ｶｰﾄ内に商品はございません｡<br>
<!--{/if}-->
<!--▲CONTENTS-->
<!--▲MAIN CONTENTS-->
<!--▲CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
