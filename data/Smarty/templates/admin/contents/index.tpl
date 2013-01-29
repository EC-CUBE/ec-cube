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

function func_regist(url) {
    res = confirm('<!--{if $edit_mode eq "on"}--><!--{t string="tpl_Do you want to edit with these contents?_01"}--><!--{else}--><!--{t string="tpl_Register and continue?_01"}--><!--{/if}-->');
    if(res == true) {
        document.form1.mode.value = 'regist';
        document.form1.submit();
        return false;
    }
    return false;
}

function func_edit(news_id) {
    document.form1.mode.value = "search";
    document.form1.news_id.value = news_id;
    document.form1.submit();
}

function func_del(news_id) {
    res = confirm('<!--{t string="tpl_Do you want to delete this new information?_01"}-->');
    if(res == true) {
        document.form1.mode.value = "delete";
        document.form1.news_id.value = news_id;
        document.form1.submit();
    }
    return false;
}

function func_rankMove(term,news_id) {
    document.form1.mode.value = "move";
    document.form1.news_id.value = news_id;
    document.form1.term.value = term;
    document.form1.submit();
}

function moving(news_id,rank, max_rank) {

    var val;
    var ml;
    var len;

    ml = document.move;
    len = document.move.elements.length;
    j = 0;
    for( var i = 0 ; i < len ; i++) {
            if ( ml.elements[i].name == 'position' && ml.elements[i].value != "" ) {
            val = ml.elements[i].value;
            j ++;
            }
    }

    if ( j > 1) {
        alert( '<!--{t string="tpl_Enter a single move ranking._01"}-->' );
        return false;
    } else if( ! val ) {
        alert( '<!--{t string="tpl_Enter a move ranking._01"}-->' );
        return false;
    } else if( val.length > 4){
        alert( '<!--{t string="tpl_Enter a move ranking that is 4 digits or less._01"}-->' );
        return false;
    } else if( val.match(/[0-9]+/g) != val){
        alert( '<!--{t string="tpl_Enter a number for the move ranking._01"}-->' );
        return false;
    } else if( val == rank ){
        alert( '<!--{t string="The number to be moved is a duplicate._01"}-->' );
        return false;
    } else if( val == 0 ){
        alert( '<!--{t string="tpl_Enter 0 or greater for the move ranking_01"}-->' );
        return false;
    } else if( val > max_rank ){
        alert( '<!--{t string="tpl_The order that was entered exceeds the maximum valued for the number registered._01"}-->' );
        return false;
    } else {
        ml.moveposition.value = val;
        ml.rank.value = rank;
        ml.news_id.value = news_id;
        ml.submit();
        return false;
    }
}

	$(function(){
        var dateFormat = $.datepicker.regional['<!--{$smarty.const.LANG_CODE}-->'].dateFormat;

        <!--{if $arrForm.year != '' && $arrForm.month != '' && $arrForm.day != ''}-->
        var year  = '<!--{$arrForm.year|h}-->';
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


<div id="admin-contents" class="contents-main">
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="news_id" value="<!--{$arrForm.news_id|h}-->" />
<input type="hidden" name="term" value="" />
    <!--{* ▼登録テーブルここから *}-->
    <table>
        <tr>
            <th><!--{t string="tpl_Date<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{if $arrErr.year || $arrErr.month || $arrErr.day}--><span class="attention"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></span><!--{/if}-->
                <input id="datepicker"
                       type="text"
                       value="" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                <input type="hidden" name="year" value="<!--{$arrForm.year|h}-->" />
                <input type="hidden" name="month" value="<!--{$arrForm.month|h}-->" />
                <input type="hidden" name="day" value="<!--{$arrForm.day|h}-->" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Title<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{if $arrErr.news_title}--><span class="attention"><!--{$arrErr.news_title}--></span><!--{/if}-->
                <textarea name="news_title" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" <!--{if $arrErr.news_title}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}-->><!--{"\n"}--><!--{$arrForm.news_title|h}--></textarea><br />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.MTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_URL_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.news_url}--></span>
                <input type="text" name="news_url" size="60" class="box60"    value="<!--{$arrForm.news_url|h}-->" <!--{if $arrErr.news_url}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}--> maxlength="<!--{$smarty.const.URL_LEN}-->" />
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.URL_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Link_01"}--></th>
            <td><label><input type="checkbox" name="link_method" value="2" <!--{if $arrForm.link_method eq 2}--> checked <!--{/if}--> /> <!--{t string="tpl_Open in separate window_01"}--></label></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Create text_01"}--></th>
            <td>
                <!--{if $arrErr.news_comment}--><span class="attention"><!--{$arrErr.news_comment}--></span><!--{/if}-->
                <textarea name="news_comment" cols="60" rows="8" wrap="soft" class="area60" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" style="background-color:<!--{if $arrErr.news_comment}--><!--{$smarty.const.ERR_COLOR|h}--><!--{/if}-->"><!--{"\n"}--><!--{$arrForm.news_comment|h}--></textarea><br />
                <span class="attention"> <!--{t string="tpl_(3000 characters max)_01"}--></span>
            </td>
        </tr>
    </table>
    <!--{* ▲登録テーブルここまで *}-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="return func_regist();"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
</form>

    <h2><!--{t string="tpl_List of new information_01"}--></h2>
    <!--{if $arrErr.moveposition}-->
    <p><span class="attention"><!--{$arrErr.moveposition}--></span></p>
    <!--{/if}-->
    <!--{* ▼一覧表示エリアここから *}-->
    <form name="move" id="move" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="moveRankSet" />
    <input type="hidden" name="term" value="setposition" />
    <input type="hidden" name="news_id" value="" />
    <input type="hidden" name="moveposition" value="" />
    <input type="hidden" name="rank" value="" />
    <table class="list">
        <col width="5%" />
        <col width="15%" />
        <col width="45%" />
        <col width="5%" />
        <col width="5%" />
        <col width="25%" />
        <tr>
            <th><!--{t string="tpl_Ranking_01"}--></th>
            <th><!--{t string="tpl_Date_01"}--></th>
            <th><!--{t string="tpl_Title_01"}--></th>
            <th class="edit"><!--{t string="tpl_Edit_01"}--></th>
            <th class="delete"><!--{t string="tpl_Remove_01"}--></th>
            <th><!--{t string="tpl_Move_01"}--></th>
        </tr>
        <!--{section name=data loop=$arrNews}-->
        <tr style="background:<!--{if $arrNews[data].news_id != $tpl_news_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;" class="center">
            <!--{assign var=db_rank value="`$arrNews[data].rank`"}-->
            <td><!--{math equation="$line_max - $db_rank + 1"}--></td>
            <td><!--{$arrNews[data].cast_news_date|date_format:"%Y/%m/%d"}--></td>
            <td class="left">
                <!--{if $arrNews[data].link_method eq 1 && $arrNews[data].news_url != ""}--><a href="<!--{$arrNews[data].news_url|h}-->" ><!--{$arrNews[data].news_title|h|nl2br}--></a>
                <!--{elseif $arrNews[data].link_method eq 1 && $arrNews[data].news_url == ""}--><!--{$arrNews[data].news_title|h|nl2br}-->
                <!--{elseif $arrNews[data].link_method eq 2 && $arrNews[data].news_url != ""}--><a href="<!--{$arrNews[data].news_url|h}-->" target="_blank" ><!--{$arrNews[data].news_title|h|nl2br}--></a>
                <!--{else}--><!--{$arrNews[data].news_title|h|nl2br}-->
                <!--{/if}-->
            </td>
            <td>
                <!--{if $arrNews[data].news_id != $tpl_news_id}-->
                <a href="#" onclick="return func_edit('<!--{$arrNews[data].news_id|h}-->');"><!--{t string="tpl_Edit_01"}--></a>
                <!--{else}-->
                <!--{t string="tpl_being edited_01"}-->
                <!--{/if}-->
            </td>
            <td><a href="#" onclick="return func_del('<!--{$arrNews[data].news_id|h}-->');"><!--{t string="tpl_Remove_01"}--></a></td>
            <td>
            <!--{if count($arrNews) != 1}-->
            <input type="text" name="pos-<!--{$arrNews[data].news_id|h}-->" size="3" class="box3" /><!--{t string="tpl_Line(s)_01"}--> <a href="?" onclick="fnFormModeSubmit('move', 'moveRankSet','news_id', '<!--{$arrNews[data].news_id|h}-->'); return false;"><!--{t string="tpl_Move_01"}--></a><br />
            <!--{/if}-->
            <!--{if $arrNews[data].rank ne $max_rank}--><a href="#" onclick="return func_rankMove('up', '<!--{$arrNews[data].news_id|h}-->', '<!--{$max_rank|h}-->');"><!--{t string="tpl_To top_01"}--></a><!--{/if}-->&nbsp;<!--{if $arrNews[data].rank ne 1}--><a href="#" onclick="return func_rankMove('down', '<!--{$arrNews[data].news_id|h}-->', '<!--{$max_rank|h}-->');"><!--{t string="tpl_To bottom_01"}--></a><!--{/if}-->
            </td>
        </tr>
        <!--{sectionelse}-->
        <tr class="center">
            <td colspan="6"><!--{t string="tpl_Currently, there is no data._01"}--></td>
        </tr>
        <!--{/section}-->
    </table>
    </form>
    <!--{* ▲一覧表示エリアここまで *}-->

</div>
