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
$(function(){
	var dateFormat = $.datepicker.regional['<!--{$smarty.const.LANG_CODE}-->'].dateFormat;

    <!--{if $arrForm.search_startyear != '' && $arrForm.search_startmonth != '' && $arrForm.search_startday != ''}-->
    var search_startyear  = '<!--{$arrForm.search_startyear|h}-->';
    var search_startmonth = '<!--{$arrForm.search_startmonth|h}-->';
    var search_startday   = '<!--{$arrForm.search_startday|h}-->';
    var search_start_ymd = $.datepicker.formatDate(dateFormat, new Date(search_startyear, search_startmonth - 1, search_startday));
    $("#datepickersearch_start").val(search_start_ymd);
    <!--{/if}-->

	$( "#datepickersearch_start" ).datepicker({
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
        setDatesearch_start(year + '/' + month + '/' + day);
	},
	showButtonPanel: true,
	beforeShow: showAdditionalButtonsearch_start,       
	onChangeMonthYear: showAdditionalButtonsearch_start
	});
	
	$("#datepickersearch_start").change( function() {
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
        setDatesearch_start(year + '/' + month + '/' + day);
	});

    <!--{if $arrForm.search_endyear != '' && $arrForm.search_endmonth != '' && $arrForm.search_endday != ''}-->
    var search_endyear  = '<!--{$arrForm.search_endyear|h}-->';
    var search_endmonth = '<!--{$arrForm.search_endmonth|h}-->';
    var search_endday   = '<!--{$arrForm.search_endday|h}-->';
    var search_end_ymd = $.datepicker.formatDate(dateFormat, new Date(search_endyear, search_endmonth - 1, search_endday));
    $("#datepickersearch_end").val(search_end_ymd);
    <!--{/if}-->
    
	$( "#datepickersearch_end" ).datepicker({
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
        setDatesearch_end(year + '/' + month + '/' + day);
	},
	showButtonPanel: true,
	beforeShow: showAdditionalButtonsearch_end,       
	onChangeMonthYear: showAdditionalButtonsearch_end
	});
	
	$("#datepickersearch_end").change( function() {
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
        setDatesearch_end(year + '/' + month + '/' + day);
	});

});

var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">Clear</button>');

var showAdditionalButtonsearch_start = function (input) {
	setTimeout(function () {
		var buttonPane = $(input)
				 .datepicker("widget")
				 .find(".ui-datepicker-buttonpane");
		btn
				.unbind("click")
				.bind("click", function () {
					$.datepicker._clearDate(input);
					$("*[name=search_startyear]").val("");
					$("*[name=search_startmonth]").val("");
					$("*[name=search_startday]").val("");
				});
		btn.appendTo(buttonPane);
	}, 1);
};

var showAdditionalButtonsearch_end = function (input) {
	setTimeout(function () {
		var buttonPane = $(input)
				 .datepicker("widget")
				 .find(".ui-datepicker-buttonpane");
		btn
				.unbind("click")
				.bind("click", function () {
					$.datepicker._clearDate(input);
					$("*[name=search_endyear]").val("");
					$("*[name=search_endmonth]").val("");
					$("*[name=search_endday]").val("");
				});
		btn.appendTo(buttonPane);
	}, 1);
};

function setDatesearch_start(dateText){
var dates = dateText.split('/');
$("*[name=search_startyear]").val(dates[0]);
$("*[name=search_startmonth]").val(dates[1]);
$("*[name=search_startday]").val(dates[2]);
}

function setDatesearch_end(dateText){
var dates = dateText.split('/');
$("*[name=search_endyear]").val(dates[0]);
$("*[name=search_endmonth]").val(dates[1]);
$("*[name=search_endday]").val(dates[2]);
}
</script>

<div id="products" class="contents-main">
<form name="search_form" method="post" action="?" >
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2><!--{t string="tpl_Search condition settings_01"}--></h2>

    <!--検索条件設定テーブルここから-->
    <table>
        <tr>
            <th><!--{t string="tpl_Poster name_01"}--></th>
            <td><input type="text" name="search_reviewer_name" value="<!--{$arrForm.search_reviewer_name|h}-->" size="30" class="box30" /></td>
            <th><!--{t string="tpl_Poster URL_01"}--></th>
            <td><input type="text" name="search_reviewer_url" value="<!--{$arrForm.search_reviewer_url|h}-->" size="30" class="box30" /></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Product name_01"}--></th>
            <td><input type="text" name="search_name" value="<!--{$arrForm.search_name|h}-->" size="30" class="box30" /></td>
            <th><!--{t string="tpl_Product code_01"}--></th>
            <td><input type="text" name="search_product_code" value="<!--{$arrForm.search_product_code|h}-->" size="30" class="box30" /></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Gender_01"}--></th>
            <!--{assign var=key value=search_sex}-->
            <td><!--{html_checkboxes name="$key" options=$arrSex selected=$arrForm[$key]}--></td>
            <th><!--{t string="tpl_Recommendation level_01"}--></th>
            <td>
                <!--{assign var=key value=search_recommend_level}-->
                <select name="<!--{$key}-->">
                    <option value="" selected="selected"><!--{t string="tpl_Please make a selection_01"}--></option>
                    <!--{html_options options=$arrRECOMMEND selected=$arrForm[$key].value|h}-->
                </select>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Post date_01"}--></th>
            <td colspan="3">
                <!--{if $arrErr.search_startyear || $arrErr.search_endyear}-->
                    <span class="attention"><!--{$arrErr.search_startyear}--></span>
                    <span class="attention"><!--{$arrErr.search_endyear}--></span>
                <!--{/if}-->
                <input id="datepickersearch_start"
                       type="text"
                       value="" <!--{if $arrErr.search_startyear != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                <input type="hidden" name="search_startyear" value="<!--{$arrForm.search_startyear|h}-->" />
                <input type="hidden" name="search_startmonth" value="<!--{$arrForm.search_startmonth|h}-->" />
                <input type="hidden" name="search_startday" value="<!--{$arrForm.search_startday|h}-->" />
                <!--{t string="-"}-->
                <input id="datepickersearch_end"
                       type="text"
                       value="" <!--{if $arrErr.search_endyear != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                <input type="hidden" name="search_endyear" value="<!--{$arrForm.search_endyear|h}-->" />
                <input type="hidden" name="search_endmonth" value="<!--{$arrForm.search_endmonth|h}-->" />
                <input type="hidden" name="search_endday" value="<!--{$arrForm.search_endday|h}-->" />
            </td>
        </tr>
    </table>

    <div class="btn">
        <p class="page_rows"><!--{t string="tpl_Results displayed_01"}-->
            <!--{assign var=key value="search_page_max"}-->
            <!--{if $arrErr[$key]}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <!--{t string="record_prefix"}-->
            <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <!--{html_options options=$arrPageMax selected=$arrForm.search_page_max|h}-->
            </select> 
            <!--{t string="record_suffix"}-->
        </p>
        <div class="btn-area">
            <ul>
                <li>
                    <a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Search using above criteria_01"}--></span></a></li>
            </ul>
        </div>
    </div>
</form>


<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <input type="hidden" name="review_id" value="" />
    <input type="hidden" name="search_pageno" value="<!--{$tpl_pageno|h}-->" />
    <!--{foreach key=key item=item from=$arrHidden}-->
        <!--{if $key ne "search_pageno"}-->
            <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->
    <h2><!--{t string="tpl_List of search results_01"}--></h2>
    <div class="btn">
        <!--検索結果数--><!--{t string="tpl_<span class='attention'>T_ARG1 items</span>&nbsp; were found._01" escape="none" T_ARG1=$tpl_linemax}-->
        <!--{if $smarty.const.ADMIN_MODE == '1'}-->
            <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete_all','',''); return false;"><span><!--{t string="tpl_Delete all search results_01"}--></span></a>
        <!--{/if}-->
        <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;"><span><!--{t string="tpl_CSV download_01"}--></span></a>
    </div>
    <!--{if $arrReview > 0 & $tpl_linemax > 0}-->

        <!--{include file=$tpl_pager}-->

        <!--検索結果表示テーブル-->
        <table id="products-review-result" class="list">
            <tr>
                <th><!--{t string="tpl_Post date_01"}--></th>
                <th><!--{t string="tpl_Poster name_01"}--></th>
                <th><!--{t string="tpl_Product name_01"}--></th>
                <th><!--{t string="tpl_Recommendation level_01"}--></th>
                <th><!--{t string="tpl_Display/Not display_01"}--></th>
                <th class="edit"><!--{t string="tpl_Edit_01"}--></th>
                <th class="delete"><!--{t string="tpl_Remove_01"}--></th>
            </tr>

            <!--{section name=cnt loop=$arrReview}-->
                <tr>
                    <td><!--{$arrReview[cnt].create_date|h|sfDispDBDate}--></td>
                    <td><!--{$arrReview[cnt].reviewer_name|h}--></td>
                    <td><!--{$arrReview[cnt].name|h}--></td>
                    <!--{assign var=key value="`$arrReview[cnt].recommend_level`"}-->
                    <td><!--{$arrRECOMMEND[$key]|h}--></td>
                    <td class="menu"><!--{if $arrReview[cnt].status eq 1}--><!--{t string="tpl_Display_01"}--><!--{elseif $arrReview[cnt].status eq 2}--><!--{t string="tpl_Not displayed_01"}--><!--{/if}--></td>
                    <td class="menu"><a href="javascript:;" onclick="fnChangeAction('./review_edit.php'); fnModeSubmit('','review_id','<!--{$arrReview[cnt].review_id}-->'); return false;"><!--{t string="tpl_Edit_01"}--></a></td>
                    <td class="menu"><a href="javascript:;" onclick="fnModeSubmit('delete','review_id','<!--{$arrReview[cnt].review_id}-->'); return false;"><!--{t string="tpl_Remove_01"}--></a></td>
                </tr>
            <!--{/section}-->
        </table>
        <!--検索結果表示テーブル-->
    <!--{/if}-->
</form>
<!--{/if}-->
<!--★★検索結果一覧★★-->
</div>
