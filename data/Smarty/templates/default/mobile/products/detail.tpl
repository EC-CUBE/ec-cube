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
<!--▼MAIN CONTENTS-->
<!--ﾀｲﾄﾙここから-->
<!--★商品名★-->
<div align="center"><!--{$arrProduct.name|escape}--></div>
<hr>
<!--ﾀｲﾄﾙここまで-->
<!--詳細ここから-->
<!--{assign var=key value="main_image"}-->
<img src="<!--{$arrFile[$key].filepath}-->"><br>
<!--{* オペビルダー用 *}-->
<!--{if "sfViewDetailOpe"|function_exists === TRUE}-->
<!--{include file=`$smarty.const.MODULE_PATH`mdl_opebuilder/detail_ope_mb_view.tpl}-->
<!--{/if}-->
<!--★詳細ﾒｲﾝｺﾒﾝﾄ★-->
[emoji:76]<!--{$arrProduct.main_comment|nl2br}--><br>
<br>
<!--ｱｲｺﾝ-->
<!--★価格★-->
<font color="#FF0000">価格(税込):
<!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
	<!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{else}-->
	<!--{$arrProduct.price02_min|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->･<!--{$arrProduct.price02_max|sfPreTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{/if}-->
円</font><br/>
<!--{if $arrProduct.price01_max > 0}-->
<font color="#FF0000">参考市場価格:
<!--{if $arrProduct.price01_min == $arrProduct.price01_max}-->
<!--{$arrProduct.price01_min|number_format}-->
<!--{else}-->
<!--{$arrProduct.price01_min|number_format}-->･<!--{$arrProduct.price01_max|number_format}-->
<!--{/if}-->
円</font><br>
<!--{/if}-->
<form name="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
	<input type="hidden" name="mode" value="select">
	<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
<!--{if $tpl_stock_find}-->
	<!--★商品を選ぶ★-->
	<center><input type="submit" name="select" id="cart" value="この商品を選ぶ"></center>
<!--{else}-->
	<font color="#FF0000">申し訳ございませんが､只今品切れ中です｡</font>
<!--{/if}-->
</form>
<!--詳細ここまで-->
<!--▲CONTENTS-->

<br>
<hr>

<a href="<!--{$smarty.const.MOBILE_URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->かごを見る</a><br>
<a href="<!--{$smarty.const.MOBILE_URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<!--{include file='footer.tpl'}-->
<!-- ▲フッター ここまで -->
