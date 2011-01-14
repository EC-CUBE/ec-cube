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
<form name="form_bloc" id="form_bloc" method="post" action="?" >
<input type="hidden" name="mode" value="" />
<input type="hidden" name="bloc_id" value="<!--{$bloc_id}-->" />
<input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />

    <!--{* ▼ブロック設定 *}-->

    <!--{if $arrBlocData.tpl_path != '' and $preview == on}-->
    <h3>プレビュー：</h3>
    <div id="design-bloc-preview">
        <!--{include file=$arrBlocData.tpl_path}-->
    </div>
    <!--{/if}-->

    <table>
        <tr>
            <th>ブロック名</th>
            <td>
                <input type="text" name="bloc_name" value="<!--{$arrBlocData.bloc_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.bloc_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
                <!--{ if $arrErr.bloc_name != "" }--> <div align="center"> <span class="attention"><!--{$arrErr.bloc_name}--></span></div> <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>ファイル名</th>
            <td><input type="text" name="filename" value="<!--{$arrBlocData.filename|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.filename != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />.tpl<span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
        <!--{ if $arrErr.filename != "" }--> <div align="center"> <span class="attention"><!--{$arrErr.filename}--></span></div> <!--{/if}-->
         </td>
     </tr>
     <tr>
         <td colspan="2">
             <textarea id="bloc_html" name="bloc_html" rows="<!--{$text_row}-->" style="width: 100%;"><!--{$arrBlocData.tpl_data|smarty:nodefaults}--></textarea>
             <input type="hidden" name="html_area_row" value="<!--{$text_row}-->" />
             <div>
                 <a id="resize-btn" class="btn-normal" href="javascript:;" onclick="ChangeSize('#resize-btn', '#bloc_html', 50, 13)">拡大</a>
             </div>
         </td>
     </tr>
 </table>
    <div class="btn">
        <a class="btn-action" href="javascript:;" name='subm' onclick="fnFormModeSubmit('form_bloc','confirm','','');"><span class="btn-next">登録する</span></a>
        <a class="btn-normal" href="javascript:;" name='preview' onclick="fnFormModeSubmit('form_bloc','preview','','');"><span>プレビュー</span></a>
    </div>
    <!--{* ▲ブロック設定 *}-->

    <!--{* ▼ブロック一覧 *}-->
    <h2>編集可能ブロック</h2>
    <div class="btn addnew">
        <a class="btn-normal" href="?"><span>ブロックを新規入力</span></a>
    </div>
    <table class="list">
        <tr>
            <th>名称</th><th class="edit">編集</th>
            <th class="delete">削除</th>
        </tr>
        <!--{foreach key=key item=item from=$arrBlocList}-->
        <tr style="background-color:<!--{if $item.bloc_id == $bloc_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;">
            <td><!--{$item.bloc_name}--></td>
            <td class="center">
                <a href="?bloc_id=<!--{$item.bloc_id}-->&amp;device_type_id=<!--{$device_type_id}-->" >編集</a>
            </td>
            <td class="center">
                <!--{if $item.deletable_flg == 1}-->
                    <a href="javascript:;" onclick="fnFormModeSubmit('form_bloc','delete','bloc_id',this.name.substr(3));">削除</a>
                    <input type="hidden" value="<!--{$item.bloc_id}-->" name="del_id<!--{$item.bloc_id}-->" />
                <!--{/if}-->
            </td>
        </tr>
        <!--{/foreach}-->
    </table>
    <!--{* ▲ブロック一覧 *}-->

</form>
