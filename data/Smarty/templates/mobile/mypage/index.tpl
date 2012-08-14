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

<!--{strip}-->
    <!--{$CustomerName1|h}--> <!--{$CustomerName2|h}-->様<br>
    いつもご利用いただきありがとうございます。<br>
    <br>
    <!--★現在のポイント★-->
    <!--{if $smarty.const.USE_POINT !== false}-->
        現在の所持ポイントは「<font color="#ff0000"><!--{$CustomerPoint|number_format|default:"0"|h}-->Pt</font>」です。<br>
        <br>
    <!--{/if}-->

    <hr>
    <a href="change.php" accesskey="1"><!--{1|numeric_emoji}-->登録内容変更</a><br>
    <a href="refusal.php" accesskey="2"><!--{2|numeric_emoji}-->退会</a><br>
    <br>
    <hr>

    ■購入履歴一覧<br>
    <!--{if $objNavi->all_row > 0}-->
        <!--{$objNavi->all_row}-->件の購入履歴があります。<br>
        <br>
        <!--{section name=cnt loop=$arrOrder}-->
            <hr>
            ▽購入日時<br>
            <!--{$arrOrder[cnt].create_date|sfDispDBDate}--><br>
            ▽注文番号<br>
            <!--{$arrOrder[cnt].order_id}--><br>
            <!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}-->
            ▽お支払い方法<br>
            <!--{$arrPayment[$payment_id]|h}--><br>
            ▽合計金額<br>
            <font color="#ff0000"><!--{$arrOrder[cnt].payment_total|number_format}-->円</font><br>
            ▽ご注文状況<br>
            <!--{if $smarty.const.MYPAGE_ORDER_STATUS_DISP_FLAG }-->
                <!--{assign var=order_status_id value="`$arrOrder[cnt].status`"}-->
                <!--{if $order_status_id != $smarty.const.ORDER_PENDING }-->
                    <!--{$arrCustomerOrderStatus[$order_status_id]|h}--><br>
                <!--{else}-->
                    <font color="#ff0000"><!--{$arrCustomerOrderStatus[$order_status_id]|h}--></font><br>
                <!--{/if}-->
            <!--{/if}-->
            
            <div align="right"><a href="./history.php?order_id=<!--{$arrOrder[cnt].order_id}-->">→詳細を見る</a></div><br>
        <!--{/section}-->
        <hr>
    <!--{else}-->
        購入履歴はありません。<br>
    <!--{/if}-->

    <!--{if $objNavi->strnavi != ""}-->
        <!--{$objNavi->strnavi}-->
        <br>
    <!--{/if}-->
<!--{/strip}-->
