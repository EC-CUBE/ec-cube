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
    function fnEdit(customer_id) {
        document.form1.action = '<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/edit.php';
        document.form1.mode.value = "edit"
        document.form1['customer_id'].value = customer_id;
        document.form1.submit();
        return false;
    }

    function fnCopyFromOrderData() {
        df = document.form1;
        
        // お届け先名のinputタグのnameを取得
        var shipping_data = $('input[name^=shipping_name01]').attr('name'); 
        var shipping_slt  = shipping_data.split("shipping_name01");
        
        var shipping_key = "[0]";
        if(shipping_slt.length > 1) {
            shipping_key = shipping_slt[1];
        }
        
        df['shipping_name01'+shipping_key].value = df.order_name01.value;
        df['shipping_name02'+shipping_key].value = df.order_name02.value;
//        df['shipping_zip01[0]'].value = df.order_zip01.value;
//        df['shipping_zip02[0]'].value = df.order_zip02.value;
        df['shipping_zipcode'+shipping_key].value = df.order_zipcode.value;
        df['shipping_tel01'+shipping_key].value = df.order_tel01.value;
        df['shipping_tel02'+shipping_key].value = df.order_tel02.value;
        df['shipping_tel03'+shipping_key].value = df.order_tel03.value;
        df['shipping_fax01'+shipping_key].value = df.order_fax01.value;
        df['shipping_fax02'+shipping_key].value = df.order_fax02.value;
        df['shipping_fax03'+shipping_key].value = df.order_fax03.value;
        df['shipping_addr01'+shipping_key].value = df.order_addr01.value;
        df['shipping_addr02'+shipping_key].value = df.order_addr02.value;
    }

    function fnFormConfirm() {
        if (fnConfirm()) {
            document.form1.submit();
        }
    }

    function fnMultiple() {
        win03('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/multiple.php', 'multiple', '600', '500');
        document.form1.anchor_key.value = "shipping";
        document.form1.mode.value = "multiple";
        document.form1.submit();
        return false;
    }

    function fnAppendShipping() {
        document.form1.anchor_key.value = "shipping";
        document.form1.mode.value = "append_shipping";
        document.form1.submit();
        return false;
    }
	
	$(function(){
		var dateFormat = $.datepicker.regional['<!--{$smarty.const.LANG_CODE}-->'].dateFormat;
        
		<!--{foreach name=shipping from=$arrAllShipping item=arrShipping key=shipping_index}-->
        
        <!--{if $arrShipping.shipping_date_year != '' && $arrShipping.shipping_date_month != '' && $arrShipping.shipping_date_day != ''}-->
        var shipping_date_year<!--{$shipping_index}-->   = '<!--{$arrShipping.shipping_date_year|h}-->';
        var shipping_date_month<!--{$shipping_index}--> = '<!--{$arrShipping.shipping_date_month|h}-->';
        var shipping_date_day<!--{$shipping_index}-->   = '<!--{$arrShipping.shipping_date_day|h}-->';
        var shipping_date_ymd<!--{$shipping_index}-->   = $.datepicker.formatDate(
            dateFormat, new Date(shipping_date_year<!--{$shipping_index}-->, shipping_date_month<!--{$shipping_index}--> - 1, shipping_date_day<!--{$shipping_index}-->));
        $("#datepickershipping_date<!--{$shipping_index}-->").val(shipping_date_ymd<!--{$shipping_index}-->);
        // console.log(ymd);
        <!--{/if}-->
        
		$( "#datepickershipping_date<!--{$shipping_index}-->" ).datepicker({
		beforeShowDay: function(date) {
			if(date.getDay() == 0) {
				return [true,"date-sunday"]; 
			} else if(date.getDay() == 6){
				return [true,"date-saturday"];
			} else {
				return [true];
			}
		},changeMonth: 'true'
		,changeYear: 'true'
		,onSelect: function(dateText, inst){
            var year  = inst.selectedYear;
            var month = inst.selectedMonth + 1;
            var day   = inst.selectedDay;
            setDateshipping_date<!--{$shipping_index}-->(year + '/' + month + '/' + day);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtonshipping_date<!--{$shipping_index}-->,       
		onChangeMonthYear: showAdditionalButtonshipping_date<!--{$shipping_index}-->
		});
		
		$("#datepickershipping_date<!--{$shipping_index}-->").change( function() {
            var dateText   = $(this).val();
            var dateFormat = $.datepicker.regional['<!--{$smarty.const.LANG_CODE}-->'].dateFormat;
            // console.log(dateText);
            // console.log(dateFormat);
            var date;
            var year  = '';
            var month = '';
            var day   = '';
            try {
                date = $.datepicker.parseDate(dateFormat, dateText);
                year  = date.getFullYear();
                month = date.getMonth() + 1;
                day   = date.getDate();
            } catch (e) {
                // console.log(e);
                // clear date text
                $(this).val('');
            }
            setDateshipping_date<!--{$shipping_index}-->(year + '/' + month + '/' + day);
		});
		<!--{/foreach}-->
	
	});
	
	var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
	
	<!--{foreach name=shipping from=$arrAllShipping item=arrShipping key=shipping_index}-->
	var showAdditionalButtonshipping_date<!--{$shipping_index}--> = function (input) {
		setTimeout(function () {
			var buttonPane = $(input)
					 .datepicker("widget")
					 .find(".ui-datepicker-buttonpane");
			btn
					.unbind("click")
					.bind("click", function () {
						$.datepicker._clearDate(input);
						$("*[name=shipping_date_year[<!--{$shipping_index}-->]]").val("");
						$("*[name=shipping_date_month[<!--{$shipping_index}-->]]").val("");
						$("*[name=shipping_date_day[<!--{$shipping_index}-->]]").val("");
					});
			btn.appendTo(buttonPane);
		}, 1);
	};
	
	function setDateshipping_date<!--{$shipping_index}-->(dateText){
	var dates = dateText.split('/');
	$("*[name=shipping_date_year[<!--{$shipping_index}-->]]").val(dates[0]);
	$("*[name=shipping_date_month[<!--{$shipping_index}-->]]").val(dates[1]);
	$("*[name=shipping_date_day[<!--{$shipping_index}-->]]").val(dates[2]);
	}
	<!--{/foreach}-->

//-->
</script>
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="<!--{$tpl_mode|default:"edit"|h}-->" />
<input type="hidden" name="order_id" value="<!--{$arrForm.order_id.value|h}-->" />
<input type="hidden" name="edit_customer_id" value="" />
<input type="hidden" name="anchor_key" value="" />
<input type="hidden" id="add_product_id" name="add_product_id" value="" />
<input type="hidden" id="add_product_class_id" name="add_product_class_id" value="" />
<input type="hidden" id="edit_product_id" name="edit_product_id" value="" />
<input type="hidden" id="edit_product_class_id" name="edit_product_class_id" value="" />
<input type="hidden" id="no" name="no" value="" />
<input type="hidden" id="delete_no" name="delete_no" value="" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->

<div id="order" class="contents-main">

    <!--▼お客様情報ここから-->
    <table class="form">
        <!--{if $tpl_mode != 'add'}-->
        <tr>
            <th><!--{t string="tpl_Ledger output_01"}--></th>
            <td><a class="btn-normal" href="javascript:;" onclick="win02('pdf.php?order_id=<!--{$arrForm.order_id.value|h}-->','pdf','615','650'); return false;"><!--{t string="tpl_Ledger output_01"}--></a></td>
        </tr>
        <!--{/if}-->
        <tr>
            <th><!--{t string="tpl_Order number_01"}--></th>
            <td><!--{$arrForm.order_id.value|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Date of order receipt_01"}--></th>
            <td><!--{$arrForm.create_date.value|sfDispDBDate|h}--><input type="hidden" name="create_date" value="<!--{$arrForm.create_date.value|h}-->" /></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Response status_01"}--></th>
            <td>
                <!--{assign var=key value="status"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option value=""><!--{t string="tpl_Please make a selection_01"}--></option>
                    <!--{html_options options=$arrORDERSTATUS selected=$arrForm[$key].value}-->
                </select><br />
                <!--{if $smarty.get.mode != 'add'}-->
                    <span class="attention"><!--{t string="tpl_* When T_ARG1 is selected, restore the inventory count manually._01" T_ARG1=$arrORDERSTATUS[$smarty.const.ORDER_CANCEL]}--></span>
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Date of deposit_01"}--></th>
            <td><!--{$arrForm.payment_date.value|sfDispDBDate|default_t:"tpl_Not deposited_01"|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Shipment date_01"}--></th>
            <td><!--{$arrForm.commit_date.value|sfDispDBDate|default_t:"tpl_Not shipped_01"|h}--></td>
        </tr>
    </table>

    <h2><!--{t string="tpl_Orderer information_01"}-->
        <!--{if $tpl_mode == 'add'}-->
            <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnOpenWindow('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/search_customer.php','search','600','650'); return false;"><!--{t string="tpl_Find users_01"}--></a>
        <!--{/if}-->
    </h2>
    <table class="form">
        <tr>
            <th><!--{t string="tpl_Member ID_01"}--></th>
            <td>
                <!--{if $arrForm.customer_id.value > 0}-->
                    <!--{$arrForm.customer_id.value|h}-->
                    <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id.value|h}-->" />
                <!--{else}-->
                    <!--{t string="tpl_(Non-member)_01"}-->
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Name_02"}--></th>
            <td>
                <!--{assign var=key1 value="order_name01"}-->
                <!--{assign var=key2 value="order_name02"}-->
                <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="15" class="box15" />
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="15" class="box15" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_E-mail address_01"}--></th>
            <td>
                <!--{assign var=key1 value="order_email"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Phone Number_01"}--></th>
            <td>
                <!--{assign var=key1 value="order_tel01"}-->
                <!--{assign var=key2 value="order_tel02"}-->
                <!--{assign var=key3 value="order_tel03"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <span class="attention"><!--{$arrErr[$key3]}--></span>
                <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_FAX_01"}--></th>
            <td>
                <!--{assign var=key1 value="order_fax01"}-->
                <!--{assign var=key2 value="order_fax02"}-->
                <!--{assign var=key3 value="order_fax03"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <span class="attention"><!--{$arrErr[$key3]}--></span>
                <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Address_01"}--></th>
            <td>
                <!--{* <!--{assign var=key1 value="order_zip01"}--> *}-->
                <!--{* <!--{assign var=key2 value="order_zip02"}--> *}-->
                <!--{assign var=key value="order_zipcode"}-->
                
                <!--{* <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span> *}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                
                <!--{t string="tpl_Postal code mark_01"}-->
                <!--{*
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                -
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
                <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01'); return false;"><!--{t string="tpl_Address input_01"}--></a><br />
                *}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="15" class="box10" />

                <!--{assign var=key value="order_addr01"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60 top" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><br />
                <!--{assign var=key value="order_addr02"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Remarks_01"}--></th>
            <td><!--{$arrForm.message.value|h|nl2br}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Current points_01"}--></th>
            <td>
                <!--{if $arrForm.customer_id > 0}-->
                    <!--{t string="pt_prefix"}-->
                    <!--{$arrForm.customer_point.value|number_format}-->
                    <!--{t string="pt_suffix"}-->
                <!--{else}-->
                    <!--{t string="tpl_(Non-member)_01"}-->
            <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Terminal type_01"}--></th>
            <td><!--{$arrDeviceType[$arrForm.device_type_id.value]|h}--></td>
        </tr>

    </table>
    <!--▲お客様情報ここまで-->

    <!--▼受注商品情報ここから-->
    <a name="order_products"></a>
    <h2 id="order_products">
        <!--{t string="tpl_Order receipt product information_01"}-->
        <a class="btn-normal" href="javascript:;" name="recalculate" onclick="fnModeSubmit('recalculate','anchor_key','order_products');"><!--{t string="tpl_Confirm calculation_01"}--></a>
        <a class="btn-normal" href="javascript:;" name="add_product" onclick="win03('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/product_select.php?order_id=<!--{$arrForm.order_id.value|h}-->', 'search', '615', '500'); return false;"><!--{t string="tpl_Add product_01"}--></a>
    </h2>

    <!--{if $arrErr.product_id}-->
        <span class="attention"><!--{t string="tpl_* No product is selected._01"}--></span>
    <!--{/if}-->

    <table class="list" id="order-edit-products">
        <tr>
            <th class="id"><!--{t string="tpl_Product code_01"}--></th>
            <th class="name"><!--{t string="tpl_Product name_01"}-->/<!--{t string="tpl_Standard 1_01"}-->/<!--{t string="tpl_Standard 2_01"}--></th>
            <th class="price"><!--{t string="tpl_Unit price_01"}--></th>
            <th class="qty"><!--{t string="tpl_Quantity_01"}--></th>
            <th class="price"><!--{t string="tpl_Price including tax_01"}--></th>
            <th class="price"><!--{t string="tpl_Subtotal_01"}--></th>
        </tr>
        <!--{section name=cnt loop=$arrForm.quantity.value}-->
        <!--{assign var=product_index value="`$smarty.section.cnt.index`"}-->
        <tr>
            <td>
                <!--{$arrForm.product_code.value[$product_index]|h}-->
                <input type="hidden" name="product_code[<!--{$product_index}-->]" value="<!--{$arrForm.product_code.value[$product_index]|h}-->" id="product_code_<!--{$product_index}-->" />
            </td>
            <td>
                <!--{$arrForm.product_name.value[$product_index]|h}-->/<!--{$arrForm.classcategory_name1.value[$product_index]|default_t:"tpl_(None)_01"|h}-->/<!--{$arrForm.classcategory_name2.value[$product_index]|default_t:"tpl_(None)_01"|h}-->
                <input type="hidden" name="product_name[<!--{$product_index}-->]" value="<!--{$arrForm.product_name.value[$product_index]|h}-->" id="product_name_<!--{$product_index}-->" />
                <input type="hidden" name="classcategory_name1[<!--{$product_index}-->]" value="<!--{$arrForm.classcategory_name1.value[$product_index]|h}-->" id="classcategory_name1_<!--{$product_index}-->" />
                <input type="hidden" name="classcategory_name2[<!--{$product_index}-->]" value="<!--{$arrForm.classcategory_name2.value[$product_index]|h}-->" id="classcategory_name2_<!--{$product_index}-->" />
                <br />
                <a class="btn-normal" href="javascript:;" name="change" onclick="win03('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/product_select.php?no=<!--{$product_index}-->&amp;order_id=<!--{$arrForm.order_id.value|h}-->', 'search', '615', '500'); return false;"><!--{t string="tpl_Change_01"}--></a>
                <!--{if count($arrForm.quantity.value) > 1}-->
                    <a class="btn-normal" href="javascript:;" name="delete" onclick="fnSetFormVal('form1', 'delete_no', <!--{$product_index}-->); fnModeSubmit('delete_product','anchor_key','order_products'); return false;"><!--{t string="tpl_Remove_01"}--></a>
                <!--{/if}-->
            <input type="hidden" name="product_type_id[<!--{$product_index}-->]" value="<!--{$arrForm.product_type_id.value[$product_index]|h}-->" id="product_type_id_<!--{$product_index}-->" />
            <input type="hidden" name="product_id[<!--{$product_index}-->]" value="<!--{$arrForm.product_id.value[$product_index]|h}-->" id="product_id_<!--{$product_index}-->" />
            <input type="hidden" name="product_class_id[<!--{$product_index}-->]" value="<!--{$arrForm.product_class_id.value[$product_index]|h}-->" id="product_class_id_<!--{$product_index}-->" />
            <input type="hidden" name="point_rate[<!--{$product_index}-->]" value="<!--{$arrForm.point_rate.value[$product_index]|h}-->" id="point_rate_<!--{$product_index}-->" />
            </td>
            <td align="center">
                <!--{assign var=key value="price"}-->
                <span class="attention"><!--{$arrErr[$key][$product_index]}--></span>
                <!--{t string="currency_prefix"}--><input type="text" name="<!--{$key}-->[<!--{$product_index}-->]" value="<!--{$arrForm[$key].value[$product_index]|h}-->" size="6" class="box6" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$product_index]|sfGetErrorColor}-->" id="<!--{$key}-->_<!--{$product_index}-->" /> <!--{t string="currency_suffix"}-->
            </td>
            <td align="center">
                <!--{assign var=key value="quantity"}-->
                <span class="attention"><!--{$arrErr[$key][$product_index]}--></span>
                <input type="text" name="<!--{$key}-->[<!--{$product_index}-->]" value="<!--{$arrForm[$key].value[$product_index]|h}-->" size="3" class="box3" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$product_index]|sfGetErrorColor}-->" id="<!--{$key}-->_<!--{$product_index}-->" />
            </td>
            <!--{assign var=price value=`$arrForm.price.value[$product_index]`}-->
            <!--{assign var=quantity value=`$arrForm.quantity.value[$product_index]`}-->
            <td class="right"><!--{t string="currency_prefix"}--><!--{$price|sfCalcIncTax|number_format}--><!--{t string="currency_suffix"}--></td>
            <td class="right"><!--{t string="tpl_&#36; T_ARG1_01" escape="none" T_ARG1=$price|sfCalcIncTax|sfMultiply:$quantity|number_format}--></td>
        </tr>
        <!--{/section}-->
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_Subtotal_01"}--></th>
            <td class="right"><!--{t string="tpl_&#36; T_ARG1_01" escape="none" T_ARG1=$arrForm.subtotal.value|number_format}--></td>
        </tr>
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_Discount_01"}--></th>
            <td class="right">
                <!--{assign var=key value="discount"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{t string="currency_prefix"}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                <!--{t string="currency_suffix"}-->
            </td>
        </tr>
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_Shipping fee_01"}--></th>
            <td class="right">
                <!--{assign var=key value="deliv_fee"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{t string="currency_prefix"}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                <!--{t string="currency_suffix"}-->
            </td>
        </tr>
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_Processing fee_01"}--></th>
            <td class="right">
                <!--{assign var=key value="charge"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{t string="currency_prefix"}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                <!--{t string="currency_suffix"}-->
            </td>
        </tr>
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_Total_01"}--></th>
            <td class="right">
                <span class="attention"><!--{$arrErr.total}--></span>
                <!--{t string="currency_prefix"}--><!--{$arrForm.total.value|number_format}--><!--{t string="currency_suffix"}-->
            </td>
        </tr>
        <tr>
            <th colspan="5" class="column right"><!--{t string="tpl_Payment total_01"}--></th>
            <td class="right">
                <span class="attention"><!--{$arrErr.payment_total}--></span>
                <!--{t string="currency_prefix"}--><!--{$arrForm.payment_total.value|number_format}--><!--{t string="currency_suffix"}-->
            </td>
        </tr>
        <!--{if $smarty.const.USE_POINT !== false}-->
            <tr>
                <th colspan="5" class="column right"><!--{t string="tpl_Points used_01"}--></th>
                <td class="right">
                    <!--{assign var=key value="use_point"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <!--{t string="pt_prefix"}-->
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:0|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="5" class="box6" />
                    <!--{t string="pt_suffix"}-->
                </td>
            </tr>
            <!--{if $arrForm.birth_point.value > 0}-->
            <tr>
                <th colspan="5" class="column right"><!--{t string="tpl_Birthday points_001"}--></th>
                <td class="right">
                    <!--{t string="pt_prefix"}-->
                    <!--{$arrForm.birth_point.value|number_format}-->
                    <!--{t string="pt_suffix"}-->
                </td>
            </tr>
            <!--{/if}-->
            <tr>
                <th colspan="5" class="column right"><!--{t string="tpl_Points added_01"}--></th>
                <td class="right">
                    <!--{t string="pt_prefix"}-->
                    <!--{$arrForm.add_point.value|number_format|default:0}-->
                    <!--{t string="pt_suffix"}-->
                </td>
            </tr>
        <!--{/if}-->
    </table>
    <!--{assign var=key value="shipping_quantity"}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
    <!--▼お届け先情報ここから-->
    <a name="shipping"></a>
    <h2><!--{t string="tpl_Delivery destination information_01"}-->
    <!--{if $arrForm.shipping_quantity.value <= 1}-->
        <a class="btn-normal" href="javascript:;" onclick="fnCopyFromOrderData();"><!--{t string="tpl_Deliver to customers_01"}--></a>
    <!--{/if}-->
    <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
        <a class="btn-normal" href="javascript:;"  onclick="fnAppendShipping();"><!--{t string="tpl_Add new delivery destination_01"}--></a>
        <a class="btn-normal" href="javascript:;" onclick="fnMultiple();"><!--{t string="tpl_ Multiple delivery destinations_01"}--></a>
    <!--{/if}-->
    </h2>

    <!--{foreach name=shipping from=$arrAllShipping item=arrShipping key=shipping_index}-->
        <!--{if $arrForm.shipping_quantity.value > 1}-->
            <h3><!--{t string="tpl_Delivery destination_01"}--><!--{$smarty.foreach.shipping.iteration}--></h3>
        <!--{/if}-->
        <!--{assign var=key value="shipping_id"}-->
        <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key]|default:"0"|h}-->" id="<!--{$key}-->_<!--{$shipping_index}-->" />
        <!--{if $arrForm.shipping_quantity.value > 1}-->
            <!--{assign var=product_quantity value="shipping_product_quantity"}-->
            <input type="hidden" name="<!--{$product_quantity}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$product_quantity]|h}-->" />

            <!--{if count($arrShipping.shipment_product_class_id) > 0}-->
                <table class="list" id="order-edit-products">
                    <tr>
                        <th class="id"><!--{t string="tpl_Product code_01"}--></th>
                        <th class="name"><!--{t string="tpl_Product name_01"}-->/<!--{t string="tpl_Standard 1_01"}-->/<!--{t string="tpl_Standard 2_01"}--></th>
                        <th class="price"><!--{t string="tpl_Unit price_01"}--></th>
                        <th class="qty"><!--{t string="tpl_Quantity_01"}--></th>
                    </tr>
                    <!--{section name=item loop=$arrShipping.shipment_product_class_id|@count}-->
                        <!--{assign var=item_index value="`$smarty.section.item.index`"}-->

                        <tr>
                            <td>
                                <!--{assign var=key value="shipment_product_class_id"}-->
                                <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" />
                                <!--{assign var=key value="shipment_product_code"}-->
                                <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" />
                                <!--{$arrShipping[$key][$item_index]|h}-->
                            </td>
                            <td>
                                <!--{assign var=key1 value="shipment_product_name"}-->
                                <!--{assign var=key2 value="shipment_classcategory_name1"}-->
                                <!--{assign var=key3 value="shipment_classcategory_name2"}-->
                                <input type="hidden" name="<!--{$key1}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key1][$item_index]|h}-->" />
                                <input type="hidden" name="<!--{$key2}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key2][$item_index]|h}-->" />
                                <input type="hidden" name="<!--{$key3}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key3][$item_index]|h}-->" />
                                <!--{$arrShipping[$key1][$item_index]|h}-->/<!--{$arrShipping[$key2][$item_index]|default_t:"tpl_(None)_01"|h}-->/<!--{$arrShipping[$key3][$item_index]|default_t:"tpl_(None)_01"|h}-->
                            </td>
                            <td class="right">
                                <!--{assign var=key value="shipment_price"}-->
                                <!--{t string="tpl_&#36; T_ARG1_01" escape="none" T_ARG1=$arrShipping[$key][$item_index]|sfCalcIncTax|number_format}-->
                                <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" />
                            </td>
                            <td class="right">
                                <!--{assign var=key value="shipment_quantity"}-->
                                <!--{$arrShipping[$key][$item_index]|h}-->
                                <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrShipping[$key][$item_index]|h}-->" />
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
                    <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--><!--{$arrErr[$key2][$shipping_index]}--></span>
                    <input type="text" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->" size="15" class="box15" />
                    <input type="text" name="<!--{$key2}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key2]|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->" size="15" class="box15" />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Phone Number_01"}--></th>
                <td>
                    <!--{assign var=key1 value="shipping_tel01"}-->
                    <!--{assign var=key2 value="shipping_tel02"}-->
                    <!--{assign var=key3 value="shipping_tel03"}-->
                    <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--></span>
                    <span class="attention"><!--{$arrErr[$key2][$shipping_index]}--></span>
                    <span class="attention"><!--{$arrErr[$key3][$shipping_index]}--></span>
                    <input type="text" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" /> -
                    <input type="text" name="<!--{$key2}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key2]|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" /> -
                    <input type="text" name="<!--{$key3}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key3]|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_FAX_01"}--></th>
                <td>
                    <!--{assign var=key1 value="shipping_fax01"}-->
                    <!--{assign var=key2 value="shipping_fax02"}-->
                    <!--{assign var=key3 value="shipping_fax03"}-->
                    <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--></span>
                    <span class="attention"><!--{$arrErr[$key2][$shipping_index]}--></span>
                    <span class="attention"><!--{$arrErr[$key3][$shipping_index]}--></span>
                    <input type="text" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" /> -
                    <input type="text" name="<!--{$key2}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key2]|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" /> -
                    <input type="text" name="<!--{$key3}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key3]|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Address_01"}--></th>
                <td>
                    <!--{* <!--{assign var=key1 value="shipping_zip01"}--> *}-->
                    <!--{* <!--{assign var=key2 value="shipping_zip02"}--> *}-->
                    <!--{assign var=key value="shipping_zipcode"}-->

                    <!--{* <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--><!--{$arrErr[$key2][$shipping_index]}--></span> *}-->
                    <span class="attention"><!--{$arrErr[$key][$shipping_index]}--></span>

                    <!--{t string="tpl_Postal code mark_01"}-->
                    <!--{*
                    <input type="text" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" />
                    -
                    <input type="text" name="<!--{$key2}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key2]|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2][$shipping_index]|sfGetErrorColor}-->" size="6" class="box6" />
                    <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'shipping_zip01[<!--{$shipping_index}-->]', 'shipping_zip02[<!--{$shipping_index}-->]', 'shipping_pref[<!--{$shipping_index}-->]', 'shipping_addr01[<!--{$shipping_index}-->]'); return false;"><!--{t string="tpl_Address input_01"}--></a><br />
                    *}-->
                    <input type="text" name="<!--{$key}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key]|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$shipping_index]|sfGetErrorColor}-->" size="15" class="box10" />

                    <!--{assign var=key value="shipping_addr01"}-->
                    <span class="attention"><!--{$arrErr[$key][$shipping_index]}--></span>
                    <input type="text" name="<!--{$key}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key]|h}-->" size="60" class="box60 top" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$shipping_index]|sfGetErrorColor}-->" /><br />
                    <!--{assign var=key value="shipping_addr02"}-->
                    <span class="attention"><!--{$arrErr[$key][$shipping_index]}--></span>
                    <input type="text" name="<!--{$key}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key]|h}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key][$shipping_index]|sfGetErrorColor}-->" />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Delivery time_01"}--></th>
                <td>
                    <!--{assign var=key value="time_id"}-->
                    <span class="attention"><!--{$arrErr[$key][$shipping_index]}--></span>
                    <select name="<!--{$key}-->[<!--{$shipping_index}-->]" style="<!--{$arrErr[$key][$shipping_index]|sfGetErrorColor}-->">
                        <option value="" selected="0"><!--{t string="tpl_No designation_01"}--></option>
                        <!--{html_options options=$arrDelivTime selected=$arrShipping[$key]}-->
                    </select>
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Delivery date_01"}--></th>
                <td>
            <!--{assign var=key1 value="shipping_date_year"}-->
            <!--{assign var=key2 value="shipping_date_month"}-->
            <!--{assign var=key3 value="shipping_date_day"}-->
            <span class="attention"><!--{$arrErr[$key1][$shipping_index]}--></span>
            <span class="attention"><!--{$arrErr[$key2][$shipping_index]}--></span>
            <span class="attention"><!--{$arrErr[$key3][$shipping_index]}--></span>
            
            <input id="datepickershipping_date<!--{$shipping_index}-->"
                   type="text"
                   value="" <!--{if $arrErr[$key1][$shipping_index] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
            <input type="hidden" name="<!--{$key1}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key1]|default:""|h}-->" />
            <input type="hidden" name="<!--{$key2}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key2]|default:""|h}-->" />
            <input type="hidden" name="<!--{$key3}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key3]|default:""|h}-->" />
                </td>
            </tr>

        </table>
    <!--{/foreach}-->
    <!--▲お届け先情報ここまで-->

    <a name="deliv"></a>
    <table class="form">
        <tr>
            <th><!--{t string="tpl_Delivery company_01"}--><br /><span class="attention"><!--{t string="tpl_(Due to changes in shipping companies, please manually select a shipping method.)_01"}--></span></th>
            <td>
                <!--{assign var=key value="deliv_id"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onchange="fnModeSubmit('deliv','anchor_key','deliv');">
                    <option value="" selected=""><!--{t string="tpl_Please make a selection_01"}--></option>
                    <!--{html_options options=$arrDeliv selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Payment method_01"}--><br /><span class="attention"><!--{t string="tpl_(Due to changes in payment, please manually select a fee.)_01"}--></span></th>
            <td>
                <!--{assign var=key value="payment_id"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onchange="fnModeSubmit('payment','anchor_key','deliv');">
                    <option value="" selected=""><!--{t string="tpl_Please make a selection_01"}--></option>
                    <!--{html_options options=$arrPayment selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>

        <!--{if $arrForm.payment_info|@count > 0}-->
        <tr>
            <th><!--{t string="tpl_T_ARG1 information_01" T_ARG1=$arrForm.payment_type}--></th>
            <td>
                <!--{foreach key=key item=item from=$arrForm.payment_info}-->
                <!--{if $key != "title"}--><!--{if $item.name != ""}--><!--{t string="tpl_T_ARG1:_01" T_ARG1=$item.name}--><!--{/if}--><!--{$item.value}--><br/><!--{/if}-->
                <!--{/foreach}-->
            </td>
        </tr>
        <!--{/if}-->

        <tr>
            <th><!--{t string="tpl_Memo_01"}--></th>
            <td>
                <!--{assign var=key value="note"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="80" rows="6" class="area80" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{"\n"}--><!--{$arrForm[$key].value|h}--></textarea>
            </td>
        </tr>
    </table>
    <!--▲受注商品情報ここまで-->

    <div class="btn-area">
        <ul>
            <!--{if count($arrSearchHidden) > 0}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_URLPATH}-->'); fnModeSubmit('search','',''); return false;"><span class="btn-prev"><!--{t string="tpl_Return to search screen_01"}--></span></a></li>
            <!--{/if}-->
            <li><a class="btn-action" href="javascript:;" onclick="return fnFormConfirm(); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
</div>
<div id="multiple"></div>
</form>
