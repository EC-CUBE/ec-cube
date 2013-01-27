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

<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->jquery.multiselect2side/css/jquery.multiselect2side.css" type="text/css" media="screen" />
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->jquery.multiselect2side/js/jquery.multiselect2side.js" ></script>
<script type="text/javascript">
<!--
$().ready(function() {
    $('#output_list').multiselect2side({
        selectedPosition: 'right',
        moveOptions: true,
        labelsx: '<!--{t string="tpl_Items not for CSV output_01"}-->',
        labeldx: '<!--{t string="tpl_Items for CSV output_01"}-->',
        labelTop: '<!--{t string="tpl_Top_01"}-->',
        labelBottom: '<!--{t string="tpl_Bottom_01"}-->',
        labelUp: '<!--{t string="tpl_Up_01"}-->',
        labelDown: '<!--{t string="Down"}-->',
        labelSort: '<!--{t string="tpl_Item order_01"}-->'
    });
    // multiselect2side の初期選択を解除
    $('.ms2side__div select').val(null);
    // [Sort] ボタンは混乱防止のため非表示
    // FIXME 選択・非選択のボタンと比べて、位置ズレしている
    $('.ms2side__div .SelSort').hide();
});

function lfFormModeDefautSetSubmit(form, mode) {
    if (!window.confirm('<!--{t string="tpl_Do you want to register using default settings_01"}-->')) {
        return;
    }
    return fnSetFormSubmit(form, 'mode', mode);
}
//-->
</script>

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="confirm" />
<input type="hidden" name="tpl_subno_csv" value="<!--{$tpl_subno_csv|h}-->" />
<div id="admin-contents" class="contents-main">
    <!--{if $tpl_is_update}-->
    <span class="attention"><!--{t string="tpl_* Update successful._01"}--></span>
    <!--{/if}-->
    <span class="attention"><!--{$arrErr.tpl_subno_csv}--></span>
    <div class="ms2side__area">
        <span class="attention"><!--{$arrErr.output_list}--></span>
        <select multiple name="output_list[]" style="<!--{$arrErr.output_list|sfGetErrorColor}-->;" id="output_list" size="20">
            <!--{html_options options=$arrOptions selected=$arrSelected}-->
        </select>
    </div>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'confirm', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="lfFormModeDefautSetSubmit('form1', 'defaultset', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Return to initial settings and register_01"}--></span></a></li>
        </ul>
    </div>

</div>
</form>
