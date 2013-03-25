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
    function fnReturn() {
        document.search_form.action = './<!--{$smarty.const.DIR_INDEX_PATH}-->';
        document.search_form.submit();
        return false;
    }
	
	$(function(){
        var dateFormat = $.datepicker.regional['<!--{$smarty.const.LANG_CODE}-->'].dateFormat;

        <!--{if $arrForm.year != '' && $arrForm.month != '' && $arrForm.day != ''}-->
        var year  = '<!--{$arrForm.year|h}-->';
        var month = '<!--{$arrForm.month|h}-->';
        var day   = '<!--{$arrForm.day|h}-->';
        var ymd = $.datepicker.formatDate(dateFormat, new Date(year, month - 1, day));
        $("#datepickercustomer_edit").val(ymd);
        // console.log(ymd);
        <!--{/if}-->

		$( "#datepickercustomer_edit" ).datepicker({
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
			setDatecustomer_edit(year + '/' + month + '/' + day);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomer_edit,       
		onChangeMonthYear: showAdditionalButtoncustomer_edit
		});
		
		$("#datepickercustomer_edit").change( function() {
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
            setDatecustomer_edit(year + '/' + month + '/' + day);
		});
		
	});
	
	var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
	
	var showAdditionalButtoncustomer_edit = function (input) {
		setTimeout(function () {
			var buttonPane = $(input)
					 .datepicker("widget")
					 .find(".ui-datepicker-buttonpane");
			btn
					.unbind("click")
					.bind("click", function () {
						$.datepicker._clearDate(input);
						$("*[name=year]").val("");
						$("*[name=month]").val("");
						$("*[name=day]").val("");
					});
			btn.appendTo(buttonPane);
		}, 1);
	};
	
	function setDatecustomer_edit(dateText){
	var dates = dateText.split('/');
	$("*[name=year]").val(dates[0]);
	$("*[name=month]").val(dates[1]);
	$("*[name=day]").val(dates[2]);
	}
//-->
</script>

<form name="search_form" method="post" action="">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />

    <!--{foreach from=$arrSearchData key="key" item="item"}-->
        <!--{if $key ne "customer_id" && $key ne "mode" && $key ne "edit_customer_id" && $key ne $smarty.const.TRANSACTION_ID_NAME}-->
            <!--{if is_array($item)}-->
                <!--{foreach item=c_item from=$item}-->
                    <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
                <!--{/foreach}-->
            <!--{else}-->
                <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/if}-->
    <!--{/foreach}-->
</form>

<form name="form1" id="form1" method="post" action="?" autocomplete="off">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="confirm" />
    <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />

    <!-- 検索条件の保持 -->
    <!--{foreach from=$arrSearchData key="key" item="item"}-->
        <!--{if $key ne "customer_id" && $key ne "mode" && $key ne "edit_customer_id" && $key ne $smarty.const.TRANSACTION_ID_NAME}-->
            <!--{if is_array($item)}-->
                <!--{foreach item=c_item from=$item}-->
                    <input type="hidden" name="search_data[<!--{$key|h}-->][]" value="<!--{$c_item|h}-->" />
                <!--{/foreach}-->
            <!--{else}-->
                <input type="hidden" name="search_data[<!--{$key|h}-->]" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/if}-->
    <!--{/foreach}-->

    <div id="customer" class="contents-main">
        <table class="form">
            <!--{if $arrForm.customer_id}-->
            <tr>
                <th><!--{t string="tpl_Member ID<span class='attention'> *</span>_01" escape="none"}--></th>
                <td><!--{$arrForm.customer_id|h}--></td>
            </tr>
            <!--{/if}-->
            <tr>
                <th><!--{t string="tpl_Member status<span class='attention'> *</span>_01" escape="none"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.status}--></span>
                    <span <!--{if $arrErr.status != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                        <!--{html_radios name="status" options=$arrStatus separator=" " selected=$arrForm.status}-->
                    </span>
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Name<span class='attention'> *</span>_02" escape="none"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
                    <input type="text" name="name01" value="<!--{$arrForm.name01|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" <!--{if $arrErr.name01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;&nbsp;<input type="text" name="name02" value="<!--{$arrForm.name02|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" <!--{if $arrErr.name02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Postal code_01" escape="none"}--></th>
                <td>
                    <!--{* <span class="attention"><!--{$arrErr.zip01}--><!--{$arrErr.zip02}--></span> *}-->
                    <span class="attention"><!--{$arrErr.zipcode}--></span>

                    <!--{*
                    <!--{t string="tpl_Postal code mark_01"}--> <input type="text" name="zip01" value="<!--{$arrForm.zip01|h}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" size="6" class="box6" maxlength="3" <!--{if $arrErr.zip01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="zip02" value="<!--{$arrForm.zip02|h}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" size="6" class="box6" maxlength="4" <!--{if $arrErr.zip02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                    <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;"><!--{t string="tpl_Address input_01"}--></a>
                    *}-->
                    <!--{t string="tpl_Postal code mark_01"}--> <input type="text" name="zipcode" value="<!--{$arrForm.zipcode|h}-->" maxlength="<!--{$smarty.const.ZIPCODE_LEN}-->" size="15" class="box10" maxlength="10" <!--{if $arrErr.zipcode != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Address<span class='attention'> *</span>_01" escape="none"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
                    <input type="text" name="addr01" value="<!--{$arrForm.addr01|h}-->" size="60" class="box60" <!--{if $arrErr.addr01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                    <!--{$smarty.const.SAMPLE_ADDRESS1}--><br />
                    <input type="text" name="addr02" value="<!--{$arrForm.addr02|h}-->" size="60" class="box60" <!--{if $arrErr.addr02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                    <!--{$smarty.const.SAMPLE_ADDRESS2}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_E-mail address<span class='attention'> *</span>_01" escape="none"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.email}--></span>
                    <input type="text" name="email" value="<!--{$arrForm.email|h}-->" size="60" class="box60" <!--{if $arrErr.email != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Mobile e-mail address_01"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.email_mobile}--></span>
                    <input type="text" name="email_mobile" value="<!--{$arrForm.email_mobile|h}-->" size="60" class="box60" <!--{if $arrErr.email_mobile != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Telephone number<span class='attention'> *</span>_01" escape="none"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
                    <input type="text" name="tel01" value="<!--{$arrForm.tel01|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="tel02" value="<!--{$arrForm.tel02|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != "" || $arrErr.tel02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="tel03" value="<!--{$arrForm.tel03|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != "" || $arrErr.tel03 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_FAX_01"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></span>
                    <input type="text" name="fax01" value="<!--{$arrForm.fax01|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="fax02" value="<!--{$arrForm.fax02|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != "" || $arrErr.fax02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="fax03" value="<!--{$arrForm.fax03|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != "" || $arrErr.fax03 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Gender<span class='attention'> *</span>_01" escape="none"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.sex}--></span>
                    <span <!--{if $arrErr.sex != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                        <!--{html_radios name="sex" options=$arrSex separator=" " selected=$arrForm.sex}-->
                    </span>
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Occupation_01"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.job}--></span>
                    <select name="job" <!--{if $arrErr.job != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                    <option value="" selected="selected"><!--{t string="tpl_Please make a selection_01"}--></option>
                    <!--{html_options options=$arrJob selected=$arrForm.job}-->
                    </select>
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Date of birth_01"}--></th>
                <td>
                    <!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
                    <!--{if $errBirth}-->
                        <div class="attention"><!--{$errBirth}--></div>
                    <!--{/if}-->
                    <input id="datepickercustomer_edit"
                           type="text"
                           value="" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                    <input type="hidden" name="year" value="<!--{$arrForm.year}-->" />
                    <input type="hidden" name="month" value="<!--{$arrForm.month}-->" />
                    <input type="hidden" name="day" value="<!--{$arrForm.day}-->" />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Password<span class='attention'> *</span>_01" escape="none"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.password}--><!--{$arrErr.password02}--></span>
                    <input type="password" name="password" value="<!--{$arrForm.password|h}-->" size="30" class="box30" <!--{if $arrErr.password != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;<!--{t string="tpl_Lower-case alphanumeric characters T_ARG1 to T_ARG2 (Symbols cannot be used)_01" T_ARG1=$smarty.const.PASSWORD_MIN_LEN T_ARG2=$smarty.const.PASSWORD_MAX_LEN}--><br />
                    <input type="password" name="password02" value="<!--{$arrForm.password02|h}-->" size="30" class="box30" <!--{if $arrErr.password02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                    <p><span class="attention mini"><!--{t string="tpl_Enter twice for confirmation_01"}--></span></p>
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Hint for when you have forgotten your password<span class='attention'> *</span>_01" escape="none"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></span>
                    <!--{t string="tpl_Question:_01"}-->
                    <select class="top" name="reminder" <!--{if $arrErr.reminder != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                        <option value="" selected="selected"><!--{t string="tpl_Please make a selection_01"}--></option>
                        <!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
                    </select><br />
                    <!--{t string="tpl_Answer:_01"}-->
                    <input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer|h}-->" size="30" class="box30" <!--{if $arrErr.reminder_answer != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Mail magazine<span class='attention'> *</span>_01" escape="none"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.mailmaga_flg}--></span>
                    <span <!--{if $arrErr.mailmaga_flg != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                        <!--{html_radios name="mailmaga_flg" options=$arrMailMagazineType separator=" " selected=$arrForm.mailmaga_flg}-->
                    </span>
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Memo for SHOP_01"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.note}--></span>
                    <textarea name="note" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" <!--{if $arrErr.note != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> cols="60" rows="8" class="area60"><!--{"\n"}--><!--{$arrForm.note|h}--></textarea>
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Points in possession<span class='attention'> *</span>_01" escape="none"}--></th>
                <td>
                    <span class="attention"><!--{$arrErr.point}--></span>
                    <!--{t string="pt_prefix"}--><input type="text" name="point" value="<!--{$arrForm.point|h}-->" maxlength="<!--{$smarty.const.TEL_LEN}-->" size="6" class="box6" <!--{if $arrErr.point != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> <!--{t string="pt_suffix"}-->
                </td>
            </tr>
        </table>

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="return fnReturn();"><span class="btn-prev"><!--{t string="tpl_Return to search screen_01"}--></span></a></li>
                <li><a class="btn-action" href="javascript:;" onclick="fnSetFormSubmit('form1', 'mode', 'confirm'); return false;"><span class="btn-next"><!--{t string="tpl_Confirmation page_01"}--></span></a></li>
            </ul>
        </div>

        <input type="hidden" name="order_id" value="" />
        <input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->">
        <input type="hidden" name="edit_customer_id" value="<!--{$edit_customer_id}-->" >

        <h2><!--{t string="tpl_Purchase history list_01"}--></h2>
        <!--{if $tpl_linemax > 0}-->
        <p><!--購入履歴一覧--><!--{t string="tpl_<span class='attention'>T_ARG1 items</span>&nbsp; were found._01" escape="none" T_ARG1=$tpl_linemax}--></p>

        <!--{include file=$tpl_pager}-->

            <!--{* 購入履歴一覧表示テーブル *}-->
            <table class="list">
                <tr>
                    <th><!--{t string="tpl_Date_01"}--></th>
                    <th><!--{t string="tpl_Order number_01"}--></th>
                    <th><!--{t string="tpl_Purchase amount_01"}--></th>
                    <th><!--{t string="tpl_Shipment date_01"}--></th>
                    <th><!--{t string="tpl_Payment method_01"}--></th>
                </tr>
                <!--{section name=cnt loop=$arrPurchaseHistory}-->
                    <tr>
                        <td><!--{$arrPurchaseHistory[cnt].create_date|sfDispDBDate}--></td>
                        <td class="center"><a href="../order/edit.php?order_id=<!--{$arrPurchaseHistory[cnt].order_id}-->" ><!--{$arrPurchaseHistory[cnt].order_id}--></a></td>
                        <td class="center"><!--{t string="tpl_&#36; T_ARG1_01" escape="none" T_ARG1=$arrPurchaseHistory[cnt].payment_total|number_format}--></td>
                        <td class="center"><!--{if $arrPurchaseHistory[cnt].status eq 5}--><!--{$arrPurchaseHistory[cnt].commit_date|sfDispDBDate}--><!--{else}--><!--{t string="tpl_Not shipped_01"}--><!--{/if}--></td>
                        <td class="center"><!--{$arrPurchaseHistory[cnt].payment_method|h}--></td>
                    </tr>
                <!--{/section}-->
            </table>
            <!--{* 購入履歴一覧表示テーブル *}-->
        <!--{else}-->
            <div class="message"><!--{t string="tpl_There is no purchase history_01"}--></div>
        <!--{/if}-->

    </div>
</form>
