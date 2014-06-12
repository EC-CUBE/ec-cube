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
    <!--{if $tpl_strnavi != "&nbsp;"}-->
        <!--{$tpl_strnavi}-->
        <br><br>
    <!--{/if}-->

    <!--{foreach from=$arrProducts key=i item=arrProduct}-->
        <!-- ▼商品 ここから -->
        <!-- 商品名 --><!--{$arrProduct.name|h}--><br>

        <!--{$smarty.const.SALE_PRICE_TITLE}-->：
        <!--{if $arrProduct.price02_min_inctax == $arrProduct.price02_max_inctax}-->
            <!--{$arrProduct.price02_min_inctax|n2s}-->円
        <!--{else}-->
            <!--{$arrProduct.price02_min_inctax|n2s}-->円～<!--{$arrProduct.price02_max_inctax|n2s}-->円
        <!--{/if}-->
        <br>

        <div align="right">
            <a href="<!--{$smarty.const.MOBILE_P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->">商品詳細へ→</a>
        </div>

        <br>
        <!-- ▲商品 ここまで -->
    <!--{foreachelse}-->
        該当商品がありません。<br>
    <!--{/foreach}-->

    <!--{if $tpl_strnavi != "&nbsp;"}-->
        <!--{$tpl_strnavi}-->
        <br><br>
    <!--{/if}-->
<!--{/strip}-->
