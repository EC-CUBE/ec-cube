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
    res = confirm('<!--{if $edit_mode eq "on"}--><!--{t string="tpl_172"}--><!--{else}--><!--{t string="tpl_173"}--><!--{/if}-->');
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
    res = confirm('<!--{t string="tpl_174"}-->');
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
        alert( '<!--{t string="tpl_175"}-->' );
        return false;
    } else if( ! val ) {
        alert( '<!--{t string="tpl_176"}-->' );
        return false;
    } else if( val.length > 4){
        alert( '<!--{t string="tpl_177"}-->' );
        return false;
    } else if( val.match(/[0-9]+/g) != val){
        alert( '<!--{t string="tpl_178"}-->' );
        return false;
    } else if( val == rank ){
        alert( '<!--{t string="tpl_179"}-->' );
        return false;
    } else if( val == 0 ){
        alert( '<!--{t string="tpl_180"}-->' );
        return false;
    } else if( val > max_rank ){
        alert( '<!--{t string="tpl_181"}-->' );
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
			setDate(dateText);
		},
		showButtonPanel: true,
		beforeShow: showAdditionalButton,       
		onChangeMonthYear: showAdditionalButton
		});
		
		$("#datepicker").blur( function() {
			var dateText = $(this).val();
			setDate(dateText);
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
            <th><!--{t string="tpl_024_1"}--></th>
            <td>
                <!--{if $arrErr.year || $arrErr.month || $arrErr.day}--><span class="attention"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></span><!--{/if}-->
                <input id="datepicker" type="text" value="<!--{if $arrForm.year != "" && $arrForm.month != "" && $arrForm.day != ""}--><!--{$arrForm.year|h}-->/<!--{$arrForm.month|h|string_format:'%02d'}-->/<!--{$arrForm.day|h|string_format:'%02d'}--><!--{/if}-->" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                <input type="hidden" name="year" value="<!--{$arrForm.year}-->" />
                <input type="hidden" name="month" value="<!--{$arrForm.month}-->" />
                <input type="hidden" name="day" value="<!--{$arrForm.day}-->" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_022_1"}--></th>
            <td>
                <!--{if $arrErr.news_title}--><span class="attention"><!--{$arrErr.news_title}--></span><!--{/if}-->
                <textarea name="news_title" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" <!--{if $arrErr.news_title}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}-->><!--{"\n"}--><!--{$arrForm.news_title|h}--></textarea><br />
                <span class="attention"> <!--{t string="tpl_023" T_FIELD=$smarty.const.MTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_109"}--></th>
            <td>
                <span class="attention"><!--{$arrErr.news_url}--></span>
                <input type="text" name="news_url" size="60" class="box60"    value="<!--{$arrForm.news_url|h}-->" <!--{if $arrErr.news_url}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}--> maxlength="<!--{$smarty.const.URL_LEN}-->" />
                <span class="attention"> <!--{t string="tpl_023" T_FIELD=$smarty.const.URL_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_110"}--></th>
            <td><label><input type="checkbox" name="link_method" value="2" <!--{if $arrForm.link_method eq 2}--> checked <!--{/if}--> /> <!--{t string="tpl_112"}--></label></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_111"}--></th>
            <td>
                <!--{if $arrErr.news_comment}--><span class="attention"><!--{$arrErr.news_comment}--></span><!--{/if}-->
                <textarea name="news_comment" cols="60" rows="8" wrap="soft" class="area60" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" style="background-color:<!--{if $arrErr.news_comment}--><!--{$smarty.const.ERR_COLOR|h}--><!--{/if}-->"><!--{"\n"}--><!--{$arrForm.news_comment|h}--></textarea><br />
                <span class="attention"> <!--{t string="tpl_113"}--></span>
            </td>
        </tr>
    </table>
    <!--{* ▲登録テーブルここまで *}-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="return func_regist();"><span class="btn-next"><!--{t string="tpl_021"}--></span></a></li>
        </ul>
    </div>
</form>

    <h2><!--{t string="tpl_114"}--></h2>
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
            <th><!--{t string="tpl_115"}--></th>
            <th><!--{t string="tpl_024"}--></th>
            <th><!--{t string="tpl_022"}--></th>
            <th class="edit"><!--{t string="tpl_003"}--></th>
            <th class="delete"><!--{t string="tpl_004"}--></th>
            <th><!--{t string="tpl_005"}--></th>
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
                <a href="#" onclick="return func_edit('<!--{$arrNews[data].news_id|h}-->');"><!--{t string="tpl_003"}--></a>
                <!--{else}-->
                <!--{t string="tpl_026"}-->
                <!--{/if}-->
            </td>
            <td><a href="#" onclick="return func_del('<!--{$arrNews[data].news_id|h}-->');"><!--{t string="tpl_004"}--></a></td>
            <td>
            <!--{if count($arrNews) != 1}-->
            <input type="text" name="pos-<!--{$arrNews[data].news_id|h}-->" size="3" class="box3" /><!--{t string="tpl_713"}--><a href="?" onclick="fnFormModeSubmit('move', 'moveRankSet','news_id', '<!--{$arrNews[data].news_id|h}-->'); return false;"><!--{t string="tpl_005"}--></a><br />
            <!--{/if}-->
            <!--{if $arrNews[data].rank ne $max_rank}--><a href="#" onclick="return func_rankMove('up', '<!--{$arrNews[data].news_id|h}-->', '<!--{$max_rank|h}-->');"><!--{t string="tpl_077"}--></a><!--{/if}-->　<!--{if $arrNews[data].rank ne 1}--><a href="#" onclick="return func_rankMove('down', '<!--{$arrNews[data].news_id|h}-->', '<!--{$max_rank|h}-->');"><!--{t string="tpl_078"}--></a><!--{/if}-->
            </td>
        </tr>
        <!--{sectionelse}-->
        <tr class="center">
            <td colspan="6"><!--{t string="tpl_182"}--></td>
        </tr>
        <!--{/section}-->
    </table>
    </form>
    <!--{* ▲一覧表示エリアここまで *}-->

</div>
