<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
    <h2>受注管理</h2>
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
        
    <h2>お客様情報</h2>
        <table class="form">
            <tr>
                <th>顧客ID</th>
                <!--{if $arrForm.customer_id.value > 0}-->
                    <td><!--{$arrForm.customer_id.value|h}-->
                <!--{else}-->
                    (非会員)
                <!--{/if}-->
                    </td>
            </tr>
            <tr>
                <th>顧客名</th>
                <td><!--{$arrForm.order_name01.value|h}-->　<!--{$arrForm.order_name02.value|h}--></td>
            </tr>
            <tr>
                <th>顧客名(カナ)</th>
                <td><!--{$arrForm.order_kana01.value|h}-->　<!--{$arrForm.order_kana02.value|h}--></td>
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
                    <!--{if $arrForm.customer_id >0}-->
                        <!--{$arrForm.customer_point.value|number_format}-->
                        pt
                    <!--{else}-->
                        (非会員)
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>アクセス端末</th>
                <td><!--{$arrDeviceType[$arrForm.device_type_id.value]|h}--></td>
            </tr>
        </table>
        
    <h2>受注商品情報</h2>
        <table class="list">
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
                <td>
                    <!--{$arrForm.product_code.value[$product_index]|h}-->
                </td>
                <td>
                    <!--{$arrForm.product_name.value[$product_index]|h}-->/<!--{$arrForm.classcategory_name1.value[$product_index]|default:"(なし)"|h}-->/<!--{$arrForm.classcategory_name2.value[$product_index]|default:"(なし)"|h}-->
                </td>
                <td align="center">
                    <!--{$arrForm.price.value[$product_index]|h}-->円
                </td>
                <td align="center">
                    <!--{$arrForm.quantity.value[$product_index]|h}-->
                </td>
                <!--{assign var=price value=`$arrForm.price.value[$product_index]`}-->
                <!--{assign var=quantity value=`$arrForm.quantity.value[$product_index]`}-->
                <td class="right"><!--{$price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}--> 円</td>
                <td class="right"><!--{$price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|sfMultiply:$quantity|number_format}-->円</td>
            </tr>
            <!--{/section}-->
            <tr>
                <th colspan="5" class="column right">小計</th>
                <td class="right"><!--{$arrForm.subtotal.value|number_format}-->円</td>
            </tr>
            <tr>
                <th colspan="5" class="column right">値引</th>
                <td class="right"><!--{$arrForm.discount.value|h}-->円</td>
            </tr>
            <tr>
                <th colspan="5" class="column right">送料</th>
                <td class="right"><!--{$arrForm.deliv_fee.value|h}-->円</td>
            </tr>
            <tr>
                <th colspan="5" class="column right">手数料</th>
                <td class="right"><!--{$arrForm.charge.value|h}-->円</td>
            </tr>
            <tr>
                <th colspan="5" class="column right">合計</th>
                <td class="right"><!--{$arrForm.total.value|number_format}--> 円</td>
            </tr>
            <tr>
                <th colspan="5" class="column right">お支払い合計</th>
                <td class="right"><!--{$arrForm.payment_total.value|number_format}--> 円</td>
            </tr>
            <!--{if $smarty.const.USE_POINT !== false}-->
                <tr>
                    <th colspan="5" class="column right">使用ポイント</th>
                    <td class="right"><!--{$arrForm.use_point.value|default:0|h}-->pt</td>
                </tr>
                <!--{if $arrForm.birth_point.value > 0}-->
                <tr>
                    <th colspan="5" class="column right">お誕生日ポイント</th>
                    <td class="right"><!--{$arrForm.birth_point.value|number_format}-->pt</td>
                </tr>
                <!--{/if}-->
                <tr>
                    <th colspan="5" class="column right">加算ポイント</th>
                    <td class="right"><!--{$arrForm.add_point.value|number_format|default:0}-->pt</td>
                </tr>
            <!--{/if}-->
        </table>
        
    <h2>お届け先情報</h2>
        <!--{section name=shipping loop=$arrForm.shipping_quantity.value}-->
        <!--{assign var=shipping_index value="`$smarty.section.shipping.index`"}-->

        <!--{if $arrForm.shipping_quantity.value > 1}-->
            <h3>お届け先<!--{$smarty.section.shipping.iteration}--></h3>
        <!--{/if}-->
        <!--{assign var=key value="shipping_id"}-->
        <!--{if $arrForm.shipping_quantity.value > 1}-->
            <!--{assign var=product_quantity value="shipping_product_quantity"}-->

            <!--{if $arrForm[$product_quantity].value[$shipping_index] > 0}-->
                <table class="list" id="order-edit-products">
                    <tr>
                        <th class="id">商品コード</th>
                        <th class="name">商品名/規格1/規格2</th>
                        <th class="price">単価</th>
                        <th class="qty">数量</th>
                    </tr>
                    <!--{section name=item loop=$arrForm[$product_quantity].value[$shipping_index]}-->
                        <!--{assign var=item_index value="`$smarty.section.item.index`"}-->

                        <tr>
                            <td>
                                <!--{assign var=key value="shipment_product_class_id"}-->
                                <!--{assign var=key value="shipment_product_code"}-->
                                <!--{$arrForm[$key].value[$shipping_index][$item_index]|h}-->
                            </td>
                            <td>
                                <!--{assign var=key1 value="shipment_product_name"}-->
                                <!--{assign var=key2 value="shipment_classcategory_name1"}-->
                                <!--{assign var=key3 value="shipment_classcategory_name2"}-->
                                <!--{$arrForm[$key1].value[$shipping_index][$item_index]|h}-->/<!--{$arrForm[$key2].value[$shipping_index][$item_index]|default:"(なし)"|h}-->/<!--{$arrForm[$key3].value[$shipping_index][$item_index]|default:"(なし)"|h}-->
                            </td>
                            <td class="right">
                                <!--{assign var=key value="shipment_price"}-->
                                <!--{$arrForm[$key].value[$shipping_index][$item_index]|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                            </td>
                            <td class="right">
                                <!--{assign var=key value="shipment_quantity"}-->
                                <!--{$arrForm[$key].value[$shipping_index][$item_index]|h}-->
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
                    <!--{$arrForm.shipping_name01.value[$shipping_index]|h}-->　<!--{$arrForm.shipping_name02.value[$shipping_index]|h}-->
                </td>
            </tr>
            <tr>
                <th>お名前(カナ)</th>
                <td>
                    <!--{$arrForm.shipping_kana01.value[$shipping_index]|h}-->　<!--{$arrForm.shipping_kana02.value[$shipping_index]|h}-->
                </td>
            </tr>
            <tr>
                <th>TEL</th>
                <td>
                    <!--{$arrForm.shipping_tel01.value[$shipping_index]|h}--> - <!--{$arrForm.shipping_tel02.value[$shipping_index]|h}--> - <!--{$arrForm.shipping_tel03.value[$shipping_index]|h}-->
                </td>
            </tr>
            <tr>
                <th>住所</th>
                <td>
                    〒　<!--{$arrForm.shipping_zip01.value[$shipping_index]|h}--> - <!--{$arrForm.shipping_zip02.value[$shipping_index]|h}--><br />
                    <!--{$arrPref[$arrForm.order_pref.value]|h}--><!--{$arrForm.shipping_addr01.value[$shipping_index]|h}--><!--{$arrForm.shipping_addr02.value[$shipping_index]|h}-->
                </td>
            </tr>
            <tr>
                <th>お届け時間</th>
                <td>
                    <!--{assign var=deliv_time_id value="`$arrForm.time_id.value[$shipping_index]`"}-->
                    <!--{$arrDelivTime[$deliv_time_id]|default:"指定無し"}-->
                </td>
            </tr>
            <tr>
                <th>お届け日</th>
                <td>
                    <!--{if $arrForm.shipping_date.value[$shipping_index] == ""}-->
                        指定無し
                    <!--{else}-->
                        <!--{$arrForm.shipping_date_year.value[$shipping_index]|default:""}-->年
                        <!--{$arrForm.shipping_date_month.value[$shipping_index]|default:""}-->月
                        <!--{$arrForm.shipping_date_day.value[$shipping_index]|default:""}-->日
                    <!--{/if}-->
                </td>
            </tr>

        </table>
        <!--{/section}-->
    
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
    
        <div class="btn-area"  >
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="window.close(); return false;"><span class="btn-next">閉じる</span></a></li>
            </ul>
        </div>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
