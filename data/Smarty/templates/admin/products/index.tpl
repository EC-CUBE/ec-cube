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
// URLの表示非表示切り替え
function lfnDispChange(){
    inner_id = 'switch';

    cnt = document.form1.item_cnt.value;

    if($('#disp_url1').css("display") == 'none'){
        for (i = 1; i <= cnt; i++) {
            disp_id = 'disp_url'+i;
            $('#' + disp_id).css("display", "");

            disp_id = 'disp_cat'+i;
            $('#' + disp_id).css("display", "none");

            $('#' + inner_id).html('    URL <a href="#" onClick="lfnDispChange();"> &gt;&gt; <!--{t string="tpl_Category display_01"}--><\/a>');
        }
    }else{
        for (i = 1; i <= cnt; i++) {
            disp_id = 'disp_url'+i;
            $('#' + disp_id).css("display", "none");

            disp_id = 'disp_cat'+i;
            $('#' + disp_id).css("display", "");

            $('#' + inner_id).html('    <!--{t string="tpl_Category_01"}--> <a href="#" onClick="lfnDispChange();"> &gt;&gt; <!--{t string="tpl_URL display_01"}--><\/a>');
        }
    }

}

$(function(){
    var dateFormat = $.datepicker.regional['<!--{$smarty.const.LANG_CODE}-->'].dateFormat;

    <!--{if $arrForm.search_startyear.value != '' && $arrForm.search_startmonth.value != '' && $arrForm.search_startday.value != ''}-->
    var search_startyear  = '<!--{$arrForm.search_startyear.value|h}-->';
    var search_startmonth = '<!--{$arrForm.search_startmonth.value|h}-->';
    var search_startday   = '<!--{$arrForm.search_startday.value|h}-->';
    var search_startymd = $.datepicker.formatDate(dateFormat, new Date(search_startyear, search_startmonth - 1, search_startday));
    $("#datepickersearch_start").val(search_startymd);
    // console.log(search_startymd);
    <!--{/if}-->
    <!--{if $arrForm.search_endyear.value != '' && $arrForm.search_endmonth.value != '' && $arrForm.search_endday.value != ''}-->
    var search_endyear  = '<!--{$arrForm.search_endyear.value|h}-->';
    var search_endmonth = '<!--{$arrForm.search_endmonth.value|h}-->';
    var search_endday   = '<!--{$arrForm.search_endday.value|h}-->';
    var search_endymd = $.datepicker.formatDate(dateFormat, new Date(search_endyear, search_endmonth - 1, search_endday));
    $("#datepickersearch_end").val(search_endymd);
    // console.log(search_endymd);
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
        setDatesearch_(year + '/' + month + '/' + day);
	});
	
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
	
	$("#datepickersearch_end").change(function() {
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
<form name="search_form" id="search_form" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <h2><!--{t string="tpl_Search condition settings_01"}--></h2>

    <!--検索条件設定テーブルここから-->
    <table>
        <tr>
            <th><!--{t string="tpl_Product ID_01"}--></th>
            <td colspan="3">
                <!--{assign var=key value="search_product_id"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30"/>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Product code_01"}--></th>
            <td>
                <!--{assign var=key value="search_product_code"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
            <th><!--{t string="tpl_Product name_01"}--></th>
            <td>
                <!--{assign var=key value="search_name"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Category_01"}--></th>
            <td>
                <!--{assign var=key value="search_category_id"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value=""><!--{t string="tpl_Please make a selection_01"}--></option>
                <!--{html_options options=$arrCatList selected=$arrForm[$key].value}-->
                </select>
            </td>
            <th><!--{t string="tpl_Type_01"}--></th>
            <td>
                <!--{assign var=key value="search_status"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <!--{html_checkboxes name="$key" options=$arrDISP selected=$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Registration/update date_01"}--></th>
            <td colspan="3">
                <!--{if $arrErr.search_startyear || $arrErr.search_endyear}-->
                    <span class="attention"><!--{$arrErr.search_startyear}--></span>
                    <span class="attention"><!--{$arrErr.search_endyear}--></span>
                <!--{/if}-->
                <input id="datepickersearch_start"
                       type="text"
                       value="" <!--{if $arrErr.search_startyear != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                <input type="hidden" name="search_startyear" value="<!--{$arrForm.search_startyear.value}-->" />
                <input type="hidden" name="search_startmonth" value="<!--{$arrForm.search_startmonth.value}-->" />
                <input type="hidden" name="search_startday" value="<!--{$arrForm.search_startday.value}-->" />
                <!--{t string="-"}-->
                <input id="datepickersearch_end"
                       type="text"
                       value="" <!--{if $arrErr.search_endyear != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                <input type="hidden" name="search_endyear" value="<!--{$arrForm.search_endyear.value}-->" />
                <input type="hidden" name="search_endmonth" value="<!--{$arrForm.search_endmonth.value}-->" />
                <input type="hidden" name="search_endday" value="<!--{$arrForm.search_endday.value}-->" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Product status_01"}--></th>
            <td colspan="3">
            <!--{assign var=key value="search_product_statuses"}-->
            <span class="attention"><!--{$arrErr[$key]|h}--></span>
            <!--{html_checkboxes name="$key" options=$arrSTATUS selected=$arrForm[$key].value}-->
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
                <!--{html_options options=$arrPageMax selected=$arrForm.search_page_max.value}-->
            </select> 
            <!--{t string="record_suffix"}-->
        </p>

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Search using above criteria_01"}--></span></a></li>
            </ul>
        </div>

    </div>
    <!--検索条件設定テーブルここまで-->
</form>


<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <input type="hidden" name="product_id" value="" />
    <input type="hidden" name="category_id" value="" />
    <!--{foreach key=key item=item from=$arrHidden}-->
        <!--{if is_array($item)}-->
            <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
            <!--{/foreach}-->
        <!--{else}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->
    <h2><!--{t string="tpl_List of search results_01"}--></h2>
    <div class="btn">
        <!--検索結果数--><!--{t string="tpl_<span class='attention'>T_ARG1 items</span>&nbsp; were found._01" escape="none" T_ARG1=$tpl_linemax}-->
        <!--検索結果-->
        <!--{if $smarty.const.ADMIN_MODE == '1'}-->
            <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete_all','',''); return false;"><!--{t string="tpl_Delete all search results_01"}--></a>
        <!--{/if}-->
        <a class="btn-tool" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;"><!--{t string="tpl_CSV download_01"}--></a>
        <a class="btn-tool" href="../contents/csv.php?tpl_subno_csv=product"><!--{t string="tpl_CSV output settings_01"}--></a>
    </div>
    <!--{if count($arrProducts) > 0}-->

        <!--{include file=$tpl_pager}-->

        <!--検索結果表示テーブル-->
        <table class="list" id="products-search-result">
            <col width="8%" />
            <col width="9%" />
            <col width="9%" />
            <col width="8%" />
            <col width="25%" />
            <col width="8%" />
            <col width="8%" />
            <col width="5%" />
            <col width="5%" />
            <col width="5%" />
            <col width="5%" />
            <col width="5%" />
            <tr>
                <th rowspan="2"><!--{t string="tpl_Product ID_01"}--></th>
                <th rowspan="2"><!--{t string="tpl_Product image_01"}--></th>
                <th rowspan="2"><!--{t string="tpl_Product code_01"}--></th>
                <th rowspan="2"><!--{t string="tpl_Price(&#36;)_01" escape="none"}--></th>
                <th><!--{t string="tpl_Product name_01"}--></th>
                <th rowspan="2"><!--{t string="tpl_Inventory_01"}--></th>
                <th rowspan="2"><!--{t string="tpl_Type_01"}--></th>
                <th rowspan="2"><!--{t string="tpl_Edit_01"}--></th>
                <th rowspan="2"><!--{t string="tpl_Confirm_02"}--></th>
                <!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
                <th rowspan="2"><!--{t string="tpl_Standard_01"}--></th>
                <!--{/if}-->
                <th rowspan="2"><!--{t string="tpl_Remove_01"}--></th>
                <th rowspan="2"><!--{t string="tpl_Duplication_01"}--></th>
            </tr>
            <tr>
                <th nowrap><a href="#" onClick="lfnDispChange(); return false;"><!--{t string="tpl_Category <> URL_01"}--></a></th>
            </tr>

            <!--{section name=cnt loop=$arrProducts}-->
                <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
                <!--{assign var=status value="`$arrProducts[cnt].status`"}-->
                <tr style="background:<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->;">
                    <td class="id" rowspan="2"><!--{$arrProducts[cnt].product_id}--></td>
                    <td class="thumbnail" rowspan="2">
                    <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrProducts[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65">            </td>
                    <td rowspan="2"><!--{$arrProducts[cnt].product_code_min|h}-->
                        <!--{if $arrProducts[cnt].product_code_min != $arrProducts[cnt].product_code_max}-->
                            <br /><!--{t string="-"}--> <!--{$arrProducts[cnt].product_code_max|h}-->
                        <!--{/if}-->
                    </td>
                    <!--{* 価格 *}-->
                    <td rowspan="2" class="right">
                        <!--{$arrProducts[cnt].price02_min|number_format}-->
                        <!--{if $arrProducts[cnt].price02_min != $arrProducts[cnt].price02_max}-->
                            <br /><!--{t string="-"}--> <!--{$arrProducts[cnt].price02_max|number_format}-->
                        <!--{/if}-->            </td>
                    <td><!--{$arrProducts[cnt].name|h}--></td>
                    <!--{* 在庫 *}-->
                    <!--{* XXX 複数規格でかつ、全ての在庫数量が等しい場合は先頭に「各」と入れたれたら良いと思う。 *}-->
                    <td class="menu" rowspan="2">
                        <!--{if $arrProducts[cnt].stock_unlimited_min}--><!--{t string="tpl_No limit_01"}--><!--{else}--><!--{$arrProducts[cnt].stock_min|number_format}--><!--{/if}-->
                        <!--{if $arrProducts[cnt].stock_unlimited_min != $arrProducts[cnt].stock_unlimited_max || $arrProducts[cnt].stock_min != $arrProducts[cnt].stock_max}-->
                            <br /><!--{t string="-"}--> <!--{if $arrProducts[cnt].stock_unlimited_max}--><!--{t string="tpl_No limit_01"}--><!--{else}--><!--{$arrProducts[cnt].stock_max|number_format}--><!--{/if}-->
                        <!--{/if}-->            </td>
                    <!--{* 表示 *}-->
                    <!--{assign var=key value=$arrProducts[cnt].status}-->
                    <td class="menu" rowspan="2"><!--{$arrDISP[$key]}--></td>
                    <td class="menu" rowspan="2"><span class="icon_edit"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" ><!--{t string="tpl_Edit_01"}--></a></span></td>
                    <td class="menu" rowspan="2"><span class="icon_confirm"><a href="<!--{$smarty.const.HTTP_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts[cnt].product_id}-->&amp;admin=on" target="_blank"><!--{t string="tpl_Confirm_02"}--></a></span></td>
                    <!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
                    <td class="menu" rowspan="2"><span class="icon_class"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" ><!--{t string="tpl_Standard_01"}--></a></span></td>
                    <!--{/if}-->
                    <td class="menu" rowspan="2"><span class="icon_delete"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnSetFormValue('category_id', '<!--{$arrProducts[cnt].category_id}-->'); fnModeSubmit('delete', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;"><!--{t string="tpl_Remove_01"}--></a></span></td>
                    <td class="menu" rowspan="2"><span class="icon_copy"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnChangeAction('./product.php'); fnModeSubmit('copy', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" ><!--{t string="tpl_Duplication_01"}--></a></span></td>
                </tr>
                <tr style="background:<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->;">
                    <td>
                        <!--{* カテゴリ名 *}-->
                        <div id="disp_cat<!--{$smarty.section.cnt.iteration}-->" style="display:<!--{$cat_flg}-->">
                            <!--{foreach from=$arrProducts[cnt].categories item=category_id name=categories}-->
                                <!--{$arrCatList[$category_id]|sfTrim}-->
                                <!--{if !$smarty.foreach.categories.last}--><br /><!--{/if}-->
                            <!--{/foreach}-->
                        </div>

                        <!--{* URL *}-->
                        <div id="disp_url<!--{$smarty.section.cnt.iteration}-->" style="display:none">
                            <!--{$smarty.const.HTTP_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrProducts[cnt].product_id}-->
                        </div>
                    </td>
                </tr>
                <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
            <!--{/section}-->
        </table>
        <input type="hidden" name="item_cnt" value="<!--{$arrProducts|@count}-->" />
        <!--検索結果表示テーブル-->
    <!--{/if}-->

</form>

<!--★★検索結果一覧★★-->
<!--{/if}-->
</div>
