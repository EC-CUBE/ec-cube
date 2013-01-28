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
$(function(){
        var dateFormat = $.datepicker.regional['<!--{$smarty.const.LANG_CODE}-->'].dateFormat;
        
        <!--{if $arrForm.month != '' && $arrForm.day != ''}-->
        var year  = new Date().getFullYear();
        var month = '<!--{$arrForm.month|h}-->';
        var day   = '<!--{$arrForm.day|h}-->';
        var ymd = $.datepicker.formatDate(dateFormat, new Date(year, month - 1, day));
        $("#datepicker").val(ymd);
        // console.log(ymd);
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
		},changeMonth: 'true'
		,changeYear: 'false'
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
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="holiday_id" value="<!--{$tpl_holiday_id}-->" />
<div id="basis" class="contents-main">

    <table class="form">
        <tr>
            <th><!--{t string="tpl_Title<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{if $arrErr.title}--><span class="attention"><!--{$arrErr.title}--></span><!--{/if}-->
                <input type="text" name="title" value="<!--{$arrForm.title|h}-->" maxlength="<!--{$smarty.const.SMTEXT_LEN}-->" style="" size="60" class="box60"/>
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.SMTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Date<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{if $arrErr.date || $arrErr.month || $arrErr.day}-->
                <span class="attention"><!--{$arrErr.date}--></span>
                <span class="attention"><!--{$arrErr.month}--></span>
                <span class="attention"><!--{$arrErr.day}--></span>
                <!--{/if}-->
                <input id="datepicker" type="text" value="" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                <input type="hidden" name="month" value="<!--{$arrForm.month}-->" />
                <input type="hidden" name="day" value="<!--{$arrForm.day}-->" />
                <br />
                <span class="attention"><!--{t string="tpl_Compensating holidays are not automatically set. Set the dates for compensating holidays._01"}--></span>
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>

    <table class="list">
        <col width="50%" />
        <col width="20%" />
        <col width="10%" />
        <col width="10%" />
        <col width="20%" />
        <tr>
            <th><!--{t string="tpl_Title_01"}--></th>
            <th><!--{t string="tpl_Date_01"}--></th>
            <th class="edit"><!--{t string="tpl_Edit_01"}--></th>
            <th class="delete"><!--{t string="tpl_Remove_01"}--></th>
            <th><!--{t string="tpl_Move_01"}--></th>
        </tr>
        <!--{section name=cnt loop=$arrHoliday}-->
        <tr style="background:<!--{if $tpl_holiday_id != $arrHoliday[cnt].holiday_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;">
            <!--{assign var=holiday_id value=$arrHoliday[cnt].holiday_id}-->
            <td><!--{$arrHoliday[cnt].title|h}--></td>
            <td><!--{t string="tpl_T_ARG1/T_ARG2_01" T_ARG1=$arrHoliday[cnt].month|h T_ARG2=$arrHoliday[cnt].day|h}--></td>
            <td class="center">
                <!--{if $tpl_holiday_id != $arrHoliday[cnt].holiday_id}-->
                <a href="?" onclick="fnModeSubmit('pre_edit', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;"><!--{t string="tpl_Edit_01"}--></a>
                <!--{else}-->
                <!--{t string="tpl_being edited_01"}-->
                <!--{/if}-->
            </td>
            <td class="center">
                <!--{if $arrClassCatCount[$class_id] > 0}-->
                -
                <!--{else}-->
                <a href="?" onclick="fnModeSubmit('delete', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;"><!--{t string="tpl_Remove_01"}--></a>
                <!--{/if}-->
            </td>
            <td class="center">
                <!--{if $smarty.section.cnt.iteration != 1}-->
                <a href="?" onclick="fnModeSubmit('up', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;" /><!--{t string="tpl_To top_01"}--></a>
                <!--{/if}-->
                <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                <a href="?" onclick="fnModeSubmit('down', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;" /><!--{t string="tpl_To bottom_01"}--></a>
                <!--{/if}-->
            </td>
        </tr>
        <!--{/section}-->
    </table>

</div>
</form>
