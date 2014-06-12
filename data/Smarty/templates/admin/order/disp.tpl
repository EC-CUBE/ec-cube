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
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
    <!--
    self.moveTo(20,20);self.focus();
    //-->
</script>

<!--▼お客様情報ここから-->
    <table class="form">
        <tr>
            <th>注文番号</th>
            <td><!--{$arrForm.order_id.value|h}--></td>
            <input type="hidden" name="order_id" value="<!--{$arrForm.order_id.value|h}-->" />
        </tr>
        <tr>
            <th>受注日</th>
            <td><!--{$arrForm.create_date.value|sfDispDBDate|h}--></td>
        </tr>
        <tr>
            <th>対応状況</th>
            <td><!--{$arrORDERSTATUS[$arrForm.status.value]|h}--></td>
        </tr>
        <tr>
            <th>入金日</th>
            <td><!--{$arrForm.payment_date.value|sfDispDBDate|default:"未入金"}--></td>
        </tr>
        <tr>
            <th>発送日</th>
            <td><!--{$arrForm.commit_date.value|sfDispDBDate|default:"未発送"}--></td>
        </tr>
    </table>

    <h2>注文者情報</h2>
    <table class="form">
        <tr>
            <th>会員ID</th>
            <td>
                <!--{if $arrForm.customer_id.value > 0}-->
                    <!--{$arrForm.customer_id.value|h}-->
                <!--{else}-->
                    (非会員)
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>お名前</th>
            <td><!--{$arrForm.order_name01.value|h}-->　<!--{$arrForm.order_name02.value|h}--></td>
        </tr>
        <tr>
            <th>お名前(カナ)</th>
            <td><!--{$arrForm.order_kana01.value|h}-->　<!--{$arrForm.order_kana02.value|h}--></td>
        </tr>
        <tr>
            <th>会社名</th>
            <td><!--{$arrForm.order_company_name.value|h}--></td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td><!--{$arrForm.order_email.value|h}--></td>
        </tr>
        <tr>
            <th>TEL</th>
            <td><!--{$arrForm.order_tel01.value|h}--> - <!--{$arrForm.order_tel02.value|h}--> - <!--{$arrForm.order_tel03.value|h}--></td>
        </tr>
        <tr>
            <th>性別</th>
            <td><!--{$arrSex[$arrForm.order_sex.value]|h}--></td>
        </tr>
        <tr>
            <th>職業</th>
            <td><!--{$arrJob[$arrForm.order_job.value]|h}--></td>
        </tr>
        <tr>
            <th>生年月日</th>
            <td>
                <!--{assign var=key1 value="order_birth_year"}-->
                <!--{assign var=key2 value="order_birth_month"}-->
                <!--{assign var=key3 value="order_birth_day"}-->
                <!--{if $arrForm[$key1].value == "" && $arrForm[$key2].value == "" && $arrForm[$key3].value == ""}-->
                    指定無し
                <!--{else}-->
                <!--{$arrForm[$key1].value}-->年
                <!--{$arrForm[$key2].value}-->月
                <!--{$arrForm[$key3].value}-->日
                <!--{/if}-->
            </td>
        </tr>

        <!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
        <tr>
            <th>国</th>
            <td>
                <!--{$arrCountry[$arrForm.order_country_id.value]|h}-->
            </td>
        </tr>
        <tr>
            <th>ZIP CODE</th>
            <td>
                <!--{$arrForm.order_zipcode.value|h}-->
            </td>
        </tr>
        <!--{/if}-->
        <tr>
            <th>住所</th>
            <td>
                〒　<!--{$arrForm.order_zip01.value|h}--> - <!--{$arrForm.order_zip02.value|h}--><br />
                <!--{$arrPref[$arrForm.order_pref.value]|h}--><!--{$arrForm.order_addr01.value|h}--><!--{$arrForm.order_addr02.value|h}-->
            </td>
        </tr>
        <tr>
            <th>備考</th>
            <td><!--{$arrForm.message.value|h|nl2br}--></td>
        </tr>
        <tr>
            <th>現在ポイント</th>
            <td>
                <!--{if $arrForm.customer_id.value > 0}-->
                    <!--{$arrForm.customer_point.value|n2s}-->
                    pt
                <!--{else}-->
                    (非会員)
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>端末種別</th>
            <td><!--{$arrDeviceType[$arrForm.device_type_id.value]|h}--></td>
        </tr>
    </table>
    <!--▲お客様情報ここまで-->

    <!--▼受注商品情報ここから-->
    <h2>受注商品情報</h2>
    <table class="list" id="order-edit-products">
        <tr>
            <th class="id">商品コード</th>
            <th class="name">商品名/規格1/規格2</th>
            <th class="price">単価</th>
            <th class="qty">数量</th>
            <th class="price">税込み価格</th>
            <th class="price">小計</th>
        </tr>
        <!--{section name=cnt loop=$arrForm.quantity.value}-->
        <!--{assign var=product_index value="`$smarty.section.cnt.index`"}-->
        <tr>
            <td class="center">
                <!--{$arrForm.product_code.value[$product_index]|h}-->
            </td>
            <td class="center">
                <!--{$arrForm.product_name.value[$product_index]|h}-->/<!--{$arrForm.classcategory_name1.value[$product_index]|default:"(なし)"|h}-->/<!--{$arrForm.classcategory_name2.value[$product_index]|default:"(なし)"|h}-->
            </td>
            <td class="right">
                    <!--{$arrForm.price.value[$product_index]|n2s|h}-->円
                </td>
            <td class="right">
                <!--{$arrForm.quantity.value[$product_index]|h}-->
            </td>
                <!--{assign var=price value=`$arrForm.price.value[$product_index]`}-->
                <!--{assign var=quantity value=`$arrForm.quantity.value[$product_index]`}-->
                <!--{assign var=tax_rate value=`$arrForm.tax_rate.value[$product_index]`}-->
                <!--{assign var=tax_rule value=`$arrForm.tax_rule.value[$product_index]`}-->
            <td class="right"><!--{$price|sfCalcIncTax:$tax_rate:$tax_rule|n2s}--> 円<br />(税率<!--{$tax_rate|n2s}-->%)</td>
            <td class="right"><!--{$price|sfCalcIncTax:$tax_rate:$tax_rule|sfMultiply:$quantity|n2s}-->円</td>
        </tr>
        <!--{/section}-->
        <tr>
            <th colspan="5" class="column right">小計</th>
            <td class="right"><!--{$arrForm.subtotal.value|n2s}-->円</td>
        </tr>
        <tr>
            <th colspan="5" class="column right">値引き</th>
            <td class="right"><!--{$arrForm.discount.value|n2s|h}-->円</td>
        </tr>
        <tr>
            <th colspan="5" class="column right">送料</th>
            <td class="right"><!--{$arrForm.deliv_fee.value|n2s|h}-->円</td>
        </tr>
        <tr>
            <th colspan="5" class="column right">手数料</th>
            <td class="right"><!--{$arrForm.charge.value|n2s|h}-->円</td>
        </tr>
        <tr>
            <th colspan="5" class="column right">合計</th>
            <td class="right"><!--{$arrForm.total.value|n2s}--> 円</td>
        </tr>
        <tr>
            <th colspan="5" class="column right">お支払い合計</th>
            <td class="right"><!--{$arrForm.payment_total.value|n2s}--> 円</td>
        </tr>
        <!--{if $smarty.const.USE_POINT !== false}-->
            <tr>
                <th colspan="5" class="column right">使用ポイント</th>
                <td class="right"><!--{$arrForm.use_point.value|n2s|default:0|h}-->pt</td>
            </tr>
            <!--{if $arrForm.birth_point.value > 0}-->
                <tr>
                    <th colspan="5" class="column right">お誕生日ポイント</th>
                    <td class="right"><!--{$arrForm.birth_point.value|n2s}-->pt</td>
                </tr>
            <!--{/if}-->
            <tr>
                <th colspan="5" class="column right">加算ポイント</th>
                <td class="right"><!--{$arrForm.add_point.value|n2s|default:0}-->pt</td>
            </tr>
        <!--{/if}-->
    </table>
    <!--▼お届け先情報ここから-->
    <h2>お届け先情報</h2>
    <!--{if $arrForm.product_type_id.value[0] != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
    <!--{foreach name=shipping from=$arrAllShipping item=arrShipping key=shipping_index}-->
        <!--{if $tpl_shipping_quantity > 1}-->
            <h3>お届け先<!--{$smarty.foreach.shipping.iteration}--></h3>
        <!--{/if}-->
        <!--{assign var=key value="shipping_id"}-->
        <!--{if $tpl_shipping_quantity > 1}-->

            <!--{if count($arrShipping.shipment_product_class_id) > 0}-->
                <table class="list" id="order-edit-products">
                    <tr>
                        <th class="id">商品コード</th>
                        <th class="name">商品名/規格1/規格2</th>
                        <th class="price">単価</th>
                        <th class="qty">数量</th>
                    </tr>
                    <!--{section name=item loop=$arrShipping.shipment_product_class_id|@count}-->
                        <!--{assign var=item_index value="`$smarty.section.item.index`"}-->
                        <tr>
                            <td class="center">
                                <!--{assign var=key value="shipment_product_code"}-->
                                <!--{$arrShipping[$key][$item_index]|h}-->
                            </td>
                            <td class="center">
                                <!--{assign var=key1 value="shipment_product_name"}-->
                                <!--{assign var=key2 value="shipment_classcategory_name1"}-->
                                <!--{assign var=key3 value="shipment_classcategory_name2"}-->
                                <!--{$arrShipping[$key1][$item_index]|h}-->/<!--{$arrShipping[$key2][$item_index]|default:"(なし)"|h}-->/<!--{$arrShipping[$key3][$item_index]|default:"(なし)"|h}-->
                            </td>
                            <td class="right">
                                <!--{assign var=key value="shipment_price"}-->
                                <!--{$arrShipping[$key][$item_index]|sfCalcIncTax:$arrForm.order_tax_rate.value:$arrForm.order_tax_rule.value|n2s}-->円
                            </td>
                            <td class="right">
                                <!--{assign var=key value="shipment_quantity"}-->
                                <!--{$arrShipping[$key][$item_index]|h}-->
                            </td>
                        </tr>
                    <!--{/section}-->
                </table>
            <!--{/if}-->
        <!--{/if}-->

        <table class="form">
            <tr>
                <th>お名前</th>
                <td>
                    <!--{assign var=key1 value="shipping_name01"}-->
                    <!--{assign var=key2 value="shipping_name02"}-->
                    <!--{$arrShipping[$key1]|h}-->　<!--{$arrShipping[$key2]|h}-->
                </td>
            </tr>
            <tr>
                <th>お名前(カナ)</th>
                <td>
                    <!--{assign var=key1 value="shipping_kana01"}-->
                    <!--{assign var=key2 value="shipping_kana02"}-->
                    <!--{$arrShipping[$key1]|h}-->　<!--{$arrShipping[$key2]|h}-->
                </td>
            </tr>
            <tr>
                <th>会社名</th>
                <!--{assign var=key1 value="shipping_company_name"}-->
                <td><!--{$arrShipping[$key1]|h}--></td>
            </tr>
            <tr>
                <th>TEL</th>
                <td>
                    <!--{assign var=key1 value="shipping_tel01"}-->
                    <!--{assign var=key2 value="shipping_tel02"}-->
                    <!--{assign var=key3 value="shipping_tel03"}-->
                    <!--{$arrShipping[$key1]|h}--> -
                    <!--{$arrShipping[$key2]|h}--> -
                    <!--{$arrShipping[$key3]|h}-->
                </td>
            </tr>
            <!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
            <tr>
                <th>国</th>
                <td>
                    <!--{assign var=key1 value="shipping_country_id"}-->
                    <!--{assign var=key2 value=$arrShipping[$key1]}-->
                    <!--{$arrCountry[$key2]|h}-->
                </td>
            </tr>
            <tr>
                <th>ZIP CODE</th>
                <td>
                    <!--{assign var=key1 value="shipping_zipcode"}-->
                    <!--{$arrShipping[$key1]|h}-->
                </td>
            </tr>
            <!--{/if}-->
            <tr>
                <th>住所</th>
                <td>
                    <!--{assign var=key1 value="shipping_zip01"}-->
                    <!--{assign var=key2 value="shipping_zip02"}-->
                    〒
                    <!--{$arrShipping[$key1]|h}-->
                    -
                    <!--{$arrShipping[$key2]|h}-->
                    <br />
                    <!--{$arrPref[$arrShipping.shipping_pref]|h}-->
                    <!--{assign var=key value="shipping_addr01"}-->
                    <!--{$arrShipping[$key]|h}-->
                    <!--{assign var=key value="shipping_addr02"}-->
                    <!--{$arrShipping[$key]|h}-->
                </td>
            </tr>
            <tr>
                <th>お届け時間</th>
                <td>
                    <!--{$arrDelivTime[$arrShipping.time_id]|default:"指定無し"}-->
                </td>
            </tr>
            <tr>
                <th>お届け日</th>
                <td>
                    <!--{assign var=key1 value="shipping_date_year"}-->
                    <!--{assign var=key2 value="shipping_date_month"}-->
                    <!--{assign var=key3 value="shipping_date_day"}-->
                    <!--{if $arrShipping[$key1] == "" && $arrShipping[$key2] == "" && $arrShipping[$key3] == ""}-->
                        指定無し
                    <!--{else}-->
                    <!--{$arrShipping[$key1]}-->年
                    <!--{$arrShipping[$key2]}-->月
                    <!--{$arrShipping[$key3]}-->日
                    <!--{/if}-->
                </td>
            </tr>

        </table>
    <!--{/foreach}-->
    <!--{/if}-->
    <!--▲お届け先情報ここまで-->

        <a name="deliv"></a>
        <table class="form">
            <tr>
                <th>配送業者</th>
                <td>
                    <!--{$arrDeliv[$arrForm.deliv_id.value]|h}-->
                </td>
            </tr>
            <tr>
                <th>お支払方法</th>
                <td>
                    <!--{$arrPayment[$arrForm.payment_id.value]|h}-->
                </td>
            </tr>

            <!--{if $arrForm.payment_info|@count > 0}-->
            <tr>
                <th><!--{$arrForm.payment_type}-->情報</th>
                <td>
                    <!--{foreach key=key item=item from=$arrForm.payment_info}-->
                    <!--{if $key != "title"}--><!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value}--><br/><!--{/if}-->
                    <!--{/foreach}-->
                </td>
            </tr>
            <!--{/if}-->

            <tr>
                <th>メモ</th>
                <td>
                    <!--{$arrForm.note.value|h|nl2br}-->
                </td>
            </tr>
        </table>

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="window.close(); return false;"><span class="btn-next">閉じる</span></a></li>
            </ul>
        </div>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
