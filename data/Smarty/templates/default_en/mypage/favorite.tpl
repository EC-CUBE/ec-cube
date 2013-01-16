<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
    <div id="mycontents_area">
        <form name="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="order_id" value="" />
        <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />
        <input type="hidden" name="mode" value="" />
        <input type="hidden" name="product_id" value="" />
        <h3><!--{$tpl_subtitle|h}--></h3>

        <!--{if $tpl_linemax > 0}-->

            <p>There are <span class="attention"><!--{$tpl_linemax}--> items</span> in your favorites.</p>
            <div class="paging">
                <!--▼ページナビ-->
                <!--{$tpl_strnavi}-->
                <!--▲ページナビ-->
            </div>

            <table summary="Favorites">
                <col width="15%" />
                <col width="20%" />
                <col width="45%" />
                <col width="20%" />
                <tr>
                    <th class="alignC">Delete</th>
                    <th class="alignC">Product image</th>
                    <th class="alignC">Product name</th>
                    <th class="alignC"><!--{$smarty.const.SALE_PRICE_TITLE}-->(tax included)</th>
                </tr>
                <!--{section name=cnt loop=$arrFavorite}-->
                    <!--{assign var=product_id value="`$arrFavorite[cnt].product_id`"}-->
                    <tr>
                        <td class="alignC"><a href="javascript:fnModeSubmit('delete_favorite','product_id','<!--{$product_id|h}-->');">Delete</a></td>
                        <td class="alignC"><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$product_id|u}-->"><img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrFavorite[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65"></a></td>
                        <td><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$product_id|u}-->"><!--{$arrFavorite[cnt].name}--></a></td>
                        <td class="alignR sale_price">
                            <span class="price">
                                &#036;
                                <!--{if $arrFavorite[cnt].price02_min_inctax == $arrFavorite[cnt].price02_max_inctax}-->
                                    <!--{$arrFavorite[cnt].price02_min_inctax|number_format}-->
                                <!--{else}-->
                                    <!--{$arrFavorite[cnt].price02_min_inctax|number_format}--> - <!--{$arrFavorite[cnt].price02_max_inctax|number_format}-->
                                <!--{/if}--></span>
                        </td>
                    </tr>
                <!--{/section}-->
            </table>
            <br />

        <!--{else}-->
            <p>There are no favorites saved.</p>
        <!--{/if}-->
        </form>
    </div>
</div>
