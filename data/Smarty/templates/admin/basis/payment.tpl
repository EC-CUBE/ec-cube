<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="payment_id" value="<!--{$tpl_payment_id}-->" />
<div id="basis" class="contents-main">
  <table class="list">
    <tr>
      <th>ID</th>
      <th>支払方法</th>
      <th>手数料（円）</th>
      <th>利用条件</th>
      <th>編集</th>
      <th>削除</th>
      <th>移動</th>
    </tr>
    <!--{section name=cnt loop=$arrPaymentListFree}-->
    <tr>
      <td><!--{$arrPaymentListFree[cnt].payment_id|escape}--></td>
      <td><!--{$arrPaymentListFree[cnt].payment_method|escape}--></td>
      <!--{if $arrPaymentListFree[cnt].charge_flg == 2}-->
        <td align="center">-</td>
      <!--{else}-->
        <td align="right"><!--{$arrPaymentListFree[cnt].charge|escape|number_format}--></td>
      <!--{/if}-->
      <td align="center">
        <!--{if $arrPaymentListFree[cnt].rule > 0}--><!--{$arrPaymentListFree[cnt].rule|escape|number_format}--><!--{else}-->0<!--{/if}-->円
        <!--{if $arrPaymentListFree[cnt].upper_rule > 0}-->～<!--{$arrPaymentListFree[cnt].upper_rule|escape|number_format}-->円<!--{elseif $arrPaymentListFree[cnt].upper_rule == "0"}--><!--{else}-->～無制限<!--{/if}--></td>
      <td align="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="?" onclick="win03('./payment_input.php?mode=pre_edit&amp;payment_id=<!--{$arrPaymentListFree[cnt].payment_id}-->','payment_input','530','400'); return false;">編集</a><!--{else}-->-<!--{/if}--></td>
      <td align="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="?" onclick="fnModeSubmit('delete', 'payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">削除</a><!--{else}-->-<!--{/if}--></td>
      <td align="center">
      <!--{if $smarty.section.cnt.iteration != 1}-->
      <a href="?" onclick="fnModeSubmit('up','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">上へ</a>
      <!--{/if}-->
      <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
      <a href="?" onclick="fnModeSubmit('down','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">下へ</a>
      <!--{/if}-->
      </td>
    </tr>
    <!--{/section}-->
  </table>
  <div class="btn addnew">
    <a class="btn_normal" href="javascript:;" name="subm2" onclick="win03('./payment_input.php','payment_input','550','400');"><span>支払方法を新規入力</span></a>
  </div>
</div>
</form>
