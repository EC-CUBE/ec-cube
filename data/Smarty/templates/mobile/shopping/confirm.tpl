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
    <form method="post" action="<!--{$smarty.const.MOBILE_SHOPPING_CONFIRM_URLPATH}-->">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <input type="hidden" name="mode" value="confirm">
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">

        下記のご注文内容に間違いはございませんか？<br>

        <br>

        【ご注文内容】<br>
        <!--{foreach from=$arrCartItems item=item}-->
            ◎<!--{$item.productsClass.name|h}--><br>
            <!--{if $item.productsClass.classcategory_name1 != ""}--><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br><!--{/if}-->
            <!--{if $item.productsClass.classcategory_name2 != ""}--><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--><br><!--{/if}-->
            &nbsp;単価：<!--{$item.price|sfCalcIncTax|number_format}-->円<br>
            &nbsp;数量：<!--{$item.quantity|number_format}--><br>
            &nbsp;小計：<!--{$item.total_inctax|number_format}-->円<br>
            <br>
        <!--{/foreach}-->

        【購入金額】<br>
        商品合計：<!--{$tpl_total_inctax[$cartKey]|number_format}-->円<br>
        <!--{if $smarty.const.USE_POINT !== false}-->
            <!--{assign var=discount value=`$arrForm.use_point*$smarty.const.POINT_VALUE`}-->
            ポイント値引き：-<!--{$discount|number_format|default:0}-->円<br>
        <!--{/if}-->
        送料：<!--{$arrForm.deliv_fee|number_format}-->円<br>
        <!--{if $arrForm.charge > 0}-->手数料：<!--{$arrForm.charge|number_format}-->円<br><!--{/if}-->
        <font color="#FF0000">合計：<!--{$arrForm.payment_total|number_format}-->円</font><br>
        (内消費税：<!--{$arrForm.tax|number_format}-->円)<br>

        <!--{* ログイン済みの会員のみ *}-->
        <!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
            <br>
            【ポイント確認】<br>
            ご注文前のポイント：<!--{$tpl_user_point|number_format|default:0}-->Pt<br>
            ご使用ポイント：-<!--{$arrForm.use_point|number_format|default:0}-->Pt<br>
            <!--{if $arrForm.birth_point > 0}-->お誕生月ポイント：+<!--{$arrForm.birth_point|number_format|default:0}-->Pt<br><!--{/if}-->
            今回加算予定のポイント：+<!--{$arrForm.add_point|number_format|default:0}-->Pt<br>
            <!--{assign var=total_point value=`$tpl_user_point-$arrForm.use_point+$arrForm.add_point`}-->
            加算後のポイント：<!--{$total_point|number_format}-->Pt<br>

            <br>
        <!--{/if}-->

        <!--{* 販売方法判定（ダウンロード販売のみの場合はお届け先を表示しない） *}-->
        <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
            【お届け先】<br>
            <!--{foreach item=shippingItem from=$arrShipping name=shippingItem}-->
                <!--{if $is_multiple}-->
                    ▼お届け先<!--{$smarty.foreach.shippingItem.iteration}--><br>
                    <!--{* 複数お届け先の場合、お届け先毎の商品を表示 *}-->
                    <!--{foreach item=item from=$shippingItem.shipment_item}-->
                        ◎<!--{$item.productsClass.name|h}--><br>
                        <!--{if $item.productsClass.classcategory_name1 != ""}--><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br><!--{/if}-->
                        <!--{if $item.productsClass.classcategory_name2 != ""}--><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--><br><!--{/if}-->
                        &nbsp;数量：<!--{$item.quantity}--><br>
                        <br>
                    <!--{/foreach}-->
                <!--{/if}-->

                〒<!--{$shippingItem.shipping_zip01|h}-->-<!--{$shippingItem.shipping_zip02|h}--><br>
                <!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--><br>
                <!--{$shippingItem.shipping_name01|h}--> <!--{$shippingItem.shipping_name02|h}--><br>
                <!--{$shippingItem.shipping_tel01}-->-<!--{$shippingItem.shipping_tel02}-->-<!--{$shippingItem.shipping_tel03}--><br>
                <!--{if $shippingItem.shipping_fax01 > 0}-->
                    <!--{$shippingItem.shipping_fax01}-->-<!--{$shippingItem.shipping_fax02}-->-<!--{$shippingItem.shipping_fax03}--><br>
                <!--{/if}-->

                <br>

                お届け日：<!--{$shippingItem.shipping_date|default:"指定なし"|h}--><br>
                お届け時間：<!--{$shippingItem.shipping_time|default:"指定なし"|h}--><br>

                <br>
            <!--{/foreach}-->
        <!--{/if}-->

        【配送方法】<br>
        <!--{$arrDeliv[$arrForm.deliv_id]|h}--><br>

        <br>

        【お支払い方法】<br>
        <!--{$arrForm.payment_method|h}--><br>

        <br>

        <!--{if $arrForm.message != ""}-->
            【その他お問い合わせ】<br>
            <!--{$arrForm.message|h|nl2br}--><br>
            <br>
        <!--{/if}-->
        <!--{if $use_module}-->
            <center><input type="submit" value="次へ"></center>
        <!--{else}-->
            <center><input type="submit" value="注文"></center>
        <!--{/if}-->
    </form>

    <form action="<!--{$smarty.const.MOBILE_SHOPPING_PAYMENT_URLPATH}-->" method="post">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <input type="hidden" name="mode" value="select_deliv">
        <input type="hidden" name="deliv_id" value="<!--{$arrForm.deliv_id|h}-->">
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
        <center><input type="submit" value="戻る"></center>
    </form>
<!--{/strip}-->
