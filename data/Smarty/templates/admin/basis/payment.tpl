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

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="payment_id" value="<!--{$tpl_payment_id}-->" />
<div id="basis" class="contents-main">
    <div class="btn">
        <ul>
            <li><a class="btn-action" href="javascript:;" name="subm2" onclick="fnChangeAction('./payment_input.php'); fnModeSubmit('','',''); return false;">
                <span class="btn-next"><!--{t string="tpl_Add payment method_01"}--></span></a></li>
        </ul>
    </div>
    <table class="list">
        <col width="5%" />
        <col width="30%" />
        <col width="20%" />
        <col width="20%" />
        <col width="5%" />
        <col width="5%" />
        <col width="15%" />
        <tr>
            <th class="center"><!--{t string="tpl_ID_01"}--></th>
            <th><!--{t string="tpl_Payment method_01"}--></th>
            <th><!--{t string="tpl_Processing fee (&#36;)_01" escape="none"}--></th>
            <th><!--{t string="tpl_Usage conditions_01"}--></th>
            <th><!--{t string="tpl_Edit_01"}--></th>
            <th><!--{t string="tpl_Remove_01"}--></th>
            <th><!--{t string="tpl_Move_01"}--></th>
        </tr>
        <!--{section name=cnt loop=$arrPaymentListFree}-->
        <tr>
            <td class="center"><!--{$arrPaymentListFree[cnt].payment_id|h}--></td>
            <td class="center"><!--{$arrPaymentListFree[cnt].payment_method|h}--></td>
            <!--{if $arrPaymentListFree[cnt].charge_flg == 2}-->
                <td class="center">-</td>
            <!--{else}-->
                <td class="right"><!--{$arrPaymentListFree[cnt].charge|number_format|h}--></td>
            <!--{/if}-->
            <td class="center">
                <!--{t string="currency_prefix"}--><!--{if $arrPaymentListFree[cnt].rule_max > 0}--><!--{$arrPaymentListFree[cnt].rule_max|number_format|h}--><!--{else}-->0<!--{/if}--><!--{t string="currency_suffix"}-->
                <!--{if $arrPaymentListFree[cnt].upper_rule > 0}--><!--{t string="-"}--><!--{t string="currency_prefix"}--><!--{$arrPaymentListFree[cnt].upper_rule|number_format|h}--><!--{t string="currency_suffix"}--><!--{elseif $arrPaymentListFree[cnt].upper_rule == "0"}--><!--{else}--><!--{t string="-"}--><!--{t string="tpl_No limit_01"}--><!--{/if}--></td>
            <td class="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="?" onclick="fnChangeAction('./payment_input.php'); fnModeSubmit('pre_edit', 'payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;"><!--{t string="tpl_Edit_01"}--></a><!--{else}-->-<!--{/if}--></td>
            <td class="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="?" onclick="fnModeSubmit('delete', 'payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;"><!--{t string="tpl_Remove_01"}--></a><!--{else}-->-<!--{/if}--></td>
            <td class="center">
            <!--{if $smarty.section.cnt.iteration != 1}-->
            <a href="?" onclick="fnModeSubmit('up','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;"><!--{t string="tpl_To top_01"}--></a>
            <!--{/if}-->
            <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
            <a href="?" onclick="fnModeSubmit('down','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;"><!--{t string="tpl_To bottom_01"}--></a>
            <!--{/if}-->
            </td>
        </tr>
        <!--{/section}-->
    </table>
</div>
</form>
