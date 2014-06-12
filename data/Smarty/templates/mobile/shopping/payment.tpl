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
    <form method="post" action="<!--{$smarty.const.MOBILE_SHOPPING_PAYMENT_URLPATH}-->">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <input type="hidden" name="mode" value="confirm">
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
        <!--{assign var=key value="deliv_id"}-->
        <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->">
        ■お支払方法 <font color="#FF0000">*</font><br>
        <!--{assign var=key value="payment_id"}-->
        <!--{if $arrErr[$key] != ""}-->
            <font color="#FF0000"><!--{$arrErr[$key]}--></font>
        <!--{/if}-->
        <!--{section name=cnt loop=$arrPayment}-->
            <input type="radio" name="<!--{$key}-->" value="<!--{$arrPayment[cnt].payment_id}-->" <!--{$arrPayment[cnt].payment_id|sfGetChecked:$arrForm[$key].value}-->>
            <!--{$arrPayment[cnt].payment_method|h}-->
            <br>
        <!--{/section}-->
        <br>

        <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
            ■お届け時間の指定<br>
            <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
                <!--{assign var=index value=$shippingItem.shipping_id}-->

                <!--{if $is_multiple}-->
                    ▼<!--{$shippingItem.shipping_name01}--><!--{$shippingItem.shipping_name02}-->
                    <!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01}--><!--{$shippingItem.shipping_addr02}--><br>
                <!--{/if}-->

                <!--★お届け日★-->
                <!--{assign var=key value="deliv_date`$index`"}-->
                <font color="#FF0000"><!--{$arrErr[$key]}--></font>
                お届け日：<br>
                <!--{if !$arrDelivDate}-->
                    ご指定頂けません。
                <!--{else}-->
                    <select name="<!--{$key}-->">
                        <option value="" selected="">指定なし</option>
                        <!--{assign var=shipping_date_value value=$arrForm[$key].value|default:$shippingItem.shipping_date}-->
                        <!--{html_options options=$arrDelivDate selected=$shipping_date_value}-->
                    </select>
                <!--{/if}-->
                <br>
                <!--★お届け時間★-->
                <!--{assign var=key value="deliv_time_id`$index`"}-->
                <font color="#FF0000"><!--{$arrErr[$key]}--></font>
                お届け時間：<br>
                <select name="<!--{$key}-->" id="<!--{$key}-->">
                    <option value="" selected="">指定なし</option>
                    <!--{assign var=shipping_time_value value=$arrForm[$key].value|default:$shippingItem.time_id}-->
                    <!--{html_options options=$arrDelivTime selected=$shipping_time_value}-->
                </select>
                <br>
                <br>
            <!--{/foreach}-->
        <!--{/if}-->

        ■その他お問い合わせ<br>
        <!--{assign var=key value="message"}-->
        <!--{if $arrErr[$key] != ""}-->
            <font color="#FF0000"><!--{$arrErr[$key]}--></font>
        <!--{/if}-->
        <textarea cols="20" rows="2" name="<!--{$key}-->"><!--{"\n"}--><!--{$arrForm[$key].value|h}--></textarea>
        <br>
        <br>

        <!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
            ■ポイント使用の指定<br>
            1ポイントを<!--{$smarty.const.POINT_VALUE}-->円として使用する事ができます。<br>
            <br>
            <!--{$name01|h}--> <!--{$name02|h}-->様の、現在の所持ポイントは「<font color="#FF0000"><!--{$tpl_user_point|n2s|default:0}-->Pt</font>」です。<br>
            <br>
            今回ご購入合計金額：<font color="#FF0000"><!--{$arrPrices.subtotal|n2s}-->円</font><br>
            (送料、手数料を含みません。)<br>
            <br>
            <input type="radio" name="point_check" value="1" <!--{$arrForm.point_check.value|sfGetChecked:1}-->>ポイントを使用する<br>
            <!--{assign var=key value="use_point"}-->
            <!--{if $arrErr[$key] != ""}-->
                <font color="#FF0000"><!--{$arrErr[$key]}--></font>
            <!--{/if}-->
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:$tpl_user_point}-->" maxlength="<!--{$arrForm[$key].length}-->" size="6">&nbsp;ポイントを使用する。<br>
            <input type="radio" name="point_check" value="2" <!--{$arrForm.point_check.value|sfGetChecked:2}-->>ポイントを使用しない<br>
            <br>
        <!--{/if}-->

        <center><input type="submit" value="次へ"></center>
    </form>

    <form action="?mode=return" method="get">
        <input type="hidden" name="mode" value="return">
        <center><input type="submit" value="戻る"></center>
    </form>
<!--{/strip}-->
