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

<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="edit" />
    <input type="hidden" name="payment_id" value="<!--{$tpl_payment_id}-->" />
    <div id="basis" class="contents-main">
        <div class="btn">
            <ul>
                <li><a class="btn-action" href="javascript:;" name="subm2" onclick="eccube.changeAction('./payment_input.php'); eccube.setModeAndSubmit('','',''); return false;">
                    <span class="btn-next">支払方法を新規入力</span></a></li>
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
                <th class="center">ID</th>
                <th>支払方法</th>
                <th>手数料（円）</th>
                <th>利用条件</th>
                <th>編集</th>
                <th>削除</th>
                <th>移動</th>
            </tr>
            <!--{section name=cnt loop=$arrPaymentListFree}-->
            <tr>
                <td class="center"><!--{$arrPaymentListFree[cnt].payment_id|h}--></td>
                <td class="center"><!--{$arrPaymentListFree[cnt].payment_method|h}--></td>
                <!--{if $arrPaymentListFree[cnt].charge_flg == 2}-->
                    <td class="center">-</td>
                <!--{else}-->
                    <td class="right"><!--{$arrPaymentListFree[cnt].charge|n2s|h}--></td>
                <!--{/if}-->
                <td class="center">
                    <!--{if $arrPaymentListFree[cnt].rule_max > 0}--><!--{$arrPaymentListFree[cnt].rule_max|n2s|h}--><!--{else}-->0<!--{/if}-->円
                    <!--{if $arrPaymentListFree[cnt].upper_rule > 0}-->～<!--{$arrPaymentListFree[cnt].upper_rule|n2s|h}-->円<!--{elseif $arrPaymentListFree[cnt].upper_rule == "0"}--><!--{else}-->～無制限<!--{/if}--></td>
                <td class="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="?" onclick="eccube.changeAction('./payment_input.php'); eccube.setModeAndSubmit('pre_edit', 'payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">編集</a><!--{else}-->-<!--{/if}--></td>
                <td class="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="?" onclick="eccube.setModeAndSubmit('delete', 'payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">削除</a><!--{else}-->-<!--{/if}--></td>
                <td class="center">
                <!--{if $smarty.section.cnt.iteration != 1}-->
                <a href="?" onclick="eccube.setModeAndSubmit('up','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">上へ</a>
                <!--{/if}-->
                <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                <a href="?" onclick="eccube.setModeAndSubmit('down','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">下へ</a>
                <!--{/if}-->
                </td>
            </tr>
            <!--{/section}-->
        </table>
    </div>
</form>
