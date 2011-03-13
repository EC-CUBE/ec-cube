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
<script type="text/javascript">
<!--
function fnTargetSelf(){
    document.form_edit.target = "_self";
}

browser_type = 0;
if(navigator.userAgent.indexOf("MSIE") >= 0){
    browser_type = 1;
}
else if(navigator.userAgent.indexOf("Mozilla") >= 0){
    browser_type = 2;
}
//-->
</script>


<form name="form_edit" id="form_edit" method="post" action="?" >
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="page_id" value="<!--{$page_id}-->" />
<input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />

    <!--{if $arrErr.page_id_err != ""}-->
        <div class="message">
            <span class="attention"><!--{$arrErr.page_id_err}--></span>
        </div>
    <!--{/if}-->
    <table>
        <tr>
            <th>名称</th>
            <td>
                <!--{if $arrPageData.edit_flg == 2}-->
                    <!--{$arrPageData.page_name|h}--><input type="hidden" name="page_name" value="<!--{$arrPageData.page_name|h}-->" />
                <!--{else}-->
                    <input type="text" name="page_name" value="<!--{$arrPageData.page_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.page_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
                <!--{/if}-->
                <!--{if $arrErr.page_name != ""}-->
                    <div class="message">
                        <span class="attention"><!--{$arrErr.page_name}--></span>
                    </div>
                <!--{/if}-->
            </td>
        </tr>
        <tr>
        <th>URL</th>
            <td>
                <!--{if $arrPageData.edit_flg == 2}-->
                    <!--{$smarty.const.HTTP_URL|h}--><!--{$arrPageData.url|h}-->
                    <input type="hidden" name="url" value="<!--{$arrPageData.filename|h}-->" />
                <!--{else}-->
                    <!--{$smarty.const.USER_URL|h}--><input type="text" name="url" value="<!--{$arrPageData.filename|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.url != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}--> ime-mode: disabled;" size="40" class="box40" />.php<span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
                <!--{/if}-->
                <!--{if $arrErr.url != ""}-->
                    <div class="attention">
                        <span class="attention"><!--{$arrErr.url}--></span>
                    </div>
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <label for="header-chk"><input type="checkbox" name="header_chk" id="header-chk" checked="<!--{$arrPageData.header_chk}-->" />共通のヘッダーを使用する</label>&nbsp;
                <label for="footer-chk"><input type="checkbox" name="footer_chk" id="footer-chk" checked="<!--{$arrPageData.footer_chk}-->" />共通のフッターを使用する</label>
                <div>
                    <textarea id="tpl_data" class="top" name="tpl_data" rows=<!--{$text_row}--> style="width: 98%;"><!--{$arrPageData.tpl_data|h|smarty:nodefaults}--></textarea>
                    <input type="hidden" name="html_area_row" value="<!--{$text_row}-->" /><br />
                    <a id="resize-btn" class="btn-normal" href="javascript:;" onclick="ChangeSize('#resize-btn', '#tpl_data', 50, 13); return false;"><span>拡大</span></a>
                </div>
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form_edit','confirm','',''); return false;"><span class="btn-next">登録する</span></a></li>
        </ul>
    </div>

    <h2>編集可能ページ一覧</h2>
    <table class="list">
        <colgroup width="70%">
        <colgroup width="10%">
        <colgroup width="10%">
        <colgroup width="10%">
        <tr>
            <th>名称</th>
            <th>レイアウト</th>
            <th>ページ詳細</th>
            <th>削除</th>
        </tr>
        <!--{foreach key=key item=item from=$arrPageList}-->
            <tr style="<!--{if $item.page_id == $page_id}-->background-color: <!--{$smarty.const.SELECT_RGB}-->;<!--{/if}-->">
                <td>
                    <!--{$item.page_name}-->
                </td>
                <td class="center">
                    <a href="./<!--{$smarty.const.DIR_INDEX_PATH}-->?page_id=<!--{$item.page_id}-->&amp;device_type_id=<!--{$item.device_type_id}-->" >編集</a>
                </td>
                <td class="center">
                    <!--{if $item.filename|strlen >= 1}-->
                        <a href="?page_id=<!--{$item.page_id}-->&amp;device_type_id=<!--{$item.device_type_id}-->">編集</a>
                    <!--{/if}-->
                </td>
                <td class="center">
                    <!--{if $item.edit_flg == 1}-->
                        <a href="javascript:;" onclick="fnTargetSelf(); fnFormModeSubmit('form_edit','delete','page_id','<!--{$item.page_id|escape:'javascript'|h}-->'); return false;">削除</a>
                    <!--{/if}-->
                </td>
            </tr>
        <!--{/foreach}-->
    </table>
    <div class="btn addnew">
        <a class="btn-normal" href="?"><span>ページを新規入力</span></a>
    </div>
</form>
