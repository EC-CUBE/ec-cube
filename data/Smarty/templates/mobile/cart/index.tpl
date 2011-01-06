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
<!--▼CONTENTS-->
<!--▼MAIN ONTENTS-->
<div align="center"><font color="#000080">かご表示</font></div>
<!--{if $tpl_message != ""}-->
	<!--{$tpl_message}--><br>
<!--{/if}-->
<!--{if count($cartItems) > 0}-->
<!--{foreach from=$cartKeys item=key}-->
<form name="form<!--{$key}-->" id="form<!--{$key}-->" method="post" action="?"  utn>
	<input type="hidden" name="mode" value="confirm">
	<input type="hidden" name="cart_no" value="">
	<input type="hidden" name="cartKey" value="<!--{$key}-->">
	<!--ご注文内容ここから-->
	<hr>
    <!--{foreach from=$cartItems[$key] item=item}-->
		<!--{* 商品名 *}--><!--{$item.productsClass.name|h}--><br>
        <!--{* 規格名1 *}--><!--{if $item.productsClass.classcategory_name1 != ""}--><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br><!--{/if}-->
        <!--{* 規格名2 *}--><!--{if $item.productsClass.classcategory_name2 != ""}--><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--><br><!--{/if}-->
		<!--{* 販売価格 *}-->
		&yen;<!--{$item.productsClass.price02|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
		× <!--{$item.quantity}--><br>
		<br>
		<!--{* 数量 *}-->
		数量:<!--{$item.quantity}-->
		<a href="?mode=up&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">+</a>
		<a href="?mode=down&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">-</a>
		<a href="?mode=delete&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">削除</a><br>
		<!--{* 合計 *}-->
		小計:<!--{$item.total_inctax|number_format}-->円<br>
		<div align="right"><a href="<!--{$smarty.const.MOBILE_P_DETAIL_URL_PATH}--><!--{$item.productsClass.product_id|u}-->">商品詳細へ→</a></div>
		<HR>
	<!--{/foreach}-->
	商品合計:<!--{$tpl_total_inctax[$key]|number_format}-->円<br>
	合計:<!--{$arrData[$key].total-$arrData[$key].deliv_fee|number_format}-->円<br>
	<!--{if $smarty.const.USE_POINT !== false}-->
        <br>
    <!--{if $arrData[$key].birth_point > 0}-->
		お誕生月ﾎﾟｲﾝﾄ<br>
		<!--{$arrData[$key].birth_point|number_format}-->pt<br>
	<!--{/if}-->
		今回加算ﾎﾟｲﾝﾄ<br>
        <!--{$arrData[$key].add_point|number_format}-->pt<br>
	<!--{/if}-->
	<br>
	<center><input type="submit" value="注文する"></center>
</form>
<!--{/foreach}-->
<!--{if $tpl_prev_url != ""}-->
    <a href="<!--{$tpl_prev_url}-->">[emoji:69]お買物を続ける</a>
<!--{/if}-->
<!--{else}-->
	※現在ｶｰﾄ内に商品はございません｡<br>
<!--{/if}-->
<!--▲CONTENTS-->
<!--▲MAIN CONTENTS-->
<!--▲CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_TOP_URL_PATH}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
