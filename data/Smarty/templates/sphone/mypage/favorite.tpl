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
<div id="mypagecolumn">
  <h2 class="title"><!--{$tpl_title|h}--></h2>
  <!--{if $tpl_navi != ""}-->
    <!--{include file=$tpl_navi}-->
  <!--{else}-->
    <!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
  <!--{/if}-->

  <div id="mycontentsarea">
    <form name="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="order_id" value="" />
    <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />
    <h2><!--{$tpl_subtitle|h}--></h2>

<!--{if $tpl_linemax > 0}-->

    <p><!--{$tpl_linemax}-->件のお気に入りがあります。</p>
    <div class="paging">
      <!--▼ページナビ-->
      <!--{$tpl_strnavi}-->
      <!--▲ページナビ-->
    </div>

    <form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="cart" />
    <input type="hidden" name="product_id" value="" />
    <table summary="お気に入り" id="mypage-history-list" class="entryform">
      <tr>
        <th width="40" class="alignC valignM">削除</th>
        <th width="60" class="alignC valignM">商品画像</th>
        <th width="200" class="alignC valignM">商品名</th>
        <th width="200" class="sale_price alignC valignM"><!--{$smarty.const.SALE_PRICE_TITLE}--><span class="mini">(税込)</span></th>
      </tr>
      <!--{section name=cnt loop=$arrFavorite}-->
      <!--{assign var=product_id value="`$arrFavorite[cnt].product_id`"}-->
      <tr>
       <td class="multi_send"><a href="javascript:fnModeSubmit('delete_favorite','product_id','<!--{$product_id|h}-->');">削除</a></td>
       <td><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$product_id|u}-->"><img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrFavorite[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65"></a></td>
       <td><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$product_id|u}-->"><!--{$arrFavorite[cnt].name}--></a></td>
       <td class="right sale_price alignR">
        <span class="price">
          <!--{if $arrFavorite[cnt].price02_min == $arrFavorite[cnt].price02_max}-->
            <!--{$arrFavorite[cnt].price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
          <!--{else}-->
            <!--{$arrFavorite[cnt].price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrFavorite[cnt].price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
          <!--{/if}-->円</span>
       </td>
     </tr>
     <!--{/section}-->
    </table>
    <br />
    <!--{if $stock_find_count > 0 && $customer_rank < 51}-->
    <div class="product-btn">
      <a href="javascript:void(document.form1.submit())" class="btn-cart">カートに入れる</a>
    </div>
    <!--{/if}-->
    </form>

    <!--{else}-->
    <p>お気に入りが登録されておりません。</p>
    <!--{/if}-->
    </form>
  </div>
</div>
