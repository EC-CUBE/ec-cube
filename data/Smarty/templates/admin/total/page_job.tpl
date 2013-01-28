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

<table id="total-job" class="list">
    <tr>
        <th><!--{t string="tpl_Ranking_01"}--></th>
        <th><!--{t string="tpl_Occupation_02"}--></th>
        <th><!--{t string="tpl_Number of items purchased_01"}--></th>
        <th><!--{t string="tpl_Purchase total_01"}--></th>
        <th><!--{t string="tpl_Purchase average_01"}--></th>
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
            <td class="center"><!--{*職業*}--><!--{$arrResults[cnt].job_name}--></td>
            <td class="right"><!--{*購入件数*}--><!--{t string="tpl_T_ARG1 item_01" T_ARG1=$arrResults[cnt].order_count}--></td>
            <td class="right"><!--{*購入合計*}--><!--{t string="tpl_&#36; T_ARG1_01" escape="none" T_ARG1=$arrResults[cnt].total|number_format}--></td>
            <td class="right"><!--{*購入平均*}--><!--{t string="tpl_&#36; T_ARG1_01" escape="none" T_ARG1=$arrResults[cnt].total_average|number_format}--></td>
        </tr>
    <!--{/section}-->

    <tr>
        <th><!--{t string="tpl_Ranking_01"}--></span></th>
        <th><!--{t string="tpl_Occupation_02"}--></span></th>
        <th><!--{t string="tpl_Number of items purchased_01"}--></span></th>
        <th><!--{t string="tpl_Purchase total_01"}--></span></th>
        <th><!--{t string="tpl_Purchase average_01"}--></span></th>
    </tr>
</table>
