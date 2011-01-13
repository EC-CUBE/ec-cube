<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<link rel="stylesheet" href="<!--{$TPL_DIR}-->jquery.multiselect2side/css/jquery.multiselect2side.css" type="text/css" media="screen" />
<script type="text/javascript" src="<!--{$TPL_DIR}-->jquery.multiselect2side/js/jquery.multiselect2side.js" ></script>
<script type="text/javascript">
<!--
$().ready(function() {
    $('#output_list').multiselect2side({
        selectedPosition: 'left',
        moveOptions: true,
        labelsx: '出力項目一覧',
        labeldx: '出力可能項目一覧'
    });
    // multiselect2side の初期選択を解除
    $('.ms2side__div select').val(null);
    // [Sort] ボタンは混乱防止のため非表示
    // FIXME 選択・非選択のボタンと比べて、位置ズレしている
    $('.ms2side__div .SelSort').hide();
});
//-->
</script>

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="confirm" />
<input type="hidden" name="tpl_subno_csv" value="<!--{$tpl_subno_csv}-->" />

<div id="admin-contents" class="contents-main">
    <div class="ms2side__area">
        <span class="attention"><!--{$arrErr.output_list}--></span>
        <select multiple name="output_list[]" style="<!--{$arrErr.output_list|sfGetErrorColor}-->;" id="output_list" size="20">
            <!--{html_options options=$arrOptions selected=$arrSelected}-->
        </select>
    </div>

    <div class="btn-area">
      <ul>
        <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'confirm', '', '');"><span class="btn-next">この内容で登録する</span></a></li>
      </ul>
    </div>

</div>
</form>
