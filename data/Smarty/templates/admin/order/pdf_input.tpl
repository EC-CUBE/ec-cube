<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
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

function lfPopwinSubmit(formName) {
    win02('about:blank','pdf','1000','900');
    document[formName].target = "pdf";
    document[formName].submit();
    return false;
}

	$(function(){
        //console.log(ymd);
        var dateFormat = $.datepicker.regional['<!--{$smarty.const.LANG_CODE}-->'].dateFormat;

        <!--{if $arrForm.year != '' && $arrForm.month != '' && $arrForm.day != ''}-->
        var year  = '<!--{$arrForm.year|h}-->';
        var month = '<!--{$arrForm.month|h}-->';
        var day   = '<!--{$arrForm.day|h}-->';
        var ymd = $.datepicker.formatDate(dateFormat, new Date(year, month - 1, day));
        $("#datepicker").val(ymd);
        //console.log(ymd);
        <!--{/if}-->

		$( "#datepicker" ).datepicker({
		beforeShowDay: function(date) {
			if(date.getDay() == 0) {
				return [true,"date-sunday"];
			} else if(date.getDay() == 6){
				return [true,"date-saturday"];
			} else {
				return [true];
			}
		}
        ,changeMonth: 'true'
		,changeYear: 'true'
		,onSelect: function(dateText, inst){
            var year  = inst.selectedYear;
            var month = inst.selectedMonth + 1;
            var day   = inst.selectedDay;
			setDate(year + '/' + month + '/' + day);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButton,
		onChangeMonthYear: showAdditionalButton
		});

		$("#datepicker").change( function() {
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
            setDate(year + '/' + month + '/' + day);
		});

	});

	var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');

	var showAdditionalButton = function (input) {
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

    function setDate(dateText){
        var dates = dateText.split('/');
        $("*[name=year]").val(dates[0]);
        $("*[name=month]").val(dates[1]);
        $("*[name=day]").val(dates[2]);
    }

//-->
</script>

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="confirm" />
<!--{foreach from=$arrForm.order_id item=order_id}-->
    <input type="hidden" name="order_id[]" value="<!--{$order_id|h}-->">
<!--{/foreach}-->

<h2><!--コンテンツタイトル--><!--{t string="tpl_Ledger creation_01"}--></h2>

<table class="form">
    <col width="20%" />
    <col width="80%" />
    <tr>
        <th><!--{t string="tpl_Order number_01"}--></th>
        <td><!--{$arrForm.order_id|@join:', '}--></td>
    </tr>
    <tr>
        <th><!--{t string="tpl_Issuance date<span class='attention'> *</span>_01" escape="none"}--></th>
        <td><!--{if $arrErr.year}--><span class="attention"><!--{$arrErr.year}--></span><!--{/if}-->
            <input id="datepicker"
                   type="text"
                   value="" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
            <input type="hidden" name="year" value="<!--{$arrForm.year|h}-->" />
            <input type="hidden" name="month" value="<!--{$arrForm.month|h}-->" />
            <input type="hidden" name="day" value="<!--{$arrForm.day|h}-->" />
        </td>
    </tr>
    <tr>
        <th><!--{t string="tpl_Ledger type_01"}--></th>
        <td><!--{if $arrErr.download}--><span class="attention"><!--{$arrErr.download}--></span><!--{/if}-->
            <select name="type">
            <!--{html_options options=$arrType selected=$arrForm.type}-->
            </select>
        </td>
    </tr>
    <tr>
        <th><!--{t string="tpl_Download method_01"}--></th>
        <td><!--{if $arrErr.download}--><span class="attention"><!--{$arrErr.download}--></span><!--{/if}-->
            <select name="download">
            <!--{html_options options=$arrDownload selected=$arrForm.download}-->
            </select>
        </td>
    </tr>
    <tr>
        <th><!--{t string="tpl_Ledger title_01"}--></th>
        <td><!--{if $arrErr.title}--><span class="attention"><!--{$arrErr.title}--></span><!--{/if}-->
            <input type="text" name="title" size="40" value="<!--{$arrForm.title}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
            <span style="font-size: 80%;"><!--{t string="tpl_* The default title is displayed when field is blank._01"}--></span><br />
        </td>
    </tr>
    <tr>
        <th><!--{t string="tpl_Ledger message_01"}--></th>
        <td><!--{if $arrErr.msg1}--><span class="attention"><!--{$arrErr.msg1}--></span><!--{/if}-->
            <!--{t string="tpl_1st line:_01"}--><input type="text" name="msg1" size="40" value="<!--{$arrForm.msg1|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
            <!--{if $arrErr.msg2}--><span class="attention"><!--{$arrErr.msg1}--></span><!--{/if}-->
            <!--{t string="tpl_2nd line:_01"}--><input type="text" name="msg2" size="40" value="<!--{$arrForm.msg2|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
            <!--{if $arrErr.msg3}--><span class="attention"><!--{$arrErr.msg3}--></span><!--{/if}-->
            <!--{t string="tpl_3rd line:_01"}--><input type="text" name="msg3" size="40" value="<!--{$arrForm.msg3|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
            <span style="font-size: 80%;"><!--{t string="tpl_* The default message is displayed when fields are blank. _01"}--></span><br />
        </td>
    </tr>
    <tr>
        <th><!--{t string="tpl_Remarks_01"}--></th>
        <td>
            <!--{t string="tpl_1st line:_01"}--><input type="text" name="etc1" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
            <!--{if $arrErr.etc2}--><span class="attention"><!--{$arrErr.msg1}--></span><!--{/if}-->
            <!--{t string="tpl_2nd line:_01"}--><input type="text" name="etc2" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
            <!--{if $arrErr.etc3}--><span class="attention"><!--{$arrErr.msg3}--></span><!--{/if}-->
            <!--{t string="tpl_3rd line:_01"}--><input type="text" name="etc3" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
            <span style="font-size: 80%;"><!--{t string="tpl_* Not displayed when fields are blank._01"}--></span><br />
        </td>
    </tr>
    <!--{if $smarty.const.USE_POINT !== false}-->
        <tr>
            <th><!--{t string="tpl_Point notation_01"}--></th>
            <td>
                <label><input type="radio" name="disp_point" value="1" checked="checked" /><!--{t string="tpl_Yes_01"}--></label>&nbsp;<label><input type="radio" name="disp_point" value="0" /><!--{t string="tpl_No_01"}--></label><br />
                <span style="font-size: 80%;"><!--{t string="tpl_* Even if 'Yes' is selected, it is not displayed unless the customer is a member._01"}--></span>
            </td>
        </tr>
    <!--{else}-->
        <input type="hidden" name="disp_point" value="0" />
    <!--{/if}-->
</table>

<div class="btn-area">
    <ul>
        <li><a class="btn-action" href="javascript:;" onclick="return lfPopwinSubmit('form1');"><span class="btn-next"><!--{t string="tpl_Create using these contents_01"}--></span></a></li>
    </ul>
</div>

</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
