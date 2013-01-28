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

<table id="total-term" class="list">
    <tr>
        <th><!--{t string="tpl_Period_01"}--></th>
        <th><!--{t string="tpl_Number of items purchased_01"}--></th>
        <th><!--{t string="tpl_Male_01"}--></th>
        <th><!--{t string="tpl_Female_01"}--></th>
        <th><!--{t string="tpl_Male (member)_01"}--></th>
        <th><!--{t string="tpl_Male (non-member)_01" escape="none"}--></th>
        <th><!--{t string="tpl_Female (member)_01"}--></th>
        <th><!--{t string="tpl_Female (non-member)_01" escape="none"}--></th>
        <th><!--{t string="tpl_Purchase total_01"}--></th>
        <th><!--{t string="tpl_Purchase average_01"}--></th>
    </tr>

    <!--{section name=cnt loop=$arrResults}-->
        <!--{* 色分け判定 *}-->
        <!--{if !$smarty.section.cnt.last}-->
            <!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
            <!--{if $type == 0}-->
                <!--{* 偶数行 *}-->
                <!--{assign var=color value="even"}-->
            <!--{else}-->
                <!--{* 奇数行 *}-->
                <!--{assign var=color value="odd"}-->
            <!--{/if}-->
        <!--{else}-->
            <!--{* 最終行 *}-->
            <!--{assign var=color value="last"}-->
        <!--{/if}-->

        <tr class="<!--{$color}-->">
            <!--{assign var=wday value="`$arrResults[cnt].wday`"}-->

            <!--{if !$smarty.section.cnt.last}-->
                <td class="center"><!--{*期間*}--><!--{$arrResults[cnt].str_date}--></td>
            <!--{else}-->
                <td class="center"><!--{*期間*}--><!--{t string="tpl_Total_01"}--></td>
            <!--{/if}-->

            <td class="right"><!--{*購入件数*}--><!--{t string="tpl_T_ARG1 item_01" T_ARG1=$arrResults[cnt].total_order|number_format}--></td>
            <td class="right"><!--{*男性*}--><!--{$arrResults[cnt].men|number_format}--></td>
            <td class="right"><!--{*女性*}--><!--{$arrResults[cnt].women|number_format}--></td>
            <td class="right"><!--{*男性(会員)*}--><!--{$arrResults[cnt].men_member|number_format}--></td>
            <td class="right"><!--{*男性(非会員)*}--><!--{$arrResults[cnt].men_nonmember|number_format}--></td>
            <td class="right"><!--{*女性(会員)*}--><!--{$arrResults[cnt].women_member|number_format}--></td>
            <td class="right"><!--{*女性(非会員)*}--><!--{$arrResults[cnt].women_nonmember|number_format}--></td>
            <td class="right"><!--{*購入合計*}--><!--{t string="tpl_&#36; T_ARG1_01" escape="none" T_ARG1=$arrResults[cnt].total|number_format}--></td>
            <td class="right"><!--{*購入平均*}--><!--{t string="tpl_&#36; T_ARG1_01" escape="none" T_ARG1=$arrResults[cnt].total_average|number_format}--></td>
        </tr>
    <!--{/section}-->

    <tr>
        <th><!--{t string="tpl_Period_01"}--></th>
        <th><!--{t string="tpl_Number of items purchased_01"}--></th>
        <th><!--{t string="tpl_Male_01"}--></th>
        <th><!--{t string="tpl_Female_01"}--></th>
        <th><!--{t string="tpl_Male (member)_01"}--></th>
        <th><!--{t string="tpl_Male (non-member)_01" escape="none"}--></th>
        <th><!--{t string="tpl_Female (member)_01"}--></th>
        <th><!--{t string="tpl_Female (non-member)_01" escape="none"}--></th>
        <th><!--{t string="tpl_Purchase total_01"}--></th>
        <th><!--{t string="tpl_Purchase average_01"}--></th>
    </tr>
</table>
