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

<!--{strip}-->
    <!--★商品画像★-->
    <!--{if $smarty.get.image != ''}-->
        <!--{assign var=key value="`$smarty.get.image`"}-->
    <!--{else}-->
        <!--{assign var=key value="main_image"}-->
    <!--{/if}-->
    <center><img src="<!--{$arrFile[$key].filepath}-->"></center>
    <br>

    <!--★商品サブ画像★-->
    <!--{if $subImageFlag == true}-->
        <center>
            画像
            <!--{if ($smarty.get.image == "" || $smarty.get.image == "main_image")}-->
                [1]
            <!--{else}-->
                [<a href="?product_id=<!--{$smarty.get.product_id}-->&amp;image=main_image">1</a>]
            <!--{/if}-->

            <!--{assign var=num value="2"}-->
            <!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
                <!--{assign var=key value="sub_image`$smarty.section.cnt.iteration`"}-->
                <!--{if $arrFile[$key].filepath != ""}-->
                    <!--{if $key == $smarty.get.image}-->
                        [<!--{$num}-->]
                    <!--{else}-->
                        [<a href="?product_id=<!--{$smarty.get.product_id}-->&amp;image=<!--{$key}-->"><!--{$num}--></a>]
                    <!--{/if}-->
                    <!--{assign var=num value="`$num+1`"}-->
                <!--{/if}-->
            <!--{/section}-->
        </center>
        <br>
    <!--{/if}-->

    <!--★詳細メインコメント★-->
    [emoji:76]<!--{$arrProduct.main_comment|nl2br_html}--><br>
    <br>

    <!--▼商品ステータス-->
    <!--{assign var=ps value=$productStatus[$tpl_product_id]}-->
    <!--{if count($ps) > 0}-->
        <!--{foreach from=$ps item=status}-->
            ★<!--{$arrSTATUS[$status]}--><br>
        <!--{/foreach}-->
        <br>
    <!--{/if}-->
    <!--▲商品ステータス-->

    <!--★商品コード★-->
    商品コード：
    <!--{if $arrProduct.product_code_min == $arrProduct.product_code_max}-->
        <!--{$arrProduct.product_code_min|h}-->
    <!--{else}-->
        <!--{$arrProduct.product_code_min|h}-->～<!--{$arrProduct.product_code_max|h}-->
    <!--{/if}-->
    <br>

    <!--★販売価格★-->
    <font color="#FF0000"><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)：
        <!--{if $arrProduct.price02_min_inctax == $arrProduct.price02_max_inctax}-->
            <!--{$arrProduct.price02_min_inctax|n2s}-->
        <!--{else}-->
            <!--{$arrProduct.price02_min_inctax|n2s}-->～<!--{$arrProduct.price02_max_inctax|n2s}-->
        <!--{/if}-->
        円</font>
    <br>

    <!--{if $arrProduct.price01_max_inctax > 0}-->
        <!--★通常価格★-->
        <font color="#FF0000"><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(税込)：
            <!--{if $arrProduct.price01_min_inctax == $arrProduct.price01_max_inctax}-->
                <!--{$arrProduct.price01_min_inctax|n2s}-->
            <!--{else}-->
                <!--{$arrProduct.price01_min_inctax|n2s}-->～<!--{$arrProduct.price01_max_inctax|n2s}-->
            <!--{/if}-->
            円</font>
        <br>
    <!--{/if}-->

    <!--★ポイント★-->
    <!--{if $smarty.const.USE_POINT !== false}-->
        ポイント：
        <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
            <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate|n2s}-->
        <!--{else}-->
            <!--{if $arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate == $arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate}-->
                <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate|n2s}-->
            <!--{else}-->
                <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate|n2s}-->～<!--{$arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate|n2s}-->
            <!--{/if}-->
        <!--{/if}-->
        Pt<br>
    <!--{/if}-->
    <br>

    <!--★メーカー★-->
    <!--{if $arrProduct.maker_name|strlen >= 1}-->
        メーカー：<!--{$arrProduct.maker_name|h}--><br>
    <!--{/if}-->

    <!--★メーカーURL★-->
    <!--{if $arrProduct.comment1|strlen >= 1}-->
        メーカーURL：<a href="<!--{$arrProduct.comment1|h}-->"><!--{$arrProduct.comment1|h}--></a><br>
    <!--{/if}-->

    <!--★関連カテゴリ★-->
    関連カテゴリ：<br>
    <!--{section name=r loop=$arrRelativeCat}-->
        <!--{section name=s loop=$arrRelativeCat[r]}-->
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php?category_id=<!--{$arrRelativeCat[r][s].category_id}-->"><!--{$arrRelativeCat[r][s].category_name}--></a>
            <!--{if !$smarty.section.s.last}--><!--{$smarty.const.SEPA_CATNAVI}--><!--{/if}-->
        <!--{/section}-->
        <br>
    <!--{/section}-->
    <br>

    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="mode" value="select">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">

        <input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
        <!--{if $tpl_stock_find}-->
            <!--★商品を選ぶ★-->
            <center><input type="submit" name="select" id="cart" value="この商品を選ぶ"></center>
        <!--{else}-->
            <font color="#FF0000">申し訳ございませんが､只今品切れ中です｡</font>
        <!--{/if}-->
    </form>
<!--{/strip}-->
