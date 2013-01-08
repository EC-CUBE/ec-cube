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

    function fnDelete(customer_id) {
        if (confirm('<!--{t string="tpl_248"}-->')) {
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
        if (confirm('<!--{t string="tpl_249"}-->')) {
            document.form1.mode.value = "resend_mail"
            document.form1['edit_customer_id'].value = customer_id;
            document.form1.submit();
            return false;
        }
    }
	
	$(function(){
		$.datepicker.setDefaults( $.datepicker.regional[ "<!--{$smarty.const.LANG_CODE}-->" ] );
		
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
			setDatecustomersearch_b_start(dateText);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_b_start,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_b_start
		});
		
		$("#datepickercustomersearch_b_start").blur( function() {
			var dateText = $(this).val();
			setDatecustomersearch_b_start(dateText);
		});
		
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
			setDatecustomersearch_b_end(dateText);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_b_end,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_b_end
		});
		
		$("#datepickercustomersearch_b_end").blur( function() {
			var dateText = $(this).val();
			setDatecustomersearch_b_end(dateText);
		});
		
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
			setDatecustomersearch_start(dateText);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_start,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_start
		});
		
		$("#datepickercustomersearch_start").blur( function() {
			var dateText = $(this).val();
			setDatecustomersearch_start(dateText);
		});
		
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
			setDatecustomersearch_end(dateText);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_end,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_end
		});
		
		$("#datepickercustomersearch_end").blur( function() {
			var dateText = $(this).val();
			setDatecustomersearch_end(dateText);
		});
		
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
			setDatecustomersearch_buy_start(dateText);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_buy_start,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_buy_start
		});
		
		$("#datepickercustomersearch_buy_start").blur( function() {
			var dateText = $(this).val();
			setDatecustomersearch_buy_start(dateText);
		});
		
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
			setDatecustomersearch_buy_end(dateText);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButtoncustomersearch_buy_end,       
		onChangeMonthYear: showAdditionalButtoncustomersearch_buy_end
		});
		
		$("#datepickercustomersearch_buy_end").blur( function() {
			var dateText = $(this).val();
			setDatecustomersearch_buy_end(dateText);
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

<div id="customer" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />

    <h2><!--{t string="tpl_250"}--></h2>

    <!--検索条件設定テーブルここから-->
    <table class="form">
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`/adminparts/form_customer_search.tpl"}-->
        <tr>
            <th><!--{t string="tpl_209"}--></th>
            <td colspan="3"><!--{html_checkboxes name="search_status" options=$arrStatus separator="&nbsp;" selected=$arrForm.search_status.value}--></td>
        </tr>
    </table>
    <div class="btn">
        <p class="page_rows"><!--{t string="tpl_251"}-->
            <!--{t string="record_prefix"}-->
            <select name="search_page_max">
                <!--{html_options options=$arrPageMax selected=$arrForm.search_page_max}-->
            </select> 
            <!--{t string="record_suffix"}-->
        </p>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_252"}--></span></a></li>
            </ul>
        </div>
    </div>
</form>
<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete' or $smarty.post.mode == 'resend_mail')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="edit_customer_id" value="" />
    <!--{foreach key=key item=item from=$arrHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->

    <h2><!--{t string="tpl_253"}--></h2>
    <div class="btn">
        <!--検索結果数--><!--{t string="tpl_230" T_FIELD=$tpl_linemax}-->
        <!--検索結果-->
        <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;"><!--{t string="tpl_254"}--></a>
        <a class="btn-normal" href="javascript:;" onclick="location.href='../contents/csv.php?tpl_subno_csv=customer'"><!--{t string="tpl_255"}--></a>
    </div>
    <!--{if count($arrData) > 0}-->

    <!--{include file=$tpl_pager}-->

    <!--検索結果表示テーブル-->
    <table class="list" id="customer-search-result">
        <col width="8%" />
        <col width="10%" />
        <col width="30%" />
        <col width="8%" />
        <col width="30%" />
        <col width="7%" />
        <col width="7%" />
        <tr>
            <th rowspan="2"><!--{t string="tpl_256"}--></th>
            <th rowspan="2"><!--{t string="tpl_207"}--></th>
            <th rowspan="2"><!--{t string="tpl_208"}--></th>
            <th rowspan="2"><!--{t string="tpl_215"}--></th>
            <th><!--{t string="tpl_037"}--></th>
            <th rowspan="2"><!--{t string="tpl_003"}--></th>
            <th rowspan="2"><!--{t string="tpl_004"}--></th>
        </tr>
        <tr>
            <th><!--{t string="tpl_108"}--></th>
        </tr>
        <!--{foreach from=$arrData item=row}-->
            <tr>
                <td class="center" rowspan="2"><!--{if $row.status eq 1}--><!--{t string="tpl_260"}--><!--{else}--><!--{t string="tpl_261"}--><!--{/if}--></td>
                <td rowspan="2"><!--{$row.customer_id|h}--></td>
                <td rowspan="2"><!--{$row.name01|h}--> <!--{$row.name02|h}--></td>
                <td class="center" rowspan="2"><!--{$arrSex[$row.sex]|h}--></td>
                <td><!--{$row.tel01|h}-->-<!--{$row.tel02|h}-->-<!--{$row.tel03|h}--></td>
                <td class="center" rowspan="2"><span class="icon_edit"><a href="#" onclick="return fnEdit('<!--{$row.customer_id|h}-->');"><!--{t string="tpl_003"}--></a></span></td>
                <td class="center" rowspan="2"><span class="icon_delete"><a href="#" onclick="return fnDelete('<!--{$row.customer_id|h}-->');"><!--{t string="tpl_004"}--></a></span></td>
            </tr>
            <tr>
                <td><!--{mailto address=$row.email encode="javascript"}--></a><!--{if $row.status eq 1}--><br /><a href="#" onclick="return fnReSendMail('<!--{$row.customer_id|h}-->');"><!--{t string="tpl_259"}--></a><!--{/if}--></td>
            </tr>
        <!--{/foreach}-->
    </table>
    <!--検索結果表示テーブル-->

    <!--{/if}-->
</form>
<!--★★検索結果一覧★★-->

<!--{/if}-->
</div>
