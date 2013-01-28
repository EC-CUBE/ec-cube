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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA    02111-1307, USA.
 */
*}-->

<script type="text/javascript">
<!--

    function fnDelete(customer_id) {
        if (confirm('<!--{t string="tpl_Do you want to delete member information?_01"}-->')) {
            document.form1.mode.value = "delete"
            document.form1['edit_customer_id'].value = customer_id;
            document.form1.submit();
            return false;
        }
    }

    function fnEdit(customer_id) {
        document.form1.action = './edit.php';
        document.form1.mode.value = "edit_search"
        document.form1['edit_customer_id'].value = customer_id;
        document.form1.search_pageno.value = 1;
        document.form1.submit();
        return false;
    }

    function fnReSendMail(customer_id) {
        if (confirm('<!--{t string="tpl_Do you wish to receive a temporary registration e-mail again?_01"}-->')) {
            document.form1.mode.value = "resend_mail"
            document.form1['edit_customer_id'].value = customer_id;
            document.form1.submit();
            return false;
        }
    }
	
	$(function(){
		var dateFormat = $.datepicker.regional['<!--{$smarty.const.LANG_CODE}-->'].dateFormat;

        <!--{if $arrForm.search_b_start_year.value != '' && $arrForm.search_b_start_month.value != '' && $arrForm.search_b_start_day.value != ''}-->
        var search_b_start_year  = '<!--{$arrForm.search_b_start_year.value|h}-->';
        var search_b_start_month = '<!--{$arrForm.search_b_start_month.value|h}-->';
        var search_b_start_day   = '<!--{$arrForm.search_b_start_day.value|h}-->';
        var search_b_start_ymd = $.datepicker.formatDate(dateFormat, new Date(search_b_start_year, search_b_start_month - 1, search_b_start_day));
        $("#datepickercustomersearch_b_start").val(search_b_start_ymd);
        <!--{/if}-->
        
		$( "#datepickercustomersearch_b_start" ).datepicker({
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
            setDatecustomersearch_b_start(year + '/' + month + '/' + day);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_b_start,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_b_start
		});
		
		$("#datepickercustomersearch_b_start").change( function() {
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
            setDatecustomersearch_b_start(year + '/' + month + '/' + day);
		});

        <!--{if $arrForm.search_b_end_year.value != '' && $arrForm.search_b_end_month.value != '' && $arrForm.search_b_end_day.value != ''}-->
        var search_b_end_year  = '<!--{$arrForm.search_b_end_year.value|h}-->';
        var search_b_end_month = '<!--{$arrForm.search_b_end_month.value|h}-->';
        var search_b_end_day   = '<!--{$arrForm.search_b_end_day.value|h}-->';
        var search_b_end_ymd = $.datepicker.formatDate(dateFormat, new Date(search_b_end_year, search_b_end_month - 1, search_b_end_day));
        $("#datepickercustomersearch_b_end").val(search_b_end_ymd);
        <!--{/if}-->

		$( "#datepickercustomersearch_b_end" ).datepicker({
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
            setDatecustomersearch_b_end(year + '/' + month + '/' + day);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_b_end,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_b_end
		});
		
		$("#datepickercustomersearch_b_end").change( function() {
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
            setDatecustomersearch_b_end(year + '/' + month + '/' + day);
		});

        <!--{if $arrForm.search_start_year.value != '' && $arrForm.search_start_month.value != '' && $arrForm.search_start_day.value != ''}-->
        var search_start_year  = '<!--{$arrForm.search_start_year.value|h}-->';
        var search_start_month = '<!--{$arrForm.search_start_month.value|h}-->';
        var search_start_day   = '<!--{$arrForm.search_start_day.value|h}-->';
        var search_start_day_ymd = $.datepicker.formatDate(dateFormat, new Date(search_start_year, search_start_month - 1, search_start_day));
        $("#datepickercustomersearch_start").val(search_start_day_ymd);
        <!--{/if}-->
        
		$( "#datepickercustomersearch_start" ).datepicker({
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
            setDatecustomersearch_start(year + '/' + month + '/' + day);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_start,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_start
		});
		
		$("#datepickercustomersearch_start").change( function() {
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
            setDatecustomersearch_start(year + '/' + month + '/' + day);
		});

        <!--{if $arrForm.search_end_year.value != '' && $arrForm.search_end_month.value != '' && $arrForm.search_end_day.value != ''}-->
        var search_end_year  = '<!--{$arrForm.search_end_year.value|h}-->';
        var search_end_month = '<!--{$arrForm.search_end_month.value|h}-->';
        var search_end_day   = '<!--{$arrForm.search_end_day.value|h}-->';
        var search_end_ymd = $.datepicker.formatDate(dateFormat, new Date(search_end_year, search_end_month - 1, search_end_day));
        $("#datepickercustomersearch_end").val(search_end_ymd);
        <!--{/if}-->
        
		$( "#datepickercustomersearch_end" ).datepicker({
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
            setDatecustomersearch_end(year + '/' + month + '/' + day);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_end,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_end
		});
		
		$("#datepickercustomersearch_end").change( function() {
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
            setDatecustomersearch_end(year + '/' + month + '/' + day);
		});

        <!--{if $arrForm.search_buy_start_year.value != '' && $arrForm.search_buy_start_month.value != '' && $arrForm.search_buy_start_day.value != ''}-->
        var search_buy_start_year  = '<!--{$arrForm.search_buy_start_year.value|h}-->';
        var search_buy_start_month = '<!--{$arrForm.search_buy_start_month.value|h}-->';
        var search_buy_start_day   = '<!--{$arrForm.search_buy_start_day.value|h}-->';
        var search_buy_start_ymd = $.datepicker.formatDate(dateFormat, new Date(search_buy_start_year, search_buy_start_month - 1, search_buy_start_day));
        $("#datepickercustomersearch_buy_start").val(search_buy_start_ymd);
        <!--{/if}-->
        
		$( "#datepickercustomersearch_buy_start" ).datepicker({
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
            setDatecustomersearch_buy_start(year + '/' + month + '/' + day);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_buy_start,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_buy_start
		});
		
		$("#datepickercustomersearch_buy_start").change( function() {
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
            setDatecustomersearch_buy_start(year + '/' + month + '/' + day);
		});

        <!--{if $arrForm.search_buy_end_year.value != '' && $arrForm.search_buy_end_month.value != '' && $arrForm.search_buy_end_day.value != ''}-->
        var search_buy_end_year  = '<!--{$arrForm.search_buy_end_year.value|h}-->';
        var search_buy_end_month = '<!--{$arrForm.search_buy_end_month.value|h}-->';
        var search_buy_end_day   = '<!--{$arrForm.search_buy_end_day.value|h}-->';
        var search_buy_end_ymd = $.datepicker.formatDate(dateFormat, new Date(search_buy_end_year, search_buy_end_month - 1, search_buy_end_day));
        $("#datepickercustomersearch_buy_end").val(search_buy_end_ymd);
        <!--{/if}-->

		$( "#datepickercustomersearch_buy_end" ).datepicker({
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
            setDatecustomersearch_buy_end(year + '/' + month + '/' + day);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_buy_end,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_buy_end
		});
		
		$("#datepickercustomersearch_buy_end").change( function() {
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
            setDatecustomersearch_buy_end(year + '/' + month + '/' + day);
		});
		
	});
	
	var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');
	
	var showAdditionalButtoncustomersearch_b_start = function (input) {
		setTimeout(function () {
			var buttonPane = $(input)
					 .datepicker("widget")
					 .find(".ui-datepicker-buttonpane");
			btn
					.unbind("click")
					.bind("click", function () {
						$.datepicker._clearDate(input);
						$("*[name=search_b_start_year]").val("");
						$("*[name=search_b_start_month]").val("");
						$("*[name=search_b_start_day]").val("");
					});
			btn.appendTo(buttonPane);
		}, 1);
	};
	
	var showAdditionalButtoncustomersearch_b_end = function (input) {
		setTimeout(function () {
			var buttonPane = $(input)
					 .datepicker("widget")
					 .find(".ui-datepicker-buttonpane");
			btn
					.unbind("click")
					.bind("click", function () {
						$.datepicker._clearDate(input);
						$("*[name=search_b_end_year]").val("");
						$("*[name=search_b_end_month]").val("");
						$("*[name=search_b_end_day]").val("");
					});
			btn.appendTo(buttonPane);
		}, 1);
	};
	
	var showAdditionalButtoncustomersearch_start = function (input) {
		setTimeout(function () {
			var buttonPane = $(input)
					 .datepicker("widget")
					 .find(".ui-datepicker-buttonpane");
			btn
					.unbind("click")
					.bind("click", function () {
						$.datepicker._clearDate(input);
						$("*[name=search_start_year]").val("");
						$("*[name=search_start_month]").val("");
						$("*[name=search_start_day]").val("");
					});
			btn.appendTo(buttonPane);
		}, 1);
	};
	
	var showAdditionalButtoncustomersearch_end = function (input) {
		setTimeout(function () {
			var buttonPane = $(input)
					 .datepicker("widget")
					 .find(".ui-datepicker-buttonpane");
			btn
					.unbind("click")
					.bind("click", function () {
						$.datepicker._clearDate(input);
						$("*[name=search_end_year]").val("");
						$("*[name=search_end_month]").val("");
						$("*[name=search_end_day]").val("");
					});
			btn.appendTo(buttonPane);
		}, 1);
	};
	
	var showAdditionalButtoncustomersearch_buy_start = function (input) {
		setTimeout(function () {
			var buttonPane = $(input)
					 .datepicker("widget")
					 .find(".ui-datepicker-buttonpane");
			btn
					.unbind("click")
					.bind("click", function () {
						$.datepicker._clearDate(input);
						$("*[name=search_buy_start_year]").val("");
						$("*[name=search_buy_start_month]").val("");
						$("*[name=search_buy_start_day]").val("");
					});
			btn.appendTo(buttonPane);
		}, 1);
	};
	
	var showAdditionalButtoncustomersearch_buy_end = function (input) {
		setTimeout(function () {
			var buttonPane = $(input)
					 .datepicker("widget")
					 .find(".ui-datepicker-buttonpane");
			btn
					.unbind("click")
					.bind("click", function () {
						$.datepicker._clearDate(input);
						$("*[name=search_buy_end_year]").val("");
						$("*[name=search_buy_end_month]").val("");
						$("*[name=search_buy_end_day]").val("");
					});
			btn.appendTo(buttonPane);
		}, 1);
	};
	
	function setDatecustomersearch_b_start(dateText){
	var dates = dateText.split('/');
	$("*[name=search_b_start_year]").val(dates[0]);
	$("*[name=search_b_start_month]").val(dates[1]);
	$("*[name=search_b_start_day]").val(dates[2]);
	}
	
	function setDatecustomersearch_b_end(dateText){
	var dates = dateText.split('/');
	$("*[name=search_b_end_year]").val(dates[0]);
	$("*[name=search_b_end_month]").val(dates[1]);
	$("*[name=search_b_end_day]").val(dates[2]);
	}
	
	function setDatecustomersearch_start(dateText){
	var dates = dateText.split('/');
	$("*[name=search_start_year]").val(dates[0]);
	$("*[name=search_start_month]").val(dates[1]);
	$("*[name=search_start_day]").val(dates[2]);
	}
	
	function setDatecustomersearch_end(dateText){
	var dates = dateText.split('/');
	$("*[name=search_end_year]").val(dates[0]);
	$("*[name=search_end_month]").val(dates[1]);
	$("*[name=search_end_day]").val(dates[2]);
	}
	
	function setDatecustomersearch_buy_start(dateText){
	var dates = dateText.split('/');
	$("*[name=search_buy_start_year]").val(dates[0]);
	$("*[name=search_buy_start_month]").val(dates[1]);
	$("*[name=search_buy_start_day]").val(dates[2]);
	}
	
	function setDatecustomersearch_buy_end(dateText){
	var dates = dateText.split('/');
	$("*[name=search_buy_end_year]").val(dates[0]);
	$("*[name=search_buy_end_month]").val(dates[1]);
	$("*[name=search_buy_end_day]").val(dates[2]);
	}

//-->
</script>

<div id="mail" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2><!--{t string="tpl_Delivery search conditions_01"}--></h2>

    <!--{* 検索条件設定テーブルここから *}-->
    <table>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`/adminparts/form_customer_search.tpl"}-->
        <tr>
            <th><!--{t string="tpl_Delivery format_01"}--></th>
            <td colspan="3">
                <!--{assign var=key value="search_htmlmail"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
                <!--{html_radios name=$key options=$arrHtmlmail separator="&nbsp;" selected=$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Type of delivery e-mail address_01"}--></th>
            <td colspan="3">
                <!--{assign var=key value="search_mail_type"}-->
                <!--{html_radios name=$key options=$arrMailType separator="<br />" selected=$arrForm[$key].value|default:1}-->
            </td>
        </tr>
    </table>
    <!--{* 検索条件設定テーブルここまで *}-->

    <div class="btn">
        <p class="page_rows"><!--{t string="tpl_Results displayed_01"}-->
            <!--{assign var=key value="search_page_max"}-->
            <!--{t string="record_prefix"}-->
            <select name="<!--{$key}-->">
                <!--{html_options options=$arrPageRows selected=$arrForm[$key]}-->
            </select> 
            <!--{t string="record_suffix"}-->
        </p>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Search using above criteria_01"}--></span></a></li>
            </ul>
        </div>
    </div>
</form>


<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete' or $smarty.post.mode == 'back')}-->

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<!--{foreach key=key item=item from=$arrHidden}-->
<!--{if is_array($item)}-->
    <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key}-->[]" value="<!--{$c_item|h}-->" />
    <!--{/foreach}-->
<!--{else}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/if}-->
<!--{/foreach}-->

    <h2><!--{t string="tpl_List of search results_01"}--></h2>
    <div class="btn">
        <!--検索結果数--><!--{t string="tpl_<span class='attention'>T_ARG1 items</span>&nbsp; were found._01" escape="none" T_ARG1=$tpl_linemax}-->
        <!--{if $smarty.const.ADMIN_MODE == '1'}-->
            <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete_all','',''); return false;"><span><!--{t string="tpl_Delete all search results_01"}--></span></a>
        <!--{/if}-->
        <!--{if $tpl_linemax > 0}-->
            <a class="btn-normal" href="javascript:;" onclick="document.form1['mode'].value='input'; document.form1.submit(); return false;"><span><!--{t string="tpl_Set delivery contents_01"}--></span></a>
        <!--{/if}-->
    </div>
    <!--{if count($arrResults) > 0}-->

    <!--{include file=$tpl_pager}-->

    <!--検索結果表示テーブル-->
    <table class="list">
    <col width="10%" />
    <col width="25%" />
    <col width="35%" />
    <col width="15%" />
    <col width="15%" />
        <tr>
            <th><!--{t string="tpl_Member ID_01"}--></th>
            <th><!--{t string="tpl_Name_03"}--></th>
            <th><!--{t string="tpl_E-mail address_01"}--></th>
            <th><!--{t string="tpl_Desired delivery_01"}--></th>
            <th><!--{t string="tpl_Registration/update date_01"}--></th>
        </tr>
        <!--{section name=i loop=$arrResults}-->
        <tr>
            <td class="center"><!--{$arrResults[i].customer_id}--></td>
            <td><!--{$arrResults[i].name01|h}--> <!--{$arrResults[i].name02|h}--></td>
            <td><!--{$arrResults[i].email|h}--></td>
            <!--{assign var="key" value="`$arrResults[i].mailmaga_flg`"}-->
            <td class="center"><!--{$arrHtmlmail[$key]}--></td>
            <td class="center"><!--{$arrResults[i].update_date|sfDispDBDate}--></td>
        </tr>
        <!--{/section}-->
    </table>
    <!--検索結果表示テーブル-->
    <!--{/if}-->

</form>

<!--{/if}-->
</div>
