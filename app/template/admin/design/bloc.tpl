<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="" />
    <input type="hidden" name="bloc_id" value="<!--{$bloc_id|h}-->" />
    <input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />

    <div id="design" class="contents-main">
        <!--{if $arrErr.err != ""}-->
            <div class="message">
                <span class="attention"><!--{$arrErr.err}--></span>
            </div>
        <!--{/if}-->

        <!--{* ▼ブロック設定 *}-->
        <table>
            <tr>
                <th>ブロック名</th>
                <td>
                    <!--{assign var=key value="bloc_name"}-->
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" />
                    <span class="attention"> (上限<!--{$arrForm[$key].length}-->文字)</span>
                    <!--{if $arrErr[$key] != ""}--> <div class="attention"><!--{$arrErr[$key]}--></div> <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>ファイル名</th>
                <td>
                    <!--{assign var=key value="filename"}-->
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" />.tpl
                    <span class="attention"> (上限<!--{$arrForm[$key].length}-->文字)</span>
                    <!--{if $arrErr[$key] != ""}--> <div class="attention"><!--{$arrErr[$key]}--></div> <!--{/if}-->
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <!--{assign var=key value="bloc_html"}-->
                    <textarea class="top" id="<!--{$key}-->" name="<!--{$key}-->" rows="<!--{$text_row}-->" style="width: 99%;"><!--{"\n"}--><!--{$arrForm[$key].value|smarty:nodefaults|h}--></textarea>
                    <input type="hidden" name="html_area_row" value="<!--{$text_row}-->" />
                    <div>
                        <a id="resize-btn" class="btn-normal" href="javascript:;" onclick="eccube.toggleRows('#resize-btn', '#bloc_html', 50, 13); return false;">拡大</a>
                    </div>
                </td>
            </tr>
        </table>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" name='subm' onclick="eccube.fnFormModeSubmit('form_bloc','confirm','',''); return false;"><span class="btn-next">登録する</span></a></li>
            </ul>
        </div>
        <!--{* ▲ブロック設定 *}-->

        <!--{* ▼ブロック一覧 *}-->
        <h2>編集可能ブロック</h2>
        <div class="btn addnew">
            <a class="btn-normal" href="?device_type_id=<!--{$device_type_id|h}-->"><span>ブロックを新規入力</span></a>
        </div>
        <table class="list">
            <tr>
                <th>名称</th>
                <th class="edit">編集</th>
                <th class="delete">削除</th>
            </tr>
            <!--{foreach key=key item=item from=$arrBlocList}-->
                <tr style="background-color:<!--{if $item.bloc_id == $bloc_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;">
                    <td><!--{$item.bloc_name}--></td>
                    <td class="center">
                        <a href="?bloc_id=<!--{$item.bloc_id|h}-->&amp;device_type_id=<!--{$device_type_id|h}-->" >編集</a>
                    </td>
                    <td class="center">
                        <!--{if $item.deletable_flg == 1}-->
                            <a href="javascript:;" onclick="eccube.fnFormModeSubmit('form_bloc','delete','bloc_id',<!--{$item.bloc_id|h}-->);">削除</a>
                            <input type="hidden" value="<!--{$item.bloc_id|h}-->" name="del_id<!--{$item.bloc_id|h}-->" />
                        <!--{/if}-->
                    </td>
                </tr>
            <!--{/foreach}-->
        </table>
        <!--{* ▲ブロック一覧 *}-->
    </div>
</form>
