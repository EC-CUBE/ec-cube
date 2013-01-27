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

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

    <!--▼お客様情報ここから-->
    <table class="form">
        <tr>
            <th><!--{t string="tpl_Order number_01"}--></th>
            <td><!--{$arrForm.order_id.value|h}--></td>
            <input type="hidden" name="order_id" value="<!--{$arrForm.order_id.value|h}-->" />
        </tr>
        <tr>
            <th><!--{t string="tpl_359"}--></th>
            <td><!--{$arrForm.create_date.value|sfDispDBDate|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_360"}--></th>
            <td><!--{$arrORDERSTATUS[$arrForm.status.value]|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_361"}--></th>
            <td><!--{$arrForm.payment_date.value|sfDispDBDate|default_t:"tpl_443"}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Shipment date_01"}--></th>
            <td><!--{$arrForm.commit_date.value|sfDispDBDate|default_t:"tpl_Not shipped_01"}--></td>
        </tr>
    </table>

    <h2><!--{t string="tpl_362"}--></h2>
    <table class="form">
        <tr>
            <th><!--{t string="tpl_Member ID_01"}--></th>
            <td>
                <!--{if $arrForm.customer_id.value > 0}-->
                    <!--{$arrForm.customer_id.value|h}-->
                <!--{else}-->
                    <!--{t string="tpl_363"}-->
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Name_02"}--></th>
            <td><!--{$arrForm.order_name01.value|h}-->&nbsp;<!--{$arrForm.order_name02.value|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_E-mail address_01"}--></th>
            <td><!--{$arrForm.order_email.value|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Phone Number_01"}--></th>
            <td><!--{$arrForm.order_tel01.value|h}--> - <!--{$arrForm.order_tel02.value|h}--> - <!--{$arrForm.order_tel03.value|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Address_01"}--></th>
            <td>
                <!--{* <!--{t string="tpl_Postal code mark_01"}-->　<!--{$arrForm.order_zip01.value|h}--> - <!--{$arrForm.order_zip02.value|h}--><br /> *}-->
                <!--{t string="tpl_Postal code mark_01"}-->&nbsp;<!--{$arrForm.order_zipcode.value|h}--><br />
                <!--{$arrPref[$arrForm.order_pref.value]|h}--><!--{$arrForm.order_addr01.value|h}--><!--{$arrForm.order_addr02.value|h}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_365"}--></th>
            <td><!--{$arrForm.message.value|h|nl2br}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_366"}--></th>
            <td>
                <!--{if $arrForm.customer_id >0}-->
                    <!--{t string="pt_prefix"}-->
                    <!--{$arrForm.customer_point.value|number_format}-->
                    <!--{t string="pt_suffix"}-->
                <!--{else}-->
                    <!--{t string="tpl_363"}-->
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_367"}--></th>
            <td><!--{$arrDeviceType[$arrForm.device_type_id.value]|h}--></td>
        </tr>
    </table>
    <!--▲お客様情報ここまで-->

    <!--▼受注商品情報ここから-->
    <h2><!--{t string="tpl_368"}--></h2>
    <table class="list">
        <tr>
            <th class="id"><!--{t string="tpl_Product code_01"}--></th>
            <th class="name"><!--{t string="tpl_Product name_01"}-->/<!--{t string="tpl_371"}-->/<!--{t string="tpl_374"}--></th>
            <th class="price"><!--{t string="tpl_372"}--></th>
            <th class="qty"><!--{t string="tpl_373"}--></th>
            <th class="price"><!--{t string="tpl_375"}--></th>
            <th class="price"><!--{t string="tpl_376"}--></th>
        </tr>
        <!--{section name=cnt loop=$arrForm.quantity.value}-->
        <!--{assign var=product_index value="`$smarty.section.cnt.index`"}-->
        <tr>
            <td>
                <!--{$arrForm.product_code.value[$product_index]|h}-->
            </td>
            <td>
                <!--{$arrForm.product_name.value[$product_index]|h}-->/<!--{$arrForm.classcategory_name1.value[$product_index]|default_t:"tpl_729"|h}-->/<!--{$arrForm.classcategory_name2.value[$product_index]|default_t:"tpl_729"|h}-->
            </td>
            <td align="center">
                    <!--{t string="currency_prefix"}--><!--{$arrForm.price.value[$product_index]|h}--><!--{t string="currency_suffix"}-->
                </td>
            <td align="center">
                <!--{$arrForm.quantity.value[$product_index]|h}-->
            </td>
                <!--{assign var=price value=`$arrForm.price.value[$product_index]`}-->
                <!--{assign var=quantity value=`$arrForm.quantity.value[$product_index]`}-->
                <td class="right"><!--{t string="currency_prefix"}--><!--{$price|sfCalcIncTax|number_format}--><!--{t string="currency_suffix"}--></td>
                <td class="right"><!--{t string="currency_prefix"}--><!--{$price|sfCalcIncTax|sfMultiply:$quantity|number_format}--><!--{t string="currency_suffix"}--></td>
        </tr>
        <!--{/section}-->
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_376"}--></th>
            <td class="right"><!--{t string="tpl_500" escape="none" T_FIELD=$arrForm.subtotal.value|number_format}--></td>
        </tr>
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_377"}--></th>
            <td class="right"><!--{t string="tpl_500" escape="none" T_FIELD=$arrForm.discount.value|h}--></td>
        </tr>
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_378"}--></th>
            <td class="right"><!--{t string="tpl_500" escape="none" T_FIELD=$arrForm.deliv_fee.value|h}--></td>
        </tr>
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_Processing fee_01"}--></th>
            <td class="right"><!--{t string="tpl_500" escape="none" T_FIELD=$arrForm.charge.value|h}--></td>
        </tr>
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_379"}--></th>
            <td class="right"><!--{t string="currency_prefix"}--><!--{$arrForm.total.value|number_format}--><!--{t string="currency_suffix"}--></td>
        </tr>
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_380"}--></th>
            <td class="right"><!--{t string="currency_prefix"}--><!--{$arrForm.payment_total.value|number_format}--><!--{t string="currency_suffix"}--></td>
        </tr>
        <!--{if $smarty.const.USE_POINT !== false}-->
            <tr>
                <th colspan="5" class="column right"><!--{t string="tpl_381"}--></th>
                <td class="right"><!--{t string="pt_prefix"}--><!--{$arrForm.use_point.value|default:0|h}--><!--{t string="pt_suffix"}--></td>
            </tr>
            <!--{if $arrForm.birth_point.value > 0}-->
                <tr>
                    <th colspan="5" class="column right"><!--{t string="tpl_382"}--></th>
                    <td class="right"><!--{t string="pt_prefix"}--><!--{$arrForm.birth_point.value|number_format}--><!--{t string="pt_suffix"}--></td>
                </tr>
            <!--{/if}-->
            <tr>
                <th colspan="5" class="column right"><!--{t string="tpl_383"}--></th>
                <td class="right"><!--{t string="pt_prefix"}--><!--{$arrForm.add_point.value|number_format|default:0}--><!--{t string="pt_suffix"}--></td>
            </tr>
        <!--{/if}-->
    </table>
    <!--▼お届け先情報ここから-->
    <h2><!--{t string="tpl_384"}--></h2>
    <!--{if $arrForm.product_type_id.value[0] != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
    <!--{foreach name=shipping from=$arrAllShipping item=arrShipping key=shipping_index}-->
        <!--{if $arrForm.shipping_quantity.value > 1}-->
            <h3><!--{t string="tpl_385"}--><!--{$smarty.foreach.shipping.iteration}--></h3>
        <!--{/if}-->
        <!--{assign var=key value="shipping_id"}-->
        <!--{if $arrForm.shipping_quantity.value > 1}-->

            <!--{if count($arrShipping.shipment_product_class_id) > 0}-->
                <table class="list" id="order-edit-products">
                    <tr>
                        <th class="id"><!--{t string="tpl_Product code_01"}--></th>
                        <th class="name"><!--{t string="tpl_Product name_01"}-->/<!--{t string="tpl_371"}-->/<!--{t string="tpl_374"}--></th>
                        <th class="price"><!--{t string="tpl_372"}--></th>
                        <th class="qty"><!--{t string="tpl_373"}--></th>
                    </tr>
                    <!--{section name=item loop=$arrShipping.shipment_product_class_id|@count}-->
                        <!--{assign var=item_index value="`$smarty.section.item.index`"}-->
                        <tr>
                            <td>
                                <!--{assign var=key value="shipment_product_code"}-->
                                <!--{$arrShipping[$key][$item_index]|h}-->
                            </td>
                            <td>
                                <!--{assign var=key1 value="shipment_product_name"}-->
                                <!--{assign var=key2 value="shipment_classcategory_name1"}-->
                                <!--{assign var=key3 value="shipment_classcategory_name2"}-->
                                <!--{$arrShipping[$key1][$item_index]|h}-->/<!--{$arrShipping[$key2][$item_index]|default_t:"tpl_729"|h}-->/<!--{$arrShipping[$key3][$item_index]|default_t:"tpl_729"|h}-->
                            </td>
                            <td class="right">
                                <!--{assign var=key value="shipment_price"}-->
                                <!--{t string="tpl_500" escape="none" T_FIELD=$arrShipping[$key][$item_index]|sfCalcIncTax|number_format}-->
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
                <th><!--{t string="tpl_Name_02"}--></th>
                <td>
                    <!--{assign var=key1 value="shipping_name01"}-->
                    <!--{assign var=key2 value="shipping_name02"}-->
                    <!--{$arrShipping[$key1]|h}-->&nbsp;<!--{$arrShipping[$key2]|h}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Phone Number_01"}--></th>
                <td>
                    <!--{assign var=key1 value="shipping_tel01"}-->
                    <!--{assign var=key2 value="shipping_tel02"}-->
                    <!--{assign var=key3 value="shipping_tel03"}-->
                    <!--{$arrShipping[$key1]|h}--> -
                    <!--{$arrShipping[$key2]|h}--> -
                    <!--{$arrShipping[$key3]|h}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Address_01"}--></th>
                <td>
                    <!--{* <!--{assign var=key1 value="shipping_zip01"}--> *}-->
                    <!--{* <!--{assign var=key2 value="shipping_zip02"}--> *}-->
                    <!--{assign var=key value="shipping_zipcode"}-->

                    <!--{t string="tpl_Postal code mark_01"}-->
                    <!--{*
                    <!--{$arrShipping[$key1]|h}-->
                    -
                    <!--{$arrShipping[$key2]|h}-->
                    *}-->
                    <!--{$arrShipping[$key]|h}-->
                    <br />
                    <!--{$arrPref[$arrShipping.shipping_pref]|h}-->
                    <!--{assign var=key value="shipping_addr01"}-->
                    <!--{$arrShipping[$key]|h}-->
                    <!--{assign var=key value="shipping_addr02"}-->
                    <!--{$arrShipping[$key]|h}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_386"}--></th>
                <td>
                    <!--{$arrDelivTime[$arrShipping.time_id]|default_t:"tpl_388"}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_387"}--></th>
                <td>
                    <!--{assign var=key1 value="shipping_date_year"}-->
                    <!--{assign var=key2 value="shipping_date_month"}-->
                    <!--{assign var=key3 value="shipping_date_day"}-->
                    <!--{if $arrShipping[$key1] == "" && $arrShipping[$key2] == "" && $arrShipping[$key3] == ""}-->
                        <!--{t string="tpl_388"}-->
                    <!--{else}-->
                        <!--{t string="tpl_726" T_FIELD1=$arrShipping[$key1] T_FIELD2=$arrShipping[$key2] T_FIELD3=$arrShipping[$key3] }-->
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
                <th><!--{t string="tpl_Delivery company_01"}--></th>
                <td>
                    <!--{$arrDeliv[$arrForm.deliv_id.value]|h}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_389"}--></th>
                <td>
                    <!--{$arrPayment[$arrForm.payment_id.value]|h}-->
                </td>
            </tr>

            <!--{if $arrForm.payment_info|@count > 0}-->
            <tr>
                <th><!--{t string="tpl_390" T_FIELD=$arrForm.payment_type}--></th>
                <td>
                    <!--{foreach key=key item=item from=$arrForm.payment_info}-->
                    <!--{if $key != "title"}--><!--{if $item.name != ""}--><!--{t string="t_T_FIELD:_01" T_FIELD=$item.name}--><!--{/if}--><!--{$item.value}--><br/><!--{/if}-->
                    <!--{/foreach}-->
                </td>
            </tr>
            <!--{/if}-->

            <tr>
                <th><!--{t string="tpl_391"}--></th>
                <td>
                    <!--{$arrForm.note.value|h|nl2br}-->
                </td>
            </tr>
        </table>

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="window.close(); return false;"><span class="btn-next"><!--{t string="tpl_392"}--></span></a></li>
            </ul>
        </div>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
