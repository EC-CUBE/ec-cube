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
<!--▼CONTENTS-->
<div id="mypagecolumn">
  <h2 class="title"><!--{$tpl_title|h}--></h2>
  <!--{if $tpl_navi != ""}-->
    <!--{include file=$tpl_navi}-->
  <!--{else}-->
    <!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
  <!--{/if}-->
  <div id="mycontentsarea">
    <form name="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="order_id" value="" />
    <input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />
    <h2><!--{$tpl_subtitle|h}--></h2>

<!--{if $objNavi->all_row > 0}-->

    <p><!--{$objNavi->all_row}-->件の購入履歴があります。</p>
    <div>
    </div>

    <table summary="購入履歴" class="entryform">
      <tr>
        <th class="alignC valignM">購入詳細</th>
        <th class="alignC valignM">合計金額</th>
        <th class="alignC valignM">詳細</th>
      </tr>
      <!--{section name=cnt loop=$arrOrder}-->
      <tr>
       <td class="detailtd">購入日時：<!--{$arrOrder[cnt].create_date|sfDispDBDate}--><br />注文番号：<!--→注文番号--><!--{$arrOrder[cnt].order_id}--><!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}--><!--←注文番号--><br />お支払方法：<!--→支払方法--><!--{$arrPayment[$payment_id]|h}--><!--←支払方法--></td>
       
       <td class="alignR yentd"><!--{$arrOrder[cnt].payment_total|number_format}-->円</td>
       <td class="centertd"><a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/history.php?order_id=<!--{$arrOrder[cnt].order_id}-->">詳細</a></td>
     </tr>
     <!--{/section}-->
    </table>

    <!--{else}-->
    <p>購入履歴はありません。</p>
    <!--{/if}-->
    </form>
  </div>
</div>
