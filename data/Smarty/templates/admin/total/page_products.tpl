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

<table id="total-products" class="list">
    <tr>
        <th>順位</th>
        <th>商品コード</th>
        <th>商品名</th>
        <th>購入件数</th>
        <th>数量</th>
        <th>単価</th>
        <th>金額</th>
    </tr>

    <!--{section name=cnt loop=$arrResults}-->
        <!--{* 色分け判定 *}-->
        <!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
        <!--{if $type == 0}-->
            <!--{* 偶数行 *}-->
            <!--{assign var=color value="even"}-->
        <!--{else}-->
            <!--{* 奇数行 *}-->
            <!--{assign var=color value="odd"}-->
        <!--{/if}-->

        <tr class="<!--{$color}-->">
            <td class="center"><!--{*順位*}--><!--{$smarty.section.cnt.iteration}--></td>
            <td class="right"><!--{*商品コード*}--><!--{$arrResults[cnt].product_code|h}--></td>
            <td class="left"><!--{*商品名*}--><!--{$arrResults[cnt].product_name|sfCutString:40:false|h}--></td>
            <td class="right"><!--{*購入件数*}--><!--{$arrResults[cnt].order_count}-->件</td>
            <td class="right"><!--{*数量*}--><!--{$arrResults[cnt].products_count}--></td>
            <td class="right"><!--{*単価*}--><!--{$arrResults[cnt].price|number_format}-->円</td>
            <td class="right"><!--{*金額*}--><!--{$arrResults[cnt].total|number_format}-->円</td>
        </tr>
    <!--{/section}-->

    <tr>
        <th>順位</th>
        <th>商品コード</th>
        <th>商品名</th>
        <th>購入件数</th>
        <th>数量</th>
        <th>単価</th>
        <th>金額</th>
    </tr>
</table>
