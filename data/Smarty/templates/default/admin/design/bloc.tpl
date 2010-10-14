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
  <!--{* ▼ブロック編集ここから *}-->
  <h2>ブロック編集</h2>

  <!--{if $arrBlocData.tpl_path != '' and $preview == on}-->
  <h3>プレビュー：</h3>
  <div id="design-bloc-preview">
    <!--{include file=$arrBlocData.tpl_path}-->
  </div>
  <!--{/if}-->

  <div>
    <!--{ if $arrErr.bloc_name != "" }--> <div align="center"> <span class="attention"><!--{$arrErr.bloc_name}--></span></div> <!--{/if}-->
    ブロック名：<input type="text" name="bloc_name" value="<!--{$arrBlocData.bloc_name|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.bloc_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span><br />
    <!--{ if $arrErr.filename != "" }--> <div align="center"> <span class="attention"><!--{$arrErr.filename}--></span></div> <!--{/if}-->
    ファイル名：<input type="text" name="filename" value="<!--{$arrBlocData.filename|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.filename != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />.tpl<span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
  </div>
  <div>
    <textarea name="bloc_html" rows="<!--{$text_row}-->" style="width: 100%;"><!--{$arrBlocData.tpl_data|smarty:nodefaults}--></textarea>
    <input type="hidden" name="html_area_row" value="<!--{$text_row}-->" />
  </div>
  <div class="btn">
    <button type="button" onClick="ChangeSize(this, bloc_html, 50, 13, html_area_row)"><span><!--{if $text_row > 13}-->小さくする<!--{else}-->大きくする<!--{/if}--></span></button>
  </div>
  <div class="btn">
    <button type='button' name='subm' onclick="fnFormModeSubmit('form_bloc','confirm','','');"><span>登録する</span></button>
    <button type='button' name='preview' onclick="fnFormModeSubmit('form_bloc','preview','','');"><span>プレビュー</span></button>
  </div>
  <!--{* ▲ブロック編集ここまで *}-->

  <!--{* ▼ブロック一覧ここから *}-->
  <h2>編集可能ブロック</h2>
  <table class="list center">
    <!--{foreach key=key item=item from=$arrBlocList}-->
    <tr style="background-color:<!--{if $item.bloc_id == $bloc_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;">
      <td>
        <a href="<!--{$smarty.server.PHP_SELF|escape}-->?bloc_id=<!--{$item.bloc_id}-->" ><!--{$item.bloc_name}--></a>
      </td>
      <td>
        <!--{if $item.del_flg == 0}-->
        <input type="button" value="削除" name="del<!--{$item.bloc_id}-->" onclick="fnFormModeSubmit('form_bloc','delete','bloc_id',this.name.substr(3));" />
        <input type="hidden" value="<!--{$item.bloc_id}-->" name="del_id<!--{$item.bloc_id}-->" />
        <!--{/if}-->
      </td>
    </tr>
    <!--{/foreach}-->
  </table>

  <div class="btn addnew">
    <button type='button' name='subm' onclick="location.href='http://<!--{$smarty.server.HTTP_HOST}--><!--{$smarty.server.PHP_SELF|escape}-->'"><span>ブロックを新規入力</span></button>
  </div>
  <!--{* ▲ブロック一覧ここまで *}-->

</form>  
