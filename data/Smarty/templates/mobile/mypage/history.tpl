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
    購入日時：<!--{$tpl_arrOrderData.create_date|sfDispDBDate}--><br>
    注文番号：<!--{$tpl_arrOrderData.order_id}--><br>
    お支払い方法：<!--{$arrPayment[$tpl_arrOrderData.payment_id]|h}-->
    <!--{if $tpl_arrOrderData.deliv_time_id != ""}--><br>
        お届け時間：<!--{$arrDelivTime[$tpl_arrOrderData.deliv_time_id]|h}-->
    <!--{/if}-->
    <!--{if $tpl_arrOrderData.deliv_date != ""}--><br>
        お届け日：<!--{$tpl_arrOrderData.deliv_date|h}-->
    <!--{/if}-->

    <form action="order.php" method="post">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <input type="hidden" name="order_id" value="<!--{$tpl_arrOrderData.order_id}-->">
        <input type="submit" name="submit" value="再注文">
    </form>

    ■購入商品詳細<br>
    <!--{foreach from=$tpl_arrOrderDetail item=orderDetail}-->
        <hr>
        商品コード：<!--{$orderDetail.product_code|h}--><br>
        商品名：<a<!--{if $orderDetail.enable}--> href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$orderDetail.product_id|u}-->"<!--{/if}-->><!--{$orderDetail.product_name|h}--></a><br>
        商品種別：
        <!--{if $orderDetail.product_type_id == $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
            <!--{if $orderDetail.is_downloadable}-->
                <!--{if $isAU == false}-->
                    <a target="_self" href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/download.php?order_id=<!--{$tpl_arrOrderData.order_id}-->&amp;product_id=<!--{$orderDetail.product_id}-->&amp;product_class_id=<!--{$orderDetail.product_class_id}-->">ダウンロード</a><br>
                <!--{else}-->
                    <object data="<!--{$smarty.const.ROOT_URLPATH}-->mypage/download.php?order_id=<!--{$tpl_arrOrderData.order_id}-->&amp;product_id=<!--{$orderDetail.product_id}-->&amp;product_class_id=<!--{$orderDetail.product_class_id}-->&amp;<!--{$smarty.const.SID}-->" copyright="no" standby="ダウンロード" type="<!--{$orderDetail.mime_type}-->">
                        <param name="title" value="<!--{$orderDetail.down_filename}-->" valuetype="data">
                    </object><br>
                <!--{/if}-->
            <!--{else}-->
                <!--{if $orderDetail.payment_date == "" && $orderDetail.effective == "0"}-->
                    <!--{$arrProductType[$orderDetail.product_type_id]}--><br>（入金確認中）<br>
                <!--{else}-->
                    <!--{$arrProductType[$orderDetail.product_type_id]}--><br>（期限切れ）<br>
                <!--{/if}-->
            <!--{/if}-->
        <!--{else}-->
            <!--{$arrProductType[$orderDetail.product_type_id]}--><br>
        <!--{/if}-->
        単価：
        <!--{assign var=price value=`$orderDetail.price`}-->
        <!--{assign var=quantity value=`$orderDetail.quantity`}-->
        <!--{$price|sfCalcIncTax|number_format|h}-->円<br>
        数量：<!--{$quantity|h}--><br>
        小計：<!--{$price|sfCalcIncTax|sfMultiply:$quantity|number_format}-->円<br>
    <!--{/foreach}-->
    <hr>
    小計：<!--{$tpl_arrOrderData.subtotal|number_format}-->円<br>
    <!--{assign var=point_discount value="`$tpl_arrOrderData.use_point*$smarty.const.POINT_VALUE`"}-->
    <!--{if $point_discount > 0}-->
        ポイント値引き：<!--{$point_discount|number_format}-->円<br>
    <!--{/if}-->
    <!--{assign var=key value="discount"}-->
    <!--{if $tpl_arrOrderData[$key] != "" && $tpl_arrOrderData[$key] > 0}-->
        値引き：<!--{$tpl_arrOrderData[$key]|number_format}-->円<br>
    <!--{/if}-->
    送料：<!--{assign var=key value="deliv_fee"}--><!--{$tpl_arrOrderData[$key]|number_format|h}-->円<br>
    手数料：
    <!--{assign var=key value="charge"}-->
    <!--{$tpl_arrOrderData[$key]|number_format|h}-->円<br>
    合計：<!--{$tpl_arrOrderData.payment_total|number_format}-->円<br>
    <hr>
    <!-- 使用ポイントここから -->
    <!--{if $smarty.const.USE_POINT !== false}-->
        ■使用ポイント<br>
        ご使用ポイント：<!--{assign var=key value="use_point"}--><!--{$tpl_arrOrderData[$key]|number_format|default:0}--> pt<br>
        今回加算されるポイント：<!--{$tpl_arrOrderData.add_point|number_format|default:0}--> pt<br>
        <hr>
    <!--{/if}-->
    <!-- 使用ポイントここまで -->

    <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
        ▼お届け先<!--{if $isMultiple}--><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--><br>
        <!--{if $isMultiple}-->
            <!--{foreach item=item from=$shippingItem.shipment_item}-->
                商品コード：<!--{$item.productsClass.product_code|h}--><br>
                商品名：<!--{* 商品名 *}--><!--{$item.productsClass.name|h}--><br>
                <!--{if $item.productsClass.classcategory_name1 != ""}-->
                    <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br>
                <!--{/if}-->
                <!--{if $item.productsClass.classcategory_name2 != ""}-->
                    <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--><br>
                <!--{/if}-->
                単価：<!--{$item.price|sfCalcIncTax|number_format}-->円<br>
                数量：<!--{$item.quantity}--><br>
                <br>
            <!--{/foreach}-->
        <!--{/if}-->
        ●お名前<br>
        <!--{$shippingItem.shipping_name01|h}-->&nbsp;<!--{$shippingItem.shipping_name02|h}--><br>
        ●お名前(フリガナ)<br>
        <!--{$shippingItem.shipping_kana01|h}-->&nbsp;<!--{$shippingItem.shipping_kana02|h}--><br>
        ●住所<br>
        〒<!--{$shippingItem.shipping_zip01}-->-<!--{$shippingItem.shipping_zip02}--><br>
        <!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--><br>
        ●電話番号<br>
        <!--{$shippingItem.shipping_tel01}-->-<!--{$shippingItem.shipping_tel02}-->-<!--{$shippingItem.shipping_tel03}--><br>
        <!--{if $shippingItem.shipping_fax01 > 0}-->
            ●FAX番号<br>
            <!--{$shippingItem.shipping_fax01}-->-<!--{$shippingItem.shipping_fax02}-->-<!--{$shippingItem.shipping_fax03}--><br>
        <!--{/if}-->
        <br>
    <!--{/foreach}-->

    <hr>

    ■メール配信履歴一覧<br>
    <!--{section name=cnt loop=$tpl_arrMailHistory}-->
        <!--{assign var=key value="`$tpl_arrMailHistory[cnt].template_id`"}-->
        処理日：<!--{$tpl_arrMailHistory[cnt].send_date|sfDispDBDate|h}--><br>
        通知メール：<!--{$arrMAILTEMPLATE[$key]|h}--><br>
        件名：
        <a href="./mail_view.php?send_id=<!--{$tpl_arrMailHistory[cnt].send_id}-->"><!--{$tpl_arrMailHistory[cnt].subject|h}--></a><br>
    <!--{/section}-->
    <br>
<!--{/strip}-->
