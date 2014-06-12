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

<table id="total-age" class="list">
    <tr>
        <th>年齢</th>
        <th>購入件数</th>
        <th>購入合計</th>
        <th>購入平均</th>
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
            <td class="center"><!--{*年齢*}--><!--{$arrResults[cnt].age_name}--></td>
            <td class="right"><!--{*購入件数*}--><!--{$arrResults[cnt].order_count}-->件</td>
            <td class="right"><!--{*購入合計*}--><!--{$arrResults[cnt].total|n2s}-->円</td>
            <td class="right"><!--{*購入平均*}--><!--{$arrResults[cnt].total_average|n2s}-->円</td>
        </tr>
    <!--{/section}-->

    <tr>
        <th>年齢</th>
        <th>購入件数</th>
        <th>購入合計</th>
        <th>購入平均</th>
    </tr>
</table>
