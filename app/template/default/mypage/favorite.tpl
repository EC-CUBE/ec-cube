<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="order_id" value="" />
            <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />
            <input type="hidden" name="mode" value="" />
            <input type="hidden" name="product_id" value="" />
            <h3><!--{$tpl_subtitle|h}--></h3>

            <!--{if $tpl_linemax > 0}-->

                <p><span class="attention"><!--{$tpl_linemax}-->件</span>のお気に入りがあります。</p>
                <div class="paging">
                    <!--▼ページナビ-->
                    <!--{$tpl_strnavi}-->
                    <!--▲ページナビ-->
                </div>

                <table summary="お気に入り">
                    <col width="15%" />
                    <col width="20%" />
                    <col width="45%" />
                    <col width="20%" />
                    <tr>
                        <th class="alignC">削除</th>
                        <th class="alignC">商品画像</th>
                        <th class="alignC">商品名</th>
                        <th class="alignC"><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)</th>
                    </tr>
                    <!--{section name=cnt loop=$arrFavorite}-->
                        <!--{assign var=product_id value="`$arrFavorite[cnt].product_id`"}-->
                        <tr>
                            <td class="alignC">
                                <a href="javascript:eccube.setModeAndSubmit('delete_favorite','product_id','<!--{$product_id|h}-->');">
                                    削除</a>
                            </td>
                            <td class="alignC">
                                <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$product_id|u}-->">
                                    <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrFavorite[cnt].main_list_image|sfNoImageMainList|h}-->" style="max-width: 65px;max-height: 65px;" alt="<!--{$arrFavorite[cnt].name|h}-->" />
                            </td>
                            <td>
                                <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$product_id|u}-->">
                                    <!--{$arrFavorite[cnt].name|h}--></a>
                            </td>
                            <td class="alignR sale_price">
                                <span class="price">
                                    <!--{if $arrFavorite[cnt].price02_min_inctax == $arrFavorite[cnt].price02_max_inctax}-->
                                        <!--{$arrFavorite[cnt].price02_min_inctax|n2s}-->
                                    <!--{else}-->
                                        <!--{$arrFavorite[cnt].price02_min_inctax|n2s}-->～<!--{$arrFavorite[cnt].price02_max_inctax|n2s}-->
                                    <!--{/if}-->円</span>
                            </td>
                        </tr>
                    <!--{/section}-->
                </table>
                <br />

            <!--{else}-->
                <p>お気に入りが登録されておりません。</p>
            <!--{/if}-->
        </form>
    </div>
</div>
